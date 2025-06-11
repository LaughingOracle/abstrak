<h1>Home</h1>
@guest
<a href="{{ route('register') }}">Go to Register</a>
<br>
<a href="{{ route('login') }}">Login</a>
<br>
<a href="{{ route('login') }}">Submit Abstract</a>
<br>
@endguest



@auth
<a href="{{ route('usermenu') }}">Menu</a>
@endauth