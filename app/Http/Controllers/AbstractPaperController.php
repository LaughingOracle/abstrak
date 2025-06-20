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

        $abstractPaper = AbstractPaper::findOrFail($id);

        $eventName = $abstractPaper->event;
        
        DB::transaction(function () use ($validated, $request, $abstractPaper) {

            // Update Presenter
            $presenter = $abstractPaper->presenter;
            $presenter->update([
                'name' => $validated['presenter_name'],
                'email' => $validated['presenter_email'],
            ]);

            // Handle new file upload
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
                        'email' => $validated['author_email'][$i],
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

        // Redirect back with a success message
        return redirect()->route('usermenu', ['event' => $eventName]);
    }
}
