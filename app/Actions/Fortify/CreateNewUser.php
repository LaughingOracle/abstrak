<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use App\Models\AbstractAccount;

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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:abstract_accounts'],
            'password' => $this->passwordRules(),
            'title' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'institution' => ['required', 'string'],
            'contact_preference' => ['required', 'in:email,phone number'],
            'address' => ['required', 'string'],
        ])->validate();

        return AbstractAccount::create([
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'title' => $input['title'],
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'phone_number' => $input['phone_number'],
            'institution' => $input['institution'],
            'contact_preference' => $input['contact_preference'],
            'address' => $input['address'],
        ]);
    }

}
