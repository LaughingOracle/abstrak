<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\AbstractPaper;
use App\Models\Author;
use App\Models\Presenter;
use App\Models\Event;
use Illuminate\Support\Facades\Mail;

class ZipUploadController extends Controller
{
    public function showForm()
    {
        $topics = DB::table('topics')
        ->where('event_id', auth()->user()->event_id)
        ->pluck('topic');
        return view('upload', compact('topics'));
    }


    //todo handleUpload:
    public function handleUpload(Request $request)
    {
        $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:2048',
        'topic' => 'required|string',
        'presentation_type' => 'required|in:poster,oral',
        'zip_file' => 'required|file|mimes:zip|max:1073741824', // max:1073741824 = 1TB  <-- kebanyakan? skripsi saya sekitar 30 giga, ga tau kalo kedokteran berapa. jangan hapus limit, ini pencegahan DOS (jangan samakan dengan DDOS btw)
        'presenter_name' => 'required|string|max:255',
        'presenter_email' => 'required|email|max:255',

        //dynamic validation
        'author_name' => 'required|array',
        'author_name.*' => 'required|string|max:255',

        'author_email' => 'required|array',
        'author_email.*' => 'required|email|max:255',

        'author_affiliation' => 'required|array',
        'author_affiliation.*' => 'required|string|max:255',
        ]);

        $eventName = DB::table('events')
            ->where('id', auth()->user()->event_id)
            ->value('event_name');

        DB::transaction(function () use ($request, $validated, $eventName) {

            // Insert into `presenter` table
            $presenter = Presenter::create([
                'name' => $validated['presenter_name'],
                'email' => $validated['presenter_email'],
            ]);



            // Insert into `AbstractPaper` table
            $submission = AbstractPaper::create([
                'event_id' => auth()->user()->event_id,
                'event' => $eventName,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'topic' => $validated['topic'],
                'presentation_type' => $validated['presentation_type'],
                'abstract_account_id' => auth()->id(),
                'presenter_id' => $presenter -> id,
            ]);


            ///////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            
            
            $file = $request->file('zip_file');
            $title = $request->input('title');
            $filename = $file->getClientOriginalName();
            $storedPath = $file->storeAs("zips/$submission->id", $submission->id);

            $extractPath = storage_path('app/public/extracted/'. $submission->id);

            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0777, true);
            }

            $zip = new ZipArchive;
            $zip->open(storage_path("app/private/{$storedPath}"));
            $zip->extractTo($extractPath);
            $zip->close();


            
            /////////////////////////////////////////////////////////////////////////////////////////////////



            // Insert multiple authors
            foreach ($validated['author_name'] as $index => $name) {
                $author = Author::create([
                    'name' => $name,
                    'email' => $validated['author_email'][$index],
                    'affiliation' => $validated['author_affiliation'][$index],
                ]);
                $submission->author()->attach($author->id);
            }
        });

        // getting presenter and user email
        $emails = [
            $validated['presenter_email'],
            auth()->user()->email
        ];

        // getting authoremail
        foreach ($validated['author_email'] as $authorEmail){
            $emails[] = $authorEmail;
        }

        // eliminate duplicates and null/empty emails
        $emails = array_filter(array_unique($emails), function ($email) {
            return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        });
        
        //sending mails
        foreach ($emails as $email){
            Mail::raw('Abstrak anda akan segera di-review', function ($message) use ($email) {
                $message->to($email)->subject('Test Email');
            });
        }
        return redirect()->route('usermenu', ['event' => $eventName])->with('success', 'File uploaded successfully.');
    }

    public function viewFile(Request $request, $id)
    {
        $request->merge([ 'id' => $id]);
        $request->validate([
            'id' => 'required'
        ]); // receive 'id' input from request
        $id = $request->input('id');

        $abstract = AbstractPaper::findOrFail($id);

        $eventName = DB::table('events')
                ->where('id', $abstract->event_id)
                ->value('event_name');

        $storage = Storage::disk('public')->allFiles("/extracted/{$abstract->id}");

        
        $data = array(
            'event' => $eventName,
            'abstract' => $abstract,
            'files' => $storage,
        );


        return view('display')->with($data);
    }
}