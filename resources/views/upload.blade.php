<!DOCTYPE html>
<html>
<head><title>Upload Zip</title></head>
<body>
    <h1>Upload a ZIP File</h1>
    @if(session('error')) <p style="color:red;">{{ session('error') }}</p> @endif
    <form id="myForm" method="POST" action="/upload" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="event_id" value="{{ Auth::user()->event_id }}">
        <label for="title">title</label>
        <input type="text" name="title" id="title" required>

        <br><br>

        <label for="description">description</label>
        <input type="text" name="description" id="description" required>

        <br><br>

        <label for="topic">Topic:</label>
        <select id="topic" name="topic" required>
            <option value="">--Please choose an option--</option>
            @foreach ($topics as $topic)
                <option value="{{ $topic }}">{{ ucfirst($topic) }}</option>
            @endforeach
        </select>

        <br><br>

        <label for="presentation_type">presentation type:</label>
        <select id="presentation_type" name="presentation_type" required>
            <option value="">--Please choose an option--</option>
            <option value="poster">poster</option>
            <option value="oral">oral</option>
        </select>

        <br><br>

        <input type="file" name="zip_file" required>

        <br><br><br>

        <label for="presenter_name">Presenter name</label>
        <input type="text" name="presenter_name" id="presenter_name" required>

        <br><br>

        <label for="presenter_email">Presenter email</label>
        <input type="text" name="presenter_email" id="presenter_email" required>

        <br><br><br>

        <div id="dynamicFields"></div>
        <button type="button" id="addFieldBtn">Add author</button>

        <br><br>
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

        const br = () => document.createElement('br');

        fieldGroup.appendChild(label('author_name_' + count, 'Author name: '));
        fieldGroup.appendChild(input('text', 'author_name', author.name || ''));
        fieldGroup.appendChild(br()); fieldGroup.appendChild(br());

        fieldGroup.appendChild(label('author_email_' + count, 'Author email: '));
        fieldGroup.appendChild(input('text', 'author_email', author.email || ''));
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
    createFieldGroup(); // or createFieldGroup({});
    </script>
</body>
</html>