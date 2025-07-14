<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 0.5rem 1rem;
            text-align: left;
        }
        th {
            background-color: #2980b9;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        h2 {
            color: #2c3e50;
        }
    </style>
</head>
<body>

    <h2>Report for Event</h2>

    @if (empty($reportData))
        <p>No data available for this report.</p>
    @else

        <table>
            <thead>
                <tr>
                    <th>Abstract Paper ID</th>
                    @foreach ($eventForms as $form)
                        <th>{{ $form->label }}</th>
                    @endforeach
                    <th>Total Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportData as $abstractId => $scores)
                    <tr>
                        <td>{{ $abstractId }}</td>
                        @foreach ($eventForms as $form)
                            <td>{{ $scores[$form->label] ?? '-' }}</td>
                        @endforeach
                        <td><strong>{{ $scores['_total'] }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>
