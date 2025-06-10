<h1>Home</h1>
<a href="{{ route('register') }}">Go to Register</a>
<br>
<a href="{{ route('login') }}">Login</a>
<br>
<a href="{{ auth()->check() ? route('zip.form') : route('login') }}"
   class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
   Upload a Zip
</a>