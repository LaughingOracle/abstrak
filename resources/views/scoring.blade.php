<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Score Submission</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fa;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 900px;
            background: #fff;
            margin: auto;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        h3 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        p {
            text-align: center;
            color: #555;
            margin-bottom: 30px;
        }

        .form-block {
            background-color: #f9fbfd;
            border: 1px solid #dbe9f6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }

        .range-label {
            font-weight: 500;
            color: #333;
        }

        input[type="range"] {
            width: 100%;
        }

        .alert {
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 14px;
        }

        .btn-close {
            font-size: 1rem;
        }

        select {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        button[type="submit"] {
            display: block;
            background: #2980b9;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            margin: 30px auto 0 auto;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #216a94;
        }
    </style>
</head>
<body>
    <div class="container">

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
                    {!! preg_replace_callback('/name="value(\[\])?"/', function ($matches) use ($form) {
                        return 'name="forms[' . $form->id . '][value]' . ($matches[1] ?? '') . '"';
                    }, $form->html) !!}
                </div>
            @endforeach

            <label for="status" class="form-label mt-3">Final Decision</label>
            <select name="status" id="status" required>
                <option value="">-- Unselected --</option>
                <option value="passed">Passed</option>
                <option value="failed">Failed</option>
            </select>

            <button type="submit">Submit</button>
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
