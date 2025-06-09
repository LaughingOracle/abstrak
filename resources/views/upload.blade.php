<!DOCTYPE html>
<html>
<head><title>Upload Zip</title></head>
<body>
    <h1>Upload a ZIP File</h1>
    @if(session('error')) <p style="color:red;">{{ session('error') }}</p> @endif
    <form id="myForm" method="POST" action="/upload" enctype="multipart/form-data">
        @csrf

        <label for="title">title</label>
        <input type="text" name="title" id="title">

        <br><br>

        <label for="description">description</label>
        <input type="text" name="description" id="description">

        <br><br>

        <label for="topic">topic:</label>
        <select id="topic" name="topic">
            <option value="">--Please choose an option--</option>
            <option value="gastroentrology">gastroentrology</option>
            <option value="hepatology">hepatology</option>
            <option value="others(miscellaneous)">anothers(miscellaneous)ge</option>
        </select>

        <br><br>

        <label for="presentation_type">presentation type:</label>
        <select id="presentation_type" name="presentation_type">
            <option value="">--Please choose an option--</option>
            <option value="poster">poster</option>
            <option value="oral">oral</option>
        </select>

        <br><br>

        <input type="file" name="zip_file" required>

        <br><br><br>

        <label for="presenter_name">Presenter name</label>
        <input type="text" name="presenter_name" id="presenter_name">

        <br><br>

        <label for="presenter_email">Presenter email</label>
        <input type="text" name="presenter_email" id="presenter_email">

        <br><br><br>

        <div id="dynamicFields"></div>
        <button type="button" id="addFieldBtn">Add author</button>

        <br><br>
        <button type="submit" value="submit">Submit</button>
    </form>
    <script>
        let count = 1;

        document.getElementById('addFieldBtn').addEventListener('click', () => {
            const container = document.getElementById('dynamicFields');

            // Create a wrapper div for label, input, and remove button
            const fieldGroup = document.createElement('div');
            fieldGroup.className = 'field-group';
            fieldGroup.id = 'fieldGroup' + count;

            // Create label
            const labelNama = document.createElement('label');
            labelNama.setAttribute('for', 'author_name_' + count);
            labelNama.textContent = 'Author name: ';

            const labelEmail = document.createElement('label');
            labelEmail.setAttribute('for', 'author_email_' + count);
            labelEmail.textContent = 'Author email: ';

            const labelAff = document.createElement('label');
            labelAff.setAttribute('for', 'author_affiliation_' + count);
            labelAff.textContent = 'Author affiliation: ';



            // Create input
            const inputNama = document.createElement('input');
            inputNama.type = 'text';
            inputNama.id = 'author_name_' + count;
            inputNama.name = 'author_name[]';

            const inputEmail = document.createElement('input');
            inputEmail.type = 'text';
            inputEmail.id = 'author_email_' + count;
            inputEmail.name = 'author_email[]';

            const inputAff = document.createElement('input');
            inputAff.type = 'text';
            inputAff.id = 'author_affiliation_' + count;
            inputAff.name = 'author_affiliation[]';



            // Create remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.textContent = 'Remove';
            removeBtn.style.marginLeft = '10px';

            // When remove button clicked, remove the entire field group
            removeBtn.addEventListener('click', () => {
            container.removeChild(fieldGroup);
            });

            // Append label, input, and remove button to the wrapper div
            fieldGroup.appendChild(labelNama);
            fieldGroup.appendChild(inputNama);
            fieldGroup.appendChild(document.createElement('br'));
            fieldGroup.appendChild(document.createElement('br'));
            fieldGroup.appendChild(labelEmail);
            fieldGroup.appendChild(inputEmail);
            fieldGroup.appendChild(document.createElement('br'));
            fieldGroup.appendChild(document.createElement('br'));
            fieldGroup.appendChild(labelAff);
            fieldGroup.appendChild(inputAff);
            fieldGroup.appendChild(document.createElement('br'));
            fieldGroup.appendChild(removeBtn);
            //this is terrible, seeing this make me want to tie THE ROPE
            fieldGroup.appendChild(document.createElement('br'));
            fieldGroup.appendChild(document.createElement('br'));
            fieldGroup.appendChild(document.createElement('br'));
            // Append wrapper div to container
            container.appendChild(fieldGroup);

            count++;
        });
    </script>
</body>
</html>