<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ZipUploadController extends Controller
{
    public function showForm()
    {
        return view('upload');
    }


    //todo handleUpload:
    public function handleUpload(Request $request)
    {
        $request->validate([
            'zip_file' => 'required|file|mimes:zip|max:10240', // Max 10MB
            'title' => 'required|string|max:255'
        ]);

        $file = $request->file('zip_file');
        $title = $request->input('title');
        $filename = $file->getClientOriginalName();
        $storedPath = $file->storeAs("zips/$title", $filename);

        $extractPath = storage_path('app/public/extracted/' . $title);

        if (!file_exists($extractPath)) {
            mkdir($extractPath, 0777, true);
        }

        $zip = new ZipArchive;
        if ($zip->open(storage_path("app/private/{$storedPath}")) === true) {
            $zip->extractTo($extractPath);
            $zip->close();
            return back()->with('success', 'File extracted successfully.');
        } else {
            return back()->with('error', 'Failed to open zip file.');
        }

    }

    public function viewFile(Request $request, $title)
    {
        $request->merge(['title' => $title]);
        $request->validate([
            'title' => 'required|string|max:255'
        ]); // receive 'name' input from request
        $title = $request->input('title');

        //$storage = Storage::allFiles($fullPath);
        $storage = Storage::disk('public')->allFiles("extracted/{$title}");
        
        $data = array(
            'title' => $title,
            'files' => $storage,
        );


        return view('display')->with($data);
    }
}
