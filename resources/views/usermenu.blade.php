<!DOCTYPE html>
<html>
<head>
    <title>User: {{ auth()->user()->username }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1100px;
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

        .notification-section {
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

    @if (session('error'))
        <div class="alert alert-info notification-section">
            {{ session('error') }}
        </div>
    @endif

    @auth
        @if (Auth::user()->email === 'admin@gmail.com')
            <div class="admin-section">
                <p><strong>Welcome, Admin!</strong></p>
                <a href="{{ route('dashboard') }}" class="button-link">Go to Dashboard</a>
            </div>
        @endif
    @endauth

    @if ($notification)
        <div class="alert alert-info notification-section">
            One or More of Your Abstracts Have Been Accepted
        </div>
    @endif

    @if($expiry)
        <a href="{{ route('zip.form') }}" class="button-link">Submit Abstract</a>
    @endif
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
                        <td>{{ Str::limit($abstract->title, 30) }}</td>
                        <td>{{ $abstract->topic }}</td>
                        <td><a href="{{ route('viewAbstract', ['id' => $abstract->id]) }}">
                            View Abstract
                        </a></td>
                        <td><a href="{{ route('view', ['id' => $abstract->id]) }}">
                            View File
                        </a></td>
                        <td>
                            {{ $abstract->presentation_type }} 
                            @if(in_array($abstract->id, $presentationList))
                                <br>
                                <button onclick="window.location='{{ route('viewPresentation', $abstract->id) }}'" >
                                    View
                                </button>
                            @endif
                        </td>
                        <td>{{ $abstract->created_at->format('Y-m-d') }}</td>
                        <td>{{ $abstract->status }}</td>
                        <td>
                            @if ($abstract->status == 'passed')
                                <div class="p-8">
                                    <button onclick="openModal({{ $abstract->id }}, '{{ $abstract->presentation_type }}')" class="bg-blue-600 text-white px-4 py-2 rounded">
                                        Upload/Update Poster/Oral
                                    </button>
                                </div>
                            @elseif ($expiry && $abstract->status !== 'failed')
                                <a href="{{ route('abstracts.edit', $abstract->id) }}" class="button-link">Update</a>
                                <form action="{{ route('abstracts.destroy', $abstract->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            @endif

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <div id="modal-backdrop" style="display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">

        {{-- Modal Box --}}
        <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; border-radius: 8px; width: 350px; box-shadow: 0 0 10px rgba(0,0,0,0.3);">

            {{-- Close Button --}}
            <span onclick="closeModal()" style="position: absolute; top: 10px; right: 15px; font-size: 20px; cursor: pointer; color: #aaa;">&times;</span>

            <h3 style="margin-top: 0;">Presentation Form</h3>

            <form method="POST" action="{{ route('uploadPresentation') }}" enctype="multipart/form-data">
                @csrf
                <label>File:</label><br>
                
                <input type="hidden" name="id" id="abstract-id-field" value="">
                <p style="font-size: 0.9em; color: #555; margin-top: 1em;">
                    📁 Please upload according to your presentation type. Oral: pdf. Poster: png
                </p>
                <input type="file" name="file" id="abstract-file-input" required>

                <p style="font-size: 0.9em; color: #555; margin-top: 1em;">
                    *This page serves as both upload and update, if you want to update your presentation file, just upload another one and it will replace the old one
                </p>
                <button type="submit"
                        style="background-color: #2980b9; color: white; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer;">
                    Submit
                </button>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, type) {
            // Set abstract ID into the hidden input
            document.getElementById('abstract-id-field').value = id;

            // Set file accept type based on presentation type
            const fileInput = document.getElementById('abstract-file-input');
            if (type.toLowerCase() === 'oral') {
                fileInput.accept = '.pdf';
            } else if (type.toLowerCase() === 'poster') {
                fileInput.accept = '.png';
            } else {
                fileInput.accept = ''; // fallback to allow anything
            }

            // Show modal
            document.getElementById('modal-backdrop').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modal-backdrop').style.display = 'none';
        }
    </script>
</body>
</html>
