<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbstractPaper;
use Illuminate\Support\Facades\Auth;
use App\Models\Topic;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->email === 'admin@gmail.com') {
            $query = AbstractPaper::query();
            $topicQuery = Topic::query();

            $eventLists = Event::select('event_name')->distinct()->pluck('event_name');

            if ($request->has('topic') && $request->topic) {
                $query->where('topic', $request->topic);
                $eventList2 = DB::table('topics')
                    ->join('events', 'topics.event_id', '=', 'events.id')
                    ->where('topic', $request->topic)
                    ->select('event_name')
                    ->distinct()
                    ->pluck('event_name');

                $eventLists = $eventList2;

            }
            if ($request->has('event') && $request->event) {
                $eventModels = Event::where('event_name',$request->event)->first();
                $query->where('event_id', $eventModels->id);
                $topicQuery->where('event_id', $eventModels->id);   
            }

            if ($request->has('presentation_type') && $request->presentation_type) {
                $query->where('presentation_type', $request->presentation_type);
            }
            $topics = $topicQuery->select('topic')->distinct()->pluck('topic');

            // need revision
            
            $abstractPapers = $query->get();
            
            $uniqueReviewers = $query->select('reviewer', 'event')->distinct()->get();

            return view('/dashboard', compact('abstractPapers', 'uniqueReviewers', 'topics', 'eventLists'));
        }
        return redirect()->route('custom.login', ['event' => 'admin_event']);
    }

    public function assignEvent(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->email === 'admin@gmail.com') {
            $request->validate([
                'event' => 'required|string|max:255',
            ]);


            if(! Event::where('event_name',$request->event)->first() ){
            Event::create(['event_name' => $request->event]);

            return redirect()->route('dashboard')->with('success', 'event assigned successfully.');                
            }
            return redirect()->route('dashboard')->with('error', 'duplicate detected.');
        }else{
            return redirect()->route('custom.login', ['event' => 'admin_event']);
        }
    }

    public function assignTopic(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->email === 'admin@gmail.com') {
            $request->validate([
                'event' => 'required|string|max:255',
                'topic' => 'required|string|max:255',
            ]);

            $eventModels = Event::where('event_name',$request->event)->first();


            if(! Topic::where('topic',$request->topic)->where('event_id', $eventModels->id)->first() ){
                Topic::create([
                    'event_id' => $eventModels->id,
                    'topic' => $request->topic,
                ]);
                return redirect()->route('dashboard')->with('success', 'topic assigned successfully.');
            }
            return redirect()->route('dashboard')->with('error', 'duplicate detected.');
        } else{
            return redirect()->route('custom.login', ['event' => 'admin_event']);
        }
    }

    public function assignReviewer(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->email === 'admin@gmail.com') {

            $request->validate([
                'reviewer' => 'required|string|max:255',
                'selected_ids' => 'required|array',
            ]);

            AbstractPaper::whereIn('id', $request->selected_ids)
                ->update(['reviewer' => $request->reviewer]);

            return redirect()->route('dashboard')->with('success', 'Reviewer assigned successfully.');
        } else{
            return redirect()->route('custom.login', ['event' => 'admin_event']);
        }
    }

}
