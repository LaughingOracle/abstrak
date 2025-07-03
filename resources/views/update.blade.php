<!DOCTYPE html>
<html>
<head><title>Upload Zip</title>

<style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 30px auto;
            padding: 20px;
            background: #f4f7fa;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="text"],
        select,
        input[type="file"] {
            border-radius: 5px;
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        textarea {
            width: 100%;
            height: 150px;
            resize: none;
            border-radius: 5px;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }


        button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #2980b9;
        }
        .field-group {
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            background: #fff;
        }
        .field-group button {
            background: #e74c3c;
        }
        .field-group button:hover {
            background: #c0392b;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>update abstract</h1>
    @if(session('error')) <p style="color:red;">{{ session('error') }}</p> @endif
    <form id="myForm" action="{{ route('abstracts.update', $abstract->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="title">title</label>
        <input type="text" name="title" id="title" value="{{ $abstract->title}}">

        <br><br>

        <label for="description">description</label>
        <textarea id="description" name="description" id="description" required> {{ $abstract->description}} </textarea>
        

        <br><br>

        <label for="topic">topic:</label>
        <select id="topic" name="topic">
            @php
                $topics = DB::table('topics')
                ->where('event_id', auth()->user()->event_id)
                ->pluck('topic');
            @endphp

            @foreach ($topics as $topic)
                <option value="{{ $topic }}" {{ $abstract->topic === $topic ? 'selected' : '' }}>
                    {{ ucfirst($topic) }}
                </option>
            @endforeach
        </select>

        <br><br>

        <label for="presentation_type">presentation type:</label>
        <select id="presentation_type" name="presentation_type">
            @php
                $presentation_types = ['poster', 'oral'];
            @endphp

            @foreach ($presentation_types as $presentation_type)
                <option value="{{ $presentation_type }}" {{ $abstract->presentation_type === $presentation_type ? 'selected' : '' }}>
                    {{ ucfirst($presentation_type) }}
                </option>
            @endforeach
        </select>


        <br><br>

        <label for="pdf">Abstract PDF</label>
        <input type="file" name="pdf" required>

        <div style="display: flex; align-items: center;">
            <label for="zip_file">Supporting File (ZIP)</label>
            <span style="opacity: 0.6; font-size: 0.85em;">*optional</span>
        </div>
        
        <input type="file" name="zip_file">
        <p style="font-size: 0.9em; color: #555; margin-top: 1em;">
            📁 Please compress your files into a <strong>.zip (not rar)</strong> archive with a maximum of 8 megabytes before uploading.  
            For best results, use supported formats like <strong>.pdf</strong>, <strong>.jpg</strong>, <strong>.mp4</strong>, or <strong>.txt</strong> inside the archive.
        </p>


        <br><br><br>

        <label for="presenter_name">Presenter name</label>
        <input type="text" name="presenter_name" id="presenter_name" value="{{ $abstract->presenter->name}}">

        <br><br>
        <br><br><br>

        <div id="dynamicFields"></div>
        <button type="button" id="addFieldBtn">Add author</button>

        <br><br>
        <button type="submit" value="submit">Update</button>
    </form>
@php
    $authorIds = $abstract->author()->pluck('author_id');
    $existingAuthors = \App\Models\Author::whereIn('id', $authorIds)->get(['name', 'affiliation']);
@endphp
<script>
    let count = 1;

    function createFieldGroup(author = {}) {
        const container = document.getElementById('dynamicFields');
        const fieldGroup = document.createElement('div');
        fieldGroup.className = 'field-group';
        fieldGroup.id = 'fieldGroup' + count;

        const input = (type, name, value = '') => {
            const input = document.createElement('input');
            input.type = type;
            input.name = name + '[]';
            input.id = name + '_' + count;
            input.value = value;
            input.required = true;
            return input;
        };

        const label = (forId, text) => {
            const label = document.createElement('label');
            label.setAttribute('for', forId);
            label.textContent = text;
            return label;
        };

        const br = () => document.createElement('br');

        fieldGroup.appendChild(label('author_name_' + count, 'Author name: '));
        fieldGroup.appendChild(input('text', 'author_name', author.name || ''));
        fieldGroup.appendChild(br()); fieldGroup.appendChild(br());

        fieldGroup.appendChild(label('author_affiliation_' + count, 'Author affiliation: '));
        fieldGroup.appendChild(input('text', 'author_affiliation', author.affiliation || ''));
        fieldGroup.appendChild(br());

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.textContent = 'Remove';
        removeBtn.style.marginLeft = '10px';
        removeBtn.addEventListener('click', () => {
            container.removeChild(fieldGroup);
        });

        fieldGroup.appendChild(removeBtn);
        fieldGroup.appendChild(br()); fieldGroup.appendChild(br());

        container.appendChild(fieldGroup);
        count++;
    }

    document.getElementById('addFieldBtn').addEventListener('click', () => {
        createFieldGroup(); // Add empty input fields
    });

    // Preload existing authors (Blade to JS)
    const existingAuthors = @json($existingAuthors);
    existingAuthors.forEach(author => createFieldGroup(author));
</script>
</body>
</html>