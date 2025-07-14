<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Score Submission</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fa;
            padding: 40px;
            margin: 0;
        }

        .container {
            max-width: 900px;
            background: #fff;
            margin: 0 auto;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .btn {
            background: #2980b9;
            color: #fff;
            padding: 10px 20px;
            border: none;
            font-size: 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #216a94;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .btn-close {
            background: none;
            border: none;
            float: right;
            font-size: 18px;
            cursor: pointer;
        }

        .range-label {
            font-weight: 500;
            color: #333;
        }

        input[type="range"] {
            width: 100%;
        }

        button[type="submit"] {
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">&times;</button>
            </div>
        @endif

        <h3>Submit Scores</h3>
        <p>Please review and fill in the forms below. Adjust any sliders to the desired value.</p>

        <form action="{{ route('score2') }}" method="POST">
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

            <button type="submit" class="btn">Submit</button>
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

    <!-- Optional Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
