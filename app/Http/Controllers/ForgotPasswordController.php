<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbstractAccount;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class ForgotPasswordController extends Controller
{
    public function showRequestForm($event)
    {
        return view('auth.passwords.email', compact('event'));
    }

    public function sendResetLink(Request $request, $event)
    {
        $request->validate(['email' => 'required|email']);
        
        $eventModel = Event::where('event_name', $event)->first();
        $user = AbstractAccount::where('email', $request->email)
                             ->where('event_id', $eventModel->id)
                             ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No matching user found for this event.']);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        $link = url("/reset-password/$token/$event");

        // send plain email (replace with notification if needed)
        Mail::raw("Reset your password: $link", function ($message) use ($request) {
            $message->to($request->email)->subject('Your Password Reset Link');
        });

        return back()->with('status', 'Reset link sent!');
    }

    public function showResetForm($token, $event)
    {
        return view('auth.passwords.reset', compact('token', 'event'));
    }

    public function resetPassword(Request $request, $event)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record || now()->diffInMinutes($record->created_at) > 60) {
            return back()->withErrors(['token' => 'Invalid or expired token.']);
        }

        $eventModel = Event::where('event_name', $event)->first();

        $user = AbstractAccount::where('email', $request->email)
                ->where('event_id', $eventModel->id)
                ->first();


        if (!$user) {
            return back()->withErrors(['email' => 'No matching user.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect("/login/$event")->with('status', 'Password has been reset.');
    }
}