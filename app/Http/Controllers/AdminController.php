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
            'label' => 'required'
        ]);

        $eventModels = Event::where('event_name',$request->event)->first();

        EventForm::create([
            'event_id' => $eventModels->id,
            'html' => $request->input('html'),
            'type' => $request->input('type'),
            'label' => $request->input('label'),
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
            ]);


            if(! Event::where('event_name',$request->event)->first() ){
            Event::create(['event_name' => $request->event]);

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
                    'logistic' => 'required|string|max:255',
                    'selected_ids' => 'required|array',
                ]);
                    AbstractPaper::whereIn('id', $request->selected_ids)
                    ->update(['logistic' => $request->logistic]);

                // Handle export logic
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

        $eventModels = Event::where('event_name',$event)->first();
        $eventId = $eventModels->id;
        // Get all event forms for a specific event ID and type (abstract/poster/oral)
        $eventForms = EventForm::where('event_id', $eventId)->where('type', $type)->get();

        // Ensure there are event forms, else return an empty result or error
        if ($eventForms->isEmpty()) {
            return []; // or handle no forms scenario
        }

        // Get all form inputs grouped by event_form_id and type (abstract/poster/oral)
        $formInputs = FormInput::whereIn('event_form_id', $eventForms->pluck('id'))
            ->whereHas('abstractPaper', function ($query) use ($type) {
                if ($type == 'abstract') {
                    $query->where('status', 'dalam review');
                } else {
                    $query->where('status', 'lulus')->where('presentation_type', $type);
                }
            })->get();

        // Group form inputs by abstract_id
        $groupedFormInputs = $formInputs->groupBy('abstract_paper_id');

        // Prepare the report data
        $reportData = [];

        // Loop over each abstract_id (e.g., each student/test)
        foreach ($groupedFormInputs as $abstractId => $inputs) {
            $reportData[$abstractId] = [];

            $inputsByFormId = $inputs->keyBy('event_form_id');
            // For each form (scoring category), find the corresponding value
            foreach ($eventForms as $eventForm) {
                $input = $inputsByFormId->get($eventForm->id);
                $reportData[$abstractId][$eventForm->label] = $input ? $input->value : null;
            }
        }

        return view('showReport', compact('eventForms', 'reportData'));
    }

}
