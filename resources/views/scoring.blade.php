<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Score Submission</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-block {
            border: 1px solid #dee2e6;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
        }
        .range-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-4">

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

        <h3>Submit Scores</h3>
        <p>Please review and fill in the forms below. Adjust any sliders to the desired value.</p>

        <form action="{{ route('score') }}" method="POST">
            @csrf

            <input type="hidden" name="event_id" value="{{ $eventId }}">
            <input type="hidden" name="abstract_paper_id" value="{{ $abstractPaperId }}">

            @foreach($forms as $form)
                <div class="form-block" id="form-{{ $form->id }}">
                    {!! preg_replace('/name="([^"]+)"/', 'name="forms[' . $form->id . '][$1]"', $form->html) !!}
                </div>
            @endforeach

            <select name="status" id="status" required>
                <option value="">-- Unselected --</option>
                <option value="passed">
                    passed
                </option>
                <option value="failed">
                    failed
                </option>
            </select>
            <br>
            <br>
            <button type="submit" class="btn text-white" style="background-color: #2980b9;">Submit</button>
        </form>
    </div>

    <!-- Script for Range Inputs -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rangeInputs = document.querySelectorAll('input[type="range"]');

            rangeInputs.forEach((rangeInput) => {
                const valueLabel = document.createElement('span');
                valueLabel.classList.add('range-label');
                valueLabel.style.marginLeft = '1em';
                valueLabel.textContent = rangeInput.value;

                rangeInput.insertAdjacentElement('afterend', valueLabel);

                rangeInput.addEventListener('input', function () {
                    valueLabel.textContent = this.value;
                });
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
