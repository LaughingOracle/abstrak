<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Abstracts</title>
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
            color: #2c3e50;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 14px;
            text-align: center;
        }

        thead {
            background-color: #2980b9;
            color: #fff;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f6fc;
        }

        tbody tr:hover {
            background-color: #eaf2f8;
        }

        a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        button {
            background: #5dade2;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            margin: 2px;
            cursor: pointer;
            font-size: 13px;
        }

        button:hover {
            background: #3498db;
        }

        form {
            display: inline;
        }
    </style>
</head>
<body>
    <h1>Review Abstracts</h1>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Topic</th>
                <th>Reviewer</th>
                <th>Abstract</th>
                <th>Supporting File</th>
                <th>Presentation Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($abstractPapers as $paper)
                <tr>
                    <td>{{ $paper->title }}</td>
                    <td>{{ $paper->topic }}</td>
                    <td>{{ $paper->reviewer }}</td>
                    <td>
                        <a href="{{ route('viewAbstract', ['id' => $paper->id]) }}">
                            View Abstract
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('view', ['id' => $paper->id]) }}">
                            View File
                        </a>
                    </td>
                    
                    <td>
                        {{ $paper->presentation_type }}
                        @if ($paper->status === "dalam review")
                            <form action="{{ route('revise', $paper->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit">Ubah</button>
                            </form>
                        @endif
                    </td>

                    <td>
                        <form action="{{ route('scoreMenu', ['id' => $paper->id]) }}" method="GET" style="display:inline;">
                            @csrf
                            @if ($paper->status === "dalam review")
                            <button type="submit">Score</button> @else
                                {{ $paper->status }}
                            @endif
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
