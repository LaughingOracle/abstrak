<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- You can add Bootstrap or any CSS here if desired -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container mt-4">
        <h3> add event/topic</h3>
        <p> Dev note: Tolong baca dokumentasi yang disediakan sebelum event </P>
        <form method="POST" action=" {{route('assignEvent')}} ">
            @csrf
            <div class="mb-3">
                <label for="event">Assign event:</label>
                <input type="text" name="event" id="add_event" required>
                <button type="submit" class="btn btn-primary mt-2">Assign event</button>
            </div>
        </form>

        <form method="POST" action=" {{route('assignTopic')}} ">
            @csrf

            <div style="all: unset;">
                <label for="topic">Assign topic:</label>
                <input type="text" name="topic" id="add_topic" required>
                <label for="event">Filter by Event:</label>
                <select name="event" id="event" required>
                    <option value="">-- Unselected --</option>
                    @foreach ($eventLists as $eventList)
                        <option value="{{ $eventList }}" {{ request('event') == $eventList ? 'selected' : '' }}>
                            {{ ucfirst($eventList) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary mt-2">Assign topic</button>
            </div>
        </form>

        <hr>
        <h3>filtering tools: </h3>
        <!-- Filter Form -->
        <form method="GET" action="dashboard" class="mb-4">
            <div class="row">

                <div class="col-md-4">
                    <label for="event">Filter by Event:</label>
                    <select name="event" id="event" class="form-control">
                        <option value="">-- All Event --</option>
                        @foreach ($eventLists as $eventList)
                            <option value="{{ $eventList }}" {{ request('event') == $eventList ? 'selected' : '' }}>
                                {{ ucfirst($eventList) }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="col-md-4">
                    <label for="topic">Filter by Topic:</label>
                    <select name="topic" id="topic" class="form-control">
                        <option value="">-- All Topics --</option>
                        @foreach ($topics as $topic)

                            <option value="{{ $topic }}" {{ request('topic') == $topic ? 'selected' : '' }}>
                                {{ ucfirst($topic) }}
                            </option>


                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="presentation_type">Filter by Presentation Type:</label>
                    <select name="presentation_type" id="presentation_type" class="form-control">
                        <option value="">-- All Presentation Type --</option>
                        <option value="oral" {{ request('presentation_type') == 'oral' ? 'selected' : '' }}>Oral</option>
                        <option value="poster" {{ request('presentation_type') == 'poster' ? 'selected' : '' }}>Poster</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Apply Filter</button>
        </form>

        <!-- Reviewer Assignment Form -->
        <form method="POST" action="{{ route('insertReviewer') }}">
            @csrf
            <div class="mb-3">
                <label for="reviewer">Assign Reviewer:</label>
                <input type="text" name="reviewer" id="reviewer" class="form-control" required>
                <button type="submit" class="btn btn-primary mt-2">Assign Reviewer to Selected</button>
            </div>

            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>ID</th>
                        <th>Event</th>
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
                            <td>{{ $paper->event }}</td>
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
        <br>

        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Reviewer</th>
                    <th>Url</th>
                </tr>
            </thead>
            <tbody>
                @foreach($uniqueReviewers as $row)
                    @if(!is_null($row->event) && !is_null($row->reviewer))
                        <tr>
                            <td>{{ $row->event }}</td>
                            <td>{{ $row->reviewer }}</td>
                            <td>
                                <a href="{{ route('listing', ['event' => $row->event, 'name' => $row->reviewer]) }}">
                                    {{ route('listing', ['event' => $row->event, 'name' => $row->reviewer]) }}
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        // Select All Checkbox Logic
        document.getElementById('select-all').addEventListener('change', function (e) {
            const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>