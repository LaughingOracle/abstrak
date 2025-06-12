
    <div>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Reviewer</th>
                    <th>url</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($uniqueReviewers as $reviewer)
                    <tr>
                        <td>{{ $reviewer}}</td>
                        <td>
                            <a href="{{ route('listing', ['name' => $reviewer]) }}">
                                {{ route('listing', ['name' => $reviewer]) }}
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="container">
        <h2>Abstract Papers</h2>

        <!-- Filter Form -->
        <form method="GET" action="dashboard" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="topic">Filter by Topic:</label>
                    <select name="topic" id="topic" class="form-control">
                        <option value="">-- All Topics --</option>
                        <option value="gastroentrology" {{ request('topic') == 'gastroentrology' ? 'selected' : '' }}>Gastroentrology</option>
                        <option value="hepatology" {{ request('topic') == 'hepatology' ? 'selected' : '' }}>Hepatology</option>
                        <option value="others(miscellaneous)" {{ request('topic') == 'others(miscellaneous)' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label for="topic">Filter by Presentation Type 	:</label>
                    <select name="presentation_type" id="presentation_type" class="form-control"">
                        <option value="">-- All Presentation Type --</option>
                        <option value="oral" {{ request('presentation_type') == 'oral' ? 'selected' : '' }}>Oral</option>
                        <option value="poster" {{ request('presentation_type') == 'poster' ? 'selected' : '' }}>Poster</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Apply filter</button>

        </form>

        <!-- Reviewer Assignment Form -->
        <form method="POST" action="{{ route('insertReviewer') }}">
            @csrf
            <div class="mb-3">
                <label for="reviewer">Assign Reviewer:</label>
                <input type="text" name="reviewer" id="reviewer" class="form-control" required>
                <button type="submit" class="btn btn-primary">Assign Reviewer to Selected</button>
            </div>

            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Topic</th>
                        <th>Presentation Type</th>
                        <th>Status</th>
                        <th>Reviewer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($abstractPapers as $paper)
                        <tr>
                            <td><input type="checkbox" name="selected_ids[]" value="{{ $paper->id }}"></td>
                            <td>{{ $paper->id }}</td>
                            <td>{{ $paper->title }}</td>
                            <td>{{ $paper->topic }}</td>
                            <td>{{ $paper->presentation_type }}</td>
                            <td>{{ $paper->status }}</td>
                            <td>{{ $paper->reviewer ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>

    <script>
        // Select All Checkbox Logic
        document.getElementById('select-all').addEventListener('change', function (e) {
            const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
        });
    </script>

