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
}
