<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />
        @if (session('warning'))
            <div class="mb-4 text-sm text-red-600">
                {{ session('warning') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="event" id="event" value="{{ $event }}">
            <!-- Title -->
            <div>
                <x-label for="title" value="Title" />
                <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
            </div>

            <!-- First Name -->
            <div class="mt-4">
                <x-label for="full_name" value="Full name" />
                <x-input id="full_name" class="block mt-1 w-full" type="text" name="full_name" :value="old('full_name')" required />
            </div>

            <!-- Last Name -->
            <div class="mt-4">
                <x-label for="username" value="Username" />
                <x-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required />
            </div>

            <!-- Phone Number -->
            <div class="mt-4">
                <x-label for="phone_number" value="Phone Number" />
                <x-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" required />
            </div>

            <!-- Institution -->
            <div class="mt-4">
                <x-label for="institution" value="Institution" />
                <x-input id="institution" class="block mt-1 w-full" type="text" name="institution" :value="old('institution')" required />
            </div>

            <!-- Contact Preference -->
            <div class="mt-4">
                <x-label for="contact_preference" value="Contact Preference" />
                <select id="contact_preference" name="contact_preference" class="block mt-1 w-full" required>
                    <option value="email" {{ old('contact_preference') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="phone number" {{ old('contact_preference') === 'phone number' ? 'selected' : '' }}>Phone Number</option>
                </select>
            </div>

            <!-- Address -->
            <div class="mt-4">
                <x-label for="address" value="Address" />
                <x-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="email" value="Email" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" value="Password" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" value="Confirm Password" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{  route('custom.login', ['event' => $event])}}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>

    </x-authentication-card>
</x-guest-layout>
