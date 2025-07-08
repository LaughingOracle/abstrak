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
        'description' => 'required|string',
        'topic' => 'required|string',
        'presentation_type' => 'required|in:poster,oral',
        'zip_file' => 'file|mimes:zip|max:1073741824', // max:1073741824 = 1TB  <-- kebanyakan? skripsi saya sekitar 30 giga, ga tau kalo kedokteran berapa. jangan hapus limit, ini pencegahan DOS (jangan samakan dengan DDOS btw)
        'pdf' => 'required|file|mimes:pdf',
        'presenter_name' => 'required|string|max:255',

        //dynamic validation
        'author_name' => 'required|array',
        'author_name.*' => 'required|string|max:255',

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
            
            $pdf = $request->file('pdf');
            // Get file extension (e.g., pdf)
            $extension = $pdf->getClientOriginalExtension();

            // Save PDF to private disk (relative to storage/app/private)
            $pdfPathBak = $pdf->storeAs("pdf/$submission->id", $submission->id) . '.' . $extension;

            // save to public disk (storage/app/public)
            $pdfPath = $pdf->storeAs(
                "pdf/$submission->id", // subfolder inside /storage/app/public
                "$submission->id" . '.' . $extension,
                'public' // this refers to the disk
            );

            if ($request->hasFile('zip_file')) {
                $file = $request->file('zip_file');
                $title = $request->input('title');
                $filename = $file->getClientOriginalName();

                // Store the ZIP file
                $storedPath = $file->storeAs("zips/{$submission->id}", $submission->id);

                // Define extraction path
                $extractPath = storage_path('app/public/extracted/' . $submission->id);

                // Create directory if it doesn't exist
                if (!file_exists($extractPath)) {
                    mkdir($extractPath, 0777, true);
                }

                // Extract the ZIP
                $zip = new ZipArchive;
                if ($zip->open(storage_path("app/private/{$storedPath}")) === true) {
                    $zip->extractTo($extractPath);
                    $zip->close();
                } else {
                    // Handle failed zip open
                    return back()->withErrors(['zip_file' => 'Failed to open the zip file.']);
                }
            }


            
            /////////////////////////////////////////////////////////////////////////////////////////////////



            // Insert multiple authors
            foreach ($validated['author_name'] as $index => $name) {
                $author = Author::create([
                    'name' => $name,
                    'affiliation' => $validated['author_affiliation'][$index],
                ]);
                $submission->author()->attach($author->id);
            }
        });

        // getting presenter and user email
        $emails = [
            auth()->user()->email
        ];
        
        //sending mails
        foreach ($emails as $email){
            Mail::raw('Abstrak anda akan segera di-review', function ($message) use ($email) {
                $message->to($email)->subject('Test Email');
            });
        }
        return redirect()->route('usermenu', ['event' => $eventName])->with('success', 'File uploaded successfully.');
    }

    public function uploadPresentation(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'file' => 'required',
        ]);

        $id = $request->id;

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        $filename = "$id.$extension";
        $filePathBak = $file->storeAs("presentation/$id", $filename); // private disk by default


        // save to public disk (storage/app/public)
        $filePathPublic = $file->storeAs("presentation/$id", $filename, 'public');

        return back();
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

    public function viewAbstract(Request $request, $id)
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

        $storage = Storage::disk('public')->allFiles("/pdf/{$abstract->id}");

        
        $data = array(
            'event' => $eventName,
            'abstract' => $abstract,
            'files' => $storage,
        );


        return view('displayAbstract')->with($data);
    }


    public function viewPresentation($id)
    {

        $abstract = AbstractPaper::find($id);
        $extensions = 'pdf';
        if($abstract->presentation_type == 'poster'){
            $extensions = 'png';
        }

        $path = "presentation/$id/$id.$extensions";
        if (Storage::disk('public')->exists($path)) {
            return redirect(Storage::url($path));
        }

        // File not found
        return abort(404, 'File not found');
    }
}