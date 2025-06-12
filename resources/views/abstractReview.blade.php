<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Title</th>
            <th>Status</th>
            <th>Topic</th>
            <th>Reviewer</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($abstractPapers as $paper)
            <tr>
                <td>{{ $paper->title }}</td>
                <td>{{ $paper->status }}</td>
                <td>{{ $paper->topic }}</td>
                <td>{{ $paper->reviewer }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
