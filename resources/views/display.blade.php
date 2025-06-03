<!DOCTYPE html>
<html>
<head><title>Extracted Files</title></head>
<body>
    <h1 class="title has-text-white"> All Files </h1>
    <ul>
        @foreach ($files as $file)
            <li>
                <a href="{{ asset('storage/' . $file) }}" target="_blank">{{ basename($file) }}</a>
            </li>
        @endforeach
    </ul>
</body>
</html>