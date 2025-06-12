<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\AbstractPaper;

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

        // You might use a hidden input or button name to determine which was clicked
        if ($request->has('lulus')) {
            $paper->status = 'lulus';
        } elseif ($request->has('tidak_lulus')) {
            $paper->status = 'tidak lulus';
        }

        $paper->save();

        return redirect()->back();
    }
}
