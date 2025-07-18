<!DOCTYPE html>
<html>
<head>
    <title>Upload Zip</title>
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
    <h1>Upload an Abstract</h1>

    @if(session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    <form id="myForm" method="POST" action="/upload" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="event_id" value="{{ Auth::user()->event_id }}">

        <label for="title">Title</label>
        <input type="text" name="title" id="title" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" id="description" required></textarea>
        
        <label for="topic">Topic</label>
        <select id="topic" name="topic" required>
            <option value="">--Please choose an option--</option>
            @foreach ($topics as $topic)
                <option value="{{ $topic }}">{{ ucfirst($topic) }}</option>
            @endforeach
        </select>

        <label for="presentation_type">Presentation Type</label>
        <select id="presentation_type" name="presentation_type" required>
            <option value="">--Please choose an option--</option>
            <option value="poster">Poster</option>
            <option value="oral">Oral</option>
        </select>

        <label for="pdf">Abstract PDF</label>
        <input type="file" name="pdf" accept=".pdf" required>

        <div style="display: flex; align-items: center;">
            <label for="zip_file">Supporting File (ZIP)</label>
            <span style="opacity: 0.6; font-size: 0.85em;">*optional</span>
        </div>
        
        <input type="file" name="zip_file" accept=".zip">
        <p style="font-size: 0.9em; color: #555; margin-top: 1em;">
            📁 Please compress your files into a <strong>.zip (not rar)</strong> archive with a maximum of 8 megabytes before uploading.  
            For best results, use supported formats like <strong>.pdf</strong>, <strong>.jpg</strong>, <strong>.mp4</strong>, or <strong>.txt</strong> inside the archive.
        </p>

        <label for="presenter_name">Presenter Name</label>
        <input type="text" name="presenter_name" id="presenter_name" required>


        <div id="dynamicFields"></div>
        <button type="button" id="addFieldBtn">Add Author</button>

        <button type="submit" value="submit">Submit</button>
    </form>

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

            fieldGroup.appendChild(label('author_name_' + count, 'Author Name'));
            fieldGroup.appendChild(input('text', 'author_name', author.name || ''));


            fieldGroup.appendChild(label('author_affiliation_' + count, 'Author Affiliation'));
            fieldGroup.appendChild(input('text', 'author_affiliation', author.affiliation || ''));

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.textContent = 'Remove';
            removeBtn.addEventListener('click', () => {
                container.removeChild(fieldGroup);
            });

            fieldGroup.appendChild(removeBtn);
            container.appendChild(fieldGroup);
            count++;
        }

        document.getElementById('addFieldBtn').addEventListener('click', () => {
            createFieldGroup();
        });

        createFieldGroup(); // Initialize with one set
    </script>
</body>
</html>
