<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\AbstractPaper;
use Illuminate\Support\Facades\Mail;
use App\Models\Author;
use App\Models\Event;
use App\Models\Presenter;
use App\Models\AbstractAccount;
use App\Models\EventForm;
use App\Models\FormInput;

class ClientController extends Controller
{
    //
    public function listing(Request $request, $event, $name){
        $abstractPapers = AbstractPaper::where('event', $event)->where('reviewer', $name)->get();

        return view('abstractReview')->with('abstractPapers', $abstractPapers);
    }

    public function scoringList(Request $request, $event, $name){
        $abstractPapers = AbstractPaper::where('event', $event)->where('jury', $name)->get();
        $abstractIds = $abstractPapers->pluck('id');

        $scoreBool = FormInput::whereIn('abstract_paper_id', $abstractIds)
            ->pluck('abstract_paper_id') // now it's just [3, 5, 7]
            ->unique();
        return view('abstractReview2')
            ->with('abstractPapers', $abstractPapers)
            ->with('scoreBool', $scoreBool);
    }
    
    public function scoreMenu(Request $request, $id){
        $abstractPaper = AbstractPaper::find($id);
        $forms = EventForm::where('event_id', $abstractPaper->event_id)->where('type', 'abstract')->get();

        return view('scoring')->with(['forms' => $forms, 'eventId' => $abstractPaper->event_id, 'abstractPaperId' => $abstractPaper->id]);
    }

    public function scoreMenu2(Request $request, $id){
        $abstractPaper = AbstractPaper::find($id);
        $forms = EventForm::where('event_id', $abstractPaper->event_id)->where('type', $abstractPaper->presentation_type)->get();

        return view('scoring2')->with(['forms' => $forms, 'eventId' => $abstractPaper->event_id, 'abstractPaperId' => $abstractPaper->id]);
    }

    public function revise(Request $request, $id)
    {
        $paper = AbstractPaper::findOrFail($id);
        if($paper->presentation_type === 'oral'){
            $paper->presentation_type = 'poster';
        }else{
            $paper->presentation_type = 'oral';
        }
        $paper->save();

        return redirect()->back();
    }

    public function review($id, $status)
    {
        $paper = AbstractPaper::findOrFail($id);

        $paper->status = $status;
        $messageContent = 'abstrak anda: ' . $status;

        $paper->save();

        //getting abstract account
        $abstract_account = AbstractAccount::findOrFail($paper->abstract_account_id);
        $emails[] = $abstract_account->email;

        // eliminate duplicates and null/empty emails
        $emails = array_filter(array_unique($emails), function ($email) {
            return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        //sending mails
        foreach ($emails as $email){
            Mail::raw($messageContent, function ($message) use ($email) {
                $message->to($email)->subject('Test Email');
            });
        }
    }

    public function score2(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'abstract_paper_id' => 'required|exists:abstract_papers,id',
            'forms' => 'required|array',
        ]);

        $eventId = $request->input('event_id');
        $abstractPaperId = $request->input('abstract_paper_id');
        $formGroups = $request->input('forms');

        FormInput::where('abstract_paper_id', $abstractPaperId)->delete();


        foreach ($formGroups as $eventFormId => $fields) {
            foreach ($fields as $value) {
                FormInput::create([
                    'event_form_id' => $eventFormId,
                    'abstract_paper_id' => $abstractPaperId,
                    'value' => (string) $value,
                ]);
            }
        }
        $abstract = AbstractPaper::find($abstractPaperId);
        return redirect()->route('scoringList', [
            'event' => $abstract->event,
            'name' => $abstract->jury
        ]);
    }

    public function score(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'abstract_paper_id' => 'required|exists:abstract_papers,id',
            'status' => 'required',
            'forms' => 'array',
        ]);

        $eventId = $request->input('event_id');
        $abstractPaperId = $request->input('abstract_paper_id');
        $formGroups = $request->input('forms');

        if($formGroups != null){
            foreach ($formGroups as $eventFormId => $fields) {
                foreach ($fields as $value) {
                    FormInput::create([
                        'event_form_id' => $eventFormId,
                        'abstract_paper_id' => $abstractPaperId,
                        'value' => (string) $value,
                    ]);
                }
            }
        }

        $abstract = AbstractPaper::find($abstractPaperId);
        $this->review($abstractPaperId, $request->input('status'));
        return redirect()->route('listing', [
            'event' => $abstract->event,
            'name' => $abstract->reviewer
        ]);
    }

}
