<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm(Request $request, $event)
    {
        return view('auth.login',['event' => $event]);
    }

    public function login(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken(); // Optional but recommended for security
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'event' => ['required']
        ]);

        $eventdb = \App\Models\Event::where('event_name',$request->event)->first();

        if (! $eventdb) {
            return back()->withErrors([
                'event' => 'Event not found. ',
            ])->onlyInput('event');
        }

        $user = \App\Models\AbstractAccount::where('email', $request->email)
            ->where('event_id', $eventdb->id)
            ->first();
        
        if ($user && \Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->filled('remember'));
            return redirect()->route('usermenu', ['event' => $request->event]);
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }
}