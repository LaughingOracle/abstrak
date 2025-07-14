<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbstractPaper;
use Illuminate\Support\Facades\Auth;
use App\Models\Topic;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use App\Models\EventForm;
use App\Models\FormInput;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class AdminController extends Controller
{
    //
    public function index(Request $request)
    {

        $user = Auth::user();
        if ($user && $user->email === 'admin@gmail.com') {
            $query = AbstractPaper::query();
            $topicQuery = Topic::query();

            $eventLists = Event::select('event_name')->distinct()->pluck('event_name');

            if ($request->has('topic') && $request->topic) {
                $query->where('topic', $request->topic);
                $eventList2 = DB::table('topics')
                    ->join('events', 'topics.event_id', '=', 'events.id')
                    ->where('topic', $request->topic)
                    ->select('event_name')
                    ->distinct()
                    ->pluck('event_name');

                $eventLists = $eventList2;

            }
            if ($request->has('event') && $request->event) {
                $eventModels = Event::where('event_name',$request->event)->first();
                $query->where('event_id', $eventModels->id);
                $topicQuery->where('event_id', $eventModels->id);   
            }

            if ($request->has('presentation_type') && $request->presentation_type) {
                $query->where('presentation_type', $request->presentation_type);
            }

            if ($request->has('logistic') && $request->logistic) {
                $query->where('logistic', 'like' ,"%$request->logistic%");

            }

            if ($request->has('sublogistic') && $request->sublogistic) {
                $query->where('logistic', 'like' ,"%$request->sublogistic%");

            }

            if ($request->has('status') && $request->status) {
                $query->where('status',$request->status);

            }


            $topics = $topicQuery->select('topic')->distinct()->pluck('topic');
            
            $abstractPapers = $query->get();
            
            $uniqueReviewers = $query->select('reviewer', 'event')->distinct()->get();

            $uniqueJury = $query->select('jury', 'event')->distinct()->get();

            return view('/dashboard', compact('abstractPapers', 'uniqueReviewers', 'uniqueJury', 'topics', 'eventLists'));
        }
        return redirect()->route('custom.login', ['event' => 'admin_event']);
    }

    public function formMenu(Request $request)
    {
        $request->validate([
            'event' => 'required|string|max:255',
            'type' => 'required'
        ]);
        
        $user = Auth::user();
        if ($user && $user->email === 'admin@gmail.com') {
            $eventModels = Event::where('event_name',$request->event)->first();

            $forms = EventForm::where('event_id', $eventModels->id)->where('type', $request->type)->get();

            return view('formMenu', compact('forms'));
        }
        return redirect()->route('custom.login', ['event' => 'admin_event', 'type' => $request->type]);
    }

    public function formInsert(Request $request)
    {
        $request->validate([
            'html' => 'required|string|max:65535',
            'event' => 'required|string|max:255',
            'type' => 'required',
            'label' => 'required',
            'score_config' => 'required'
        ]);

        $eventModels = Event::where('event_name',$request->event)->first();

        EventForm::create([
            'event_id' => $eventModels->id,
            'html' => $request->input('html'),
            'type' => $request->input('type'),
            'label' => $request->input('label'),
            'score_config' => $request->input('score_config'),
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteForm($id)
    {
        $form = EventForm::find($id);
        if (!$form) {
            return response()->json(['success' => false, 'message' => 'Form not found.'], 404);
        }

        $form->delete();
        return redirect()->back();
    }

    public function assignEvent(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->email === 'admin@gmail.com') {
            $request->validate([
                'event' => 'required|string|max:255',
                'deadline' => 'required'
            ]);


            if(! Event::where('event_name',$request->event)->first() ){
                Event::create(['event_name' => $request->event, 'deadline' => $request->deadline]);

                return redirect()->route('dashboard')->with('success', 'event assigned successfully.');                
            }
            return redirect()->route('dashboard')->with('error', 'duplicate detected.');
        }else{
            return redirect()->route('custom.login', ['event' => 'admin_event']);
        }
    }

    public function assignTopic(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->email === 'admin@gmail.com') {
            $request->validate([
                'event' => 'required|string|max:255',
                'topic' => 'required|string|max:255',
            ]);

            $eventModels = Event::where('event_name',$request->event)->first();


            if(! Topic::where('topic',$request->topic)->where('event_id', $eventModels->id)->first() ){
                Topic::create([
                    'event_id' => $eventModels->id,
                    'topic' => $request->topic,
                ]);
                return redirect()->route('dashboard')->with('success', 'topic assigned successfully.');
            }
            return redirect()->route('dashboard')->with('error', 'duplicate detected.');
        } else{
            return redirect()->route('custom.login', ['event' => 'admin_event']);
        }
    }

    public function assignReviewer(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->email === 'admin@gmail.com') {
            if ($request->input('action') === 'assignment') {
                $request->validate([
                    'reviewer' => 'required|string|max:255',
                    'selected_ids' => 'required|array',
                    'stage' => 'required',
                ]);
                if ($request->input('stage') == '1') {
                    AbstractPaper::whereIn('id', $request->selected_ids)
                    ->update(['reviewer' => $request->reviewer]);
                }else{
                    AbstractPaper::whereIn('id', $request->selected_ids)
                    ->update(['jury' => $request->reviewer]);
                }
            } 
            elseif ($request->input('action') === 'logistic') {
                $request->validate([
                    'logisticform' => 'string|max:255',
                    'sublogisticform' => 'string|max:255',
                    'selected_ids' => 'required|array',
                ]);
                    AbstractPaper::whereIn('id', $request->selected_ids)
                    ->update(['logistic' => $request->logisticform . ' ' . $request->sublogisticform]);

                // Handle export logic
            }
            elseif($request->input('action') === 'downloads'){
                $request->validate([
                    'selected_ids' => 'required|array',
                    'stage2'=> 'required'
                ]);
                return $this->downloadFiles($request->selected_ids, $request->stage2);
            }
            return redirect()->route('dashboard')->with('success', 'Logistic assigned successfully.');
        } else{
            return redirect()->route('custom.login', ['event' => 'admin_event']);
        }
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'event' => 'required',
            'type' => 'required'
        ]);

        $type = $request->type;
        $event = $request->event;
        $eventModel = Event::where('event_name', $event)->firstOrFail();
        $eventId = $eventModel->id;

        $eventForms = EventForm::where('event_id', $eventId)->where('type', $type)->get();
        if ($eventForms->isEmpty()) return [];


        $abstracts = AbstractPaper::where('event_id', $eventId)
            ->where('status', 'passed')->where('presentation_type', $type)
            ->get();

        if($type == 'abstract'){
            $abstracts = AbstractPaper::where('event_id', $eventId)->get();
        }

        $formInputs = FormInput::whereIn('event_form_id', $eventForms->pluck('id'))
            ->whereIn('abstract_paper_id', $abstracts->pluck('id'))
            ->get()
            ->groupBy('abstract_paper_id');

        $reportData = [];

        foreach ($abstracts as $abstract) {
            $abstractId = $abstract->id;
            $inputSet = $formInputs->get($abstractId, collect());
            $inputsByFormId = $inputSet->keyBy('event_form_id');

            $row = ['_total' => 0];

            foreach ($eventForms as $form) {
                $input = $inputsByFormId->get($form->id);
                $value = $input ? $input->value : null;

                $score = 0;
                if ($value !== null && $form->score_config) {
                    $config = json_decode($form->score_config, true);
                    $decoded = json_decode($value, true) ?? $value;

                    if (is_array($decoded)) {
                        $score = collect($decoded)->sum(fn($v) => $config[$v] ?? 0);
                    } else {
                        $score = $config[$decoded] ?? 0;
                    }
                    
                }

                $row[$form->label] = $score;
                $row['_total'] += $score;
            }
            $reportData[$abstractId] = $row;
        }

        return view('showReport', compact('eventForms', 'reportData'));
    }


    public function downloadFiles($ids, $stage2) 
    {

        if (!$ids || !is_array($ids)) {
            return back()->with('error', 'No file IDs provided.');
        }

        $documents = AbstractPaper::whereIn('id', $ids)->get();

        if ($documents->isEmpty()) {
            return back()->with('error', 'No documents found for given IDs.');
        }

        $zipFileName = 'documents_' . now()->timestamp . '.zip';
        $zipFilePath = storage_path("app/private/tmp/{$zipFileName}");

        $parrent = 'presentation';
        if($stage2 == '1'){
            $parrent = 'pdf';
        }

        // Make sure tmp directory exists
        Storage::makeDirectory('tmp');

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($documents as $doc) {
                $ext = 'pdf';
                if($parrent == 'presentation' && $doc->presentation_type == 'poster'){
                    $ext = 'png';
                }
                $filePath = "{$parrent}/{$doc->id}/{$doc->id}.{$ext}";

                if (Storage::disk('public')->exists($filePath)) {
                    $contents = Storage::disk('public')->get($filePath);

                    // Decide how to rename: could be based on title, id, etc.
                    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                    $renamed = "{$doc->title}.pdf"; // Customize this

                    $zip->addFromString($renamed, $contents);
                }
            }

            $zip->close();
        } else {
            return back()->with('error', 'Could not create ZIP file.');
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}
