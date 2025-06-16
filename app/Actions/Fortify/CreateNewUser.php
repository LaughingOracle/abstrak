<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use App\Models\AbstractAccount;
use Illuminate\Validation\Rule;
use App\Models\Event;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): AbstractAccount
    {
        Validator::make($input, [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => $this->passwordRules(),
            'title' => ['required', 'string'],
            'full_name' => ['required', 'string'],
            'username' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'institution' => ['required', 'string'],
            'contact_preference' => ['required', 'in:email,phone number'],
            'address' => ['required', 'string'],
            'event' => ['required', 'string'],
            Rule::unique('abstract_accounts')->where(function ($query) use ($input) {
                return $query->where('event_id', $input['event_id']);
            }),


        ])->validate();

        $eventName = $input['event'];
        $event = Event::where('event_name', $eventName)->first();



        return AbstractAccount::create([
            'event_id' => $event->id,
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'title' => $input['title'],
            'full_name' => $input['full_name'],
            'username' => $input['username'],
            'phone_number' => $input['phone_number'],
            'institution' => $input['institution'],
            'contact_preference' => $input['contact_preference'],
            'address' => $input['address'],
        ]);
    }

}
