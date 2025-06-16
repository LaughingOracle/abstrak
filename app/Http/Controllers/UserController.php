<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbstractPaper;

class UserController extends Controller
{
    public function listing()
    {
        $userId = auth()->id();
        // You can validate the role if needed here
        $abstracts = AbstractPaper::where('abstract_account_id', $userId)->where('event_id',auth()->user()->event_id)->get();

        return view('usermenu', compact('abstracts'));
    }
}
