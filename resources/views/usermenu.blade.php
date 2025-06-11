<!DOCTYPE html>
<html>
<head>
    <title>User: {{ auth()->user()->username }}</title>
</head>
<body>
    <h1>User: {{ auth()->user()->username }}</h1>
    <a href="{{ route('zip.form') }}">Submit Abstract</a>
    <br>

    @if($abstracts->isEmpty())
        <p>No abstract found.</p>
    @else

        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Topic</th>
                    <th>Presentation type</th>
                    <th>uploaded At</th>
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
                        <td>{{ $abstract->presentation_type }}</td>
                        <td>{{ $abstract->created_at->format('Y-m-d') }}</td>
                        <td>{{ $abstract->status }}</td>
                        <td>
                            <!-- Update Button -->
                            <a href="{{ route('abstracts.edit', $abstract->id) }}">
                                <button type="button">Update</button>
                            </a>

                            <!-- Delete Button -->
                            <form action="{{ route('abstracts.destroy', $abstract->id) }}" method="POST" style="display:inline;">
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
