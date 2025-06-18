<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbstractPaper;
use Illuminate\Support\Facades\Auth;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

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

            if ($request->has('event') && $request->event) {
                $eventModels = \App\Models\Event::where('event_name',$request->event)->first();
                $query->where('event_id', $eventModels->id);
            }

            if ($request->has('presentation_type') && $request->presentation_type) {
                $query->where('presentation_type', $request->presentation_type);
            }
            $topics = Topic::select('topic')->distinct()->pluck('topic');

            $eventLists = \App\Models\Event::select('event_name')->distinct()->pluck('event_name');

            // need revision
            
            $abstractPapers = $query->get();
            
            $uniqueReviewers = $query->select('reviewer', 'event')->distinct()->get();

            return view('/dashboard', compact('abstractPapers', 'uniqueReviewers', 'topics', 'eventLists'));
        }
        return redirect()->route('custom.login', ['event' => 'admin_event']);
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
