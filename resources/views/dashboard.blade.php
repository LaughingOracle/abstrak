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
        <p><a href="https://docs.google.com/document/d/1xc8ehPJVKGFQwU1n0F9zLhgcK0OoO8tlpy3GvMyPg3Q/edit?usp=sharing"> https://docs.google.com/document/d/1xc8ehPJVKGFQwU1n0F9zLhgcK0OoO8tlpy3GvMyPg3Q/edit?usp=sharing </a> <-- dokumentasi (WIP)</p>
        <form method="POST" action=" {{route('assignEvent')}} ">
            @csrf
            <div class="mb-3">
                <label for="event">Assign event:</label>
                <input type="text" name="event" id="add_event" required>
                <input type="date" id="deadline" name="deadline" required>
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
        <br>
        <label> Assign Form: </label>
        <form method="GET" action=" {{route('formMenu')}} ">
            <div style="all: unset;">
                <label for="event">Event:</label>
                <select name="event" id="event" required>
                    <option value="">-- Unselected --</option>
                    @foreach ($eventLists as $eventList)
                        <option value="{{ $eventList }}" {{ request('event') == $eventList ? 'selected' : '' }}>
                            {{ ucfirst($eventList) }}
                        </option>
                    @endforeach
                </select>
                <label> Type </label>
                <select name="type" id="type" required>
                    <option value="">-- Unselected --</option>
                    <option value="abstract">Abstract</option>
                    <option value="poster">Poster</option>
                    <option value="oral">Oral</option>
                </select>
                <button type="submit" class="btn btn-primary mt-2">Assign form</button>
            </div>
        </form>
        <label> View Scores: </label>
        <form method="GET" action=" {{route('report')}} ">
            <div style="all: unset;">
                <label for="event">Event:</label>
                <select name="event" id="event" required>
                    <option value="">-- Unselected --</option>
                    @foreach ($eventLists as $eventList)
                        <option value="{{ $eventList}}" {{ request('event') == $eventList ? 'selected' : '' }}>
                            {{ ucfirst($eventList) }}
                        </option>
                    @endforeach
                </select>
                <label> Type </label>
                <select name="type" id="type" required>
                    <option value="">-- Unselected --</option>
                    <option value="abstract">Abstract</option>
                    <option value="poster">Poster</option>
                    <option value="oral">Oral</option>
                </select>
                <button type="submit" class="btn btn-primary mt-2">View</button>
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
                            <option value="{{ $topic }}" {{ request('topic') == $topic ? 'selected' : '' }}>{{ ucfirst($topic) }}</option>
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
            <div class="row">
                <div class="col-md-4">
                    <label for="status">Filter by Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">-- All Status --</option>
                        <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}>
                            passed
                        </option>

                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>
                            failed
                        </option>

                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                            pending
                        </option>
                        
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="logistic">Filter by TV/Room (Logistic):</label>

                    <div class="row">
                        <div class="col-md-6">
                            <select name="logistic" id="logistic" class="form-control">
                                <option value="">-- All Logistic --</option>
                                <option value="TV"  {{ request('logistic') == 'TV' ? 'selected' : '' }}  >TV</option>
                                <option value="Room" {{ request('logistic') == 'Room' ? 'selected' : '' }} >Room</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="sublogistic" id="sublogistic" class="form-control">
                                <option value="">-- All Sub-Logistic --</option>
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ request('sublogistic') == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Apply Filter</button>
        </form>

        <hr>

        <!-- Reviewer Assignment Form -->
        <h4> Assignment: </h4>
        <form method="POST" action="{{ route('insertReviewer') }}">
            @csrf
            <div class="mb-3">
                <label for="reviewer">Assign Doctors Name:</label>
                <input type="text" name="reviewer" id="reviewer">

                <label for="stage">Assignment</label>
                <select name="stage" id="stage">
                        <option value="">-- Unselected --</option>
                        <option value="1"> Reviewer (stage 1) </option>
                        <option value="2"> Jury (Stage 2) </option>
                </select>
                <button id="assign-doctor" type="submit" class="btn btn-primary mt-2" name="action" value="assignment">
                    Assign Doctor to Selected
                </button>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <label for="logisticform">TV/Room</label>
                    <select name="logisticform" id="logisticform" class="form-control">
                        <option value="">-- Unselected --</option>
                        <option value="TV">TV</option>
                        <option value="Room">Room</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="sublogisticform">Number</label>
                    <select name="sublogisticform" id="sublogisticform" class="form-control">
                        <option value="">-- Unselected --</option>
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ request('sublogistic') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button id="assign-logistic" type="submit" class="btn btn-primary w-100" name="action" value="logistic">
                        Assign TV/Room (Logistic)
                    </button>
                </div>
            </div>
            <br>
            <hr>

            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>ID</th>
                        <th>Event</th>
                        <th>Title</th>
                        <th>Topic</th>
                        <th>Abstract</th>
                        <th>Supporting File</th>
                        <th>Status</th>
                        <th>Presentation Type</th>
                        <th>Room/TV (Logistics)</th>
                        <th>Reviewer (stage 1)</th>
                        <th>Jury (stage 2)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($abstractPapers as $paper)
                        <tr>
                            <td><input type="checkbox" name="selected_ids[]" value="{{ $paper->id }}"></td>
                            <td>{{ $paper->id }}</td>
                            <td>{{ $paper->event }}</td>
                            <td>{{ Str::limit($paper->title, 30) }}</td>
                            <td>{{ $paper->topic }}</td>
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
                            <td>{{ $paper->status }}</td>
                            <td>
                                <a href="{{ route('viewPresentation', $paper->id) }}" target="_blank">
                                    {{ $paper->presentation_type }}</td>
                                </a>
                            </td>
                            <td>{{ $paper->logistic ?? '-' }}</td>
                            <td>{{ $paper->reviewer ?? '-' }}</td>
                            <td>{{ $paper->jury ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
        <br>

        <div class="row">
            <div class="col-md-2">
                <label for="stage2">Choose file type</label>
                <select name="stage2" id="stage2" class="form-control">
                        <option value="">-- Unselected --</option>
                        <option value="1"> Abstract (stage 1) </option>
                        <option value="2"> Presentation (Stage 2) </option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button id="assign-logistic" type="submit" class="btn btn-primary w-100" name="action" value="downloads">
                    downloads
                </button>
            </div>
        </div>

        <hr>
        <br>

        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Reviewer</th>
                    <th>Url (stage 1)</th>
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
        <br>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Jury</th>
                    <th>Url (stage 2)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($uniqueJury as $row)
                    @if(!is_null($row->event) && !is_null($row->jury))
                        <tr>
                            <td>{{ $row->event }}</td>
                            <td>{{ $row->jury }}</td>
                            <td>
                                <a href="{{ route('scoringList', ['event' => $row->event, 'name' => $row->jury]) }}">
                                    {{ route('scoringList', ['event' => $row->event, 'name' => $row->jury]) }}
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