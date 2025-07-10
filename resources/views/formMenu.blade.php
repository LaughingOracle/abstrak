<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Form Builder</title>
  <style>
  </style>
</head>
<body>
  <h2>HTML Form Builder</h2>

  <label for="inputType">Select Input Type:</label>
  <select id="inputType">
    <option value="text">String (Text)</option>
    <option value="number">Number</option>
    <option value="range">Range</option>
    <option value="radio">Radio</option>
    <option value="checkbox">Checkbox</option>
  </select>

  <label for="labelText">Label Text:</label>
  <input type="text" id="labelText" placeholder="e.g. Enter your name">
  <input type="hidden" id="eventValue" value="{{ request('event') }}">
  <input type="hidden" id="type" value="{{ request('type') }}">
  <div id="extraOptions"></div>

  <button onclick="generateHTML()">Generate Form</button>



    <h2>Stored Forms for "{{ request('event') }}"</h2>


    @foreach($forms as $form)
        <form action="{{ route('formDelete', ['id' => $form->id]) }}" method="POST" style="margin-bottom: 1em;">
            @csrf
            @method('DELETE')

            <div class="form-block" id="form-{{ $form->id }}">
                {!! $form->html !!}
                <button type="submit" >Delete</button>
            </div>
        </form>
    @endforeach
  <script>
    const inputType = document.getElementById("inputType");
    const extraOptions = document.getElementById("extraOptions");
    

    inputType.addEventListener("change", showExtraOptions);
    showExtraOptions(); // init

    function showExtraOptions() {
      const type = inputType.value;
      extraOptions.innerHTML = "";

      if (type === "range") {
        extraOptions.innerHTML = `
          <label>Min:</label><input type="number" id="min" value="0">
          <label>Max:</label><input type="number" id="max" value="100">
        `;
      } else if (type === "radio" || type === "checkbox") {
        extraOptions.innerHTML = `
          <label>Options (comma-separated):</label>
          <input type="text" id="options" placeholder="e.g. Male,Female,Other">
          <br>
          <label>Layout:</label>
          <select id="layout">
            <option value="vertical">Vertical</option>
            <option value="horizontal">Horizontal</option>
          </select>
        `;
      }
    }

    function generateHTML() {
        const type = inputType.value;
        const label = document.getElementById("labelText").value.trim();
        const name = label.toLowerCase().replace(/\s+/g, "_");
        let html = "";

        if (!label) {
            alert("Label text is required.");
            return;
        }

        if (type === "radio" || type === "checkbox") {
            const options = document.getElementById("options").value.split(",");
            const layoutSelect = document.getElementById("layout");
            const layout = layoutSelect ? layoutSelect.value : 'vertical';
            const isHorizontal = layout === "horizontal";

            html += `<fieldset><legend>${label}</legend>`;
            options.forEach((opt, i) => {
                const val = opt.trim();
                html += `
                    <label style="${isHorizontal ? 'display:inline-block; margin-right:15px;' : 'display:block;'}">
                        <input type="${type}" name="${name}${type === 'checkbox' ? '[]' : ''}" value="${val}" id="${name}_${i}">
                        ${val}
                    </label>
                `;
            });
            html += `</fieldset>`;
        } else if (type === "range") {
            const min = document.getElementById("min").value;
            const max = document.getElementById("max").value;
            html = `<label for="${name}">${label}</label>\n<input type="range" id="${name}" name="${name}" min="${min}" max="${max}">`;
        } else {
            html = `<label for="${name}">${label}</label>\n<input type="${type}" id="${name}" name="${name}">`;
        }

        // Submit to backend
        const event = document.getElementById("eventValue").value;
        const type2 = document.getElementById("type").value;
        const label2 = document.getElementById("labelText").value;
        fetch("/formInsert", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                html: html,
                event: event,
                type: type2,
                label: label2
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("HTML saved to database!");
                location.reload();
            } else {
                alert("Failed to save.");
            }
        })
        .catch(err => {
            console.error("Error:", err);
            alert("Something went wrong.");
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const rangeInputs = document.querySelectorAll('input[type="range"]');

        rangeInputs.forEach((rangeInput, index) => {
            // Create and insert the label span
            const valueLabel = document.createElement('span');
            valueLabel.classList.add('range-label');
            valueLabel.style.marginLeft = '1em';
            valueLabel.textContent = rangeInput.value;

            // Insert the label right after the range input
            rangeInput.insertAdjacentElement('afterend', valueLabel);

            // Add event listener to update label on input
            rangeInput.addEventListener('input', function () {
                valueLabel.textContent = this.value;
            });
        });
    });

  </script>
</body>
</html>
