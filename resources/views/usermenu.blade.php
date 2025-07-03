<!DOCTYPE html>
<html>
<head>
    <title>User: {{ auth()->user()->username }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background: #f4f7fa;
            color: #333;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        p {
            font-size: 16px;
        }

        a.button-link,
        button {
            display: inline-block;
            padding: 8px 15px;
            margin: 6px 5px 6px 0;
            text-decoration: none;
            background: #5dade2;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            transition: background 0.3s ease;
        }

        a.button-link:hover,
        button:hover {
            background: #3498db;
        }

        .admin-section {
            background: #d6eaf8;
            padding: 15px 20px;
            border-left: 6px solid #3498db;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            padding: 12px 14px;
            text-align: center;
        }

        table th {
            background-color: #2980b9;
            color: #fff;
            font-weight: 600;
        }

        table tr:nth-child(even) {
            background-color: #f2f6fc;
        }

        table tr:hover {
            background-color: #eaf2f8;
        }

        form {
            display: inline;
        }

        .no-abstracts {
            background: #fcf3cf;
            color: #7d6608;
            padding: 15px;
            border-left: 5px solid #f4d03f;
            border-radius: 6px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <h1>User: {{ auth()->user()->username }}</h1>

    @auth
        @if (Auth::user()->email === 'admin@gmail.com')
            <div class="admin-section">
                <p><strong>Welcome, Admin!</strong></p>
                <a href="{{ route('dashboard') }}" class="button-link">Go to Dashboard</a>
            </div>
        @endif
    @endauth

    <a href="{{ route('zip.form') }}" class="button-link">Submit Abstract</a>

    @if($abstracts->isEmpty())
        <div class="no-abstracts">
            <p>No abstract found.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Topic</th>
                    <th>PDF</th>
                    <th>Supporting File</th>
                    <th>Presentation Type</th>
                    <th>Uploaded At</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($abstracts as $index => $abstract)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $abstract->title }}</td>
                        <td>{{ $abstract->topic }}</td>
                        <td><a href="{{ route('viewAbstract', ['id' => $abstract->id]) }}">
                            View Abstract
                        </a></td>
                        <td><a href="{{ route('view', ['id' => $abstract->id]) }}">
                            View File
                        </a></td>
                        <td>{{ $abstract->presentation_type }}</td>
                        <td>{{ $abstract->created_at->format('Y-m-d') }}</td>
                        <td>{{ $abstract->status }}</td>
                        <td>
                            <a href="{{ route('abstracts.edit', $abstract->id) }}" class="button-link">Update</a>
                            <form action="{{ route('abstracts.destroy', $abstract->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
