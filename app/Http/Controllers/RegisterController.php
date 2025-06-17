<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function show($event)
    {
        return view('auth.register', ['event' => $event]);
    }

    public function store(Request $request)
    {
        $user = app(CreateNewUser::class)->create($request->all());

        Auth::login($user);

        return redirect('/usermenu/' . $request->event);
    }
}
