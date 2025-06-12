<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\AbstractPaper;

class AdminController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->email === 'admin@gmail.com') {
            $query = AbstractPaper::query();

            if ($request->has('topic') && $request->topic) {
                $query->where('topic', $request->topic);
            }

            if ($request->has('presentation_type') && $request->presentation_type) {
                $query->where('presentation_type', $request->presentation_type);
            }

            $uniqueReviewers = AbstractPaper::select('reviewer')->distinct()->pluck('reviewer');

            $abstractPapers = $query->get();

            return view('/dashboard', compact('abstractPapers', 'uniqueReviewers'));
        }

        Auth::logout();
        return redirect('usermenu')->with('error', 'Access denied.');
    }

    public function assignReviewer(Request $request)
    {
        $request->validate([
            'reviewer' => 'required|string|max:255',
            'selected_ids' => 'required|array',
        ]);

        AbstractPaper::whereIn('id', $request->selected_ids)
            ->update(['reviewer' => $request->reviewer]);

        return redirect()->route('dashboard')->with('success', 'Reviewer assigned successfully.');
    }

}
