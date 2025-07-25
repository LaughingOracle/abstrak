<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>score an Abstracts</title>
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
            background-color:rgb(223, 236, 255);
        }

        tbody tr:hover {
            background-color:rgb(194, 229, 255);
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
    <h1>Penilaian</h1>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Topic</th>
                <th>Jury</th>
                <th>Score</th>
            </tr>
        </thead>
            <tbody>
                @foreach ($abstractPapers as $paper)
                    <tr style="{{ $scoreBool->contains($paper->id) ? 'background-color:rgb(159, 255, 181);' : '' }}">
                        <td>{{ $paper->title }}</td>
                        <td>{{ $paper->topic }}</td>
                        <td>{{ $paper->jury }}</td>
                        <td>
                            <form action="{{ route('scoreMenu2', ['id' => $paper->id]) }}" method="GET" style="display:inline;">
                                @csrf
                                <button type="submit">Score</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
    </table>
</body>
</html>
