<!DOCTYPE html>
<html>
<head>
    <title>Files for {{ $title }} ({{ $event }})</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }

        h1 {
            margin-bottom: 1rem;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin: 0.5rem 0;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Files for "<strong>{{ $title }}</strong>" in Event "<strong>{{ $event }}</strong>"</h1>

    @if (count($files) > 0)
        <ul>
            @foreach ($files as $file)
                <li>
                    📄 
                    <a href="{{ asset('storage/' . $file) }}" target="_blank">
                        {{ $file }}
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>No files found in this directory.</p>
    @endif
</body>
</html>
