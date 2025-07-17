<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbstractPaper;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class UserController extends Controller
{
    public function listing(Request $request, $event)
    {
        if (!Auth::check()) {
            return redirect()->route('custom.login', ['event' => $event]);
        }
        $userId = auth()->id();
        $user = Auth::user();
        // You can validate the role if needed here

        $expectedEventId = Event::where('event_name', $event)->first();
        if (!$expectedEventId || $user->event_id !== $expectedEventId->id) {
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return view('auth.login', ['event' => $event]);
        }

        $abstracts = AbstractPaper::where('abstract_account_id', $userId)->where('event_id',auth()->user()->event_id)->get();
        $deadline = Event::where('id', auth()->user()->event_id)->value('deadline');
        $deadlineDate = Carbon::parse($deadline)->startOfDay();
        $today = Carbon::now()->startOfDay();

        $notified = $abstracts->filter(function ($abstract) {
            return $abstract->status === 'passed' && !$abstract->notified;
        });

        $notification = $notified->isNotEmpty();

        $notified->each(function ($abstract) {
            $abstract->notified = true;
            $abstract->save();
        });

        $presentationList = [];
        foreach($abstracts as $doc){
            $ext = 'pdf';
            if($doc->presentation_type == 'poster'){
                $ext = 'png';
            }
            $filePath = "presentation/{$doc->id}/{$doc->id}.{$ext}";
            if (Storage::disk('public')->exists($filePath)){
                $presentationList[] = $doc->id;
            }
        }

        $expiry = $today->lessThanOrEqualTo($deadlineDate);
        return view('usermenu', compact('abstracts', 'expiry', 'notification', 'presentationList'));
    }
}
