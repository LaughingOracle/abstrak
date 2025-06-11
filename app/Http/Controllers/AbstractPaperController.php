<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbstractPaper;
use App\Models\Presenter;
use App\Models\Author;
use Illuminate\Support\Facades\File;


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
        //needs some work
        $abstract = AbstractPaper::findOrFail($id);
        
    }
     
    public function destroy(string $id)
    {
        // Find the abstract by ID
        $abstract = AbstractPaper::findOrFail($id);

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

        File::deleteDirectory(storage_path('app/private/zips/'. $abstract->title));

        File::deleteDirectory(storage_path('app/public/extracted/'. $abstract->title));

        // Redirect back with a success message
        return redirect()->route('usermenu');
    }
}
