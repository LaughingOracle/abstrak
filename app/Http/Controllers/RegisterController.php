<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use App\Models\AbstractAccount;
use App\Models\Event;

class RegisterController extends Controller
{
    public function show($event)
    {
        return view('auth.register', ['event' => $event]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'event' => 'required',
        ]);

        $event = Event::where('event_name', $request->event)->first();

        $exists = AbstractAccount::where('email', $request->email)
                             ->where('event_id', $event->id)
                             ->exists();

        if ($exists) {
            return redirect()->route('register.with.event', ['event' => $request->event])
                            ->with('warning', 'An account with this email already exists.');
        }

        $user = app(CreateNewUser::class)->create($request->all());

        Auth::login($user);

        return redirect('/usermenu/' . $request->event);
    }
}
