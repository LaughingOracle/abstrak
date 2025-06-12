<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Title</th>
            <th>Topic</th>
            <th>Reviewer</th>
            <th>File</th>
            <th>Tipe Presentasi</th>
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
                    <a href="{{ route('view', ['title' => $paper->title]) }}">
                        file
                    </a>
                </td>
                <form action="{{ route('revise', $paper->id) }}" method="POST" style="display:inline;">
                    <td>
                        {{ $paper->presentation_type }}
                        @if ($paper->status === "dalam review")
                            <button>Ubah</button>
                        @endif
                    </td>
                </form>
                <form action="{{ route('review', $paper->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <td>

                    @if ($paper->status === "dalam review")
                        <button type="submit" name="lulus" onclick="return confirm('Are you sure?')">Lulus</button>
                        <button type="submit" name="tidak_lulus" onclick="return confirm('Are you sure?')">Tidak Lulus</button>
                    @else
                        {{ $paper->status }}
                    @endif
                    </td>
                </form>
            </tr>
        @endforeach
    </tbody>
</table>
