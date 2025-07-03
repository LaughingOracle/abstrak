<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbstractPaper;
use Illuminate\Support\Facades\Storage;
use App\Models\Presenter;
use App\Models\Author;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use ZipArchive;


class AbstractPaperController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $abstract = AbstractPaper::findOrFail($id);

        return view('/update', compact('abstract'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'topic' => 'required|string',
        'presentation_type' => 'required|in:poster,oral',
        'pdf' => 'required|file|mimes:pdf',
        'zip_file' => 'file|mimes:zip|max:1073741824', // max:1073741824 = 1TB  <-- kebanyakan? skripsi saya sekitar 30 giga, ga tau kalo kedokteran berapa. jangan hapus limit, ini pencegahan DOS (jangan samakan dengan DDOS btw)
        'presenter_name' => 'required|string|max:255',

        //dynamic validation
        'author_name' => 'required|array',
        'author_name.*' => 'required|string|max:255',

        'author_affiliation' => 'required|array',
        'author_affiliation.*' => 'required|string|max:255',
        ]);

        $abstractPaper = AbstractPaper::findOrFail($id);

        $eventName = $abstractPaper->event;
        
        DB::transaction(function () use ($validated, $request, $abstractPaper) {

            // Update Presenter
            $presenter = $abstractPaper->presenter;
            $presenter->update([
                'name' => $validated['presenter_name']
            ]);

            // Handle new file upload

            $pdf = $request->file('pdf');
            // Get file extension (e.g., pdf)
            $extension = $pdf->getClientOriginalExtension();

            // Save PDF to private disk (relative to storage/app/private)
            $pdfPathBak = $pdf->storeAs("pdf/$abstractPaper->id", $abstractPaper->id) . '.' . $extension;

            // save to public disk (storage/app/public)
            $pdfPath = $pdf->storeAs(
                "pdf/$abstractPaper->id", // subfolder inside /storage/app/public
                "$abstractPaper->id" . '.' . $extension,
                'public' // this refers to the disk
            );

            if ($request->hasFile('zip_file')) {
                $zip = new ZipArchive;
                $file = $request->file('zip_file');
                $title = $request->input('title');
                $filename = $file->getClientOriginalName();

                $eventName = DB::table('events')
                    ->where('id', auth()->user()->event_id)
                    ->value('event_name');

                $extractPath = storage_path('app/public/extracted/'. $abstractPaper->id);

                File::deleteDirectory(storage_path('app/private/zips/'. $abstractPaper->id));
                File::deleteDirectory(storage_path('app/public/extracted/'. $abstractPaper->id));
                //extraction
                $storedPath = $file->storeAs("zips/$abstractPaper->id", $abstractPaper->id);
                $zip->open(storage_path("app/private/{$storedPath}"));
                $zip->extractTo($extractPath);
                $zip->close();
            }

            // Update Abstract Paper
            $abstractPaper->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'topic' => $validated['topic'],
                'presentation_type' => $validated['presentation_type'],
            ]);

            // Sync authors
            $authorIds = [];
            for ($i = 0; $i < count($validated['author_name']); $i++) {
                $author = Author::firstOrCreate(
                    [
                        'name' => $validated['author_name'][$i],
                    ],
                    [
                        'name' => $validated['author_name'][$i],
                        'affiliation' => $validated['author_affiliation'][$i],
                    ]
                );

                // Update name/affiliation in case email matches but data changed
                $author->update([
                    'name' => $validated['author_name'][$i],
                    'affiliation' => $validated['author_affiliation'][$i],
                ]);

                $authorIds[] = $author->id;
            }

            // Detach old and attach new authors
            $abstractPaper->author()->sync($authorIds);
        });
        return redirect()->route('usermenu', ['event' => $eventName]);
    }
     
    public function destroy(string $id)
    {
        // Find the abstract by ID
        $abstract = AbstractPaper::findOrFail($id);

        $eventName = $abstract->event;

        $presenter = Presenter::findOrFail($abstract->presenter_id);
        
        $author = $abstract->author()->pluck('author_id');

        // Optional: Make sure the authenticated user owns the abstract
        if ($abstract->abstract_account_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the abstract
        $abstract->delete();

        $presenter->delete();

        Author::destroy($author);

        File::deleteDirectory(storage_path('app/private/zips/'. $abstract->id));
        File::deleteDirectory(storage_path('app/public/extracted/' . $abstract->id));
        File::deleteDirectory(storage_path('app/private/pdf/'. $abstract->id));
        File::deleteDirectory(storage_path('app/public/pdf/' . $abstract->id));

        // Redirect back with a success message
        return redirect()->route('usermenu', ['event' => $eventName]);
    }
}
