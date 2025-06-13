<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\AbstractPaper;
use Illuminate\Support\Facades\Mail;
use App\Models\Author;
use App\Models\Presenter;
use App\Models\AbstractAccount;

class ClientController extends Controller
{
    //
    public function listing(Request $request, $name){
        $abstractPapers = AbstractPaper::where('reviewer', $name)->get();

        return view('abstractReview')->with('abstractPapers', $abstractPapers);
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

    public function review(Request $request, $id)
    {
        $paper = AbstractPaper::findOrFail($id);

        if ($request->has('lulus')) {
            $paper->status = 'lulus';
            $messageContent = 'abstrak anda lulus';
        } elseif ($request->has('tidak_lulus')) {
            $paper->status = 'tidak lulus';
            $messageContent = 'abstrak anda tidak lulus';
        }

        $paper->save();

        // getting presenter email
        $emails = [
            $paper->presenter_email
        ];

        //getting abstract account
        $abstract_account = AbstractAccount::findOrFail($paper->abstract_account_id);
        $emails[] = $abstract_account->email;

        // getting author email
        $authorsEmail = $paper->author()->get()->pluck('email');
        foreach ($authorsEmail as $authorEmail){
            $emails[] = $authorEmail;
        }

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

        return redirect()->back();
    }
}
