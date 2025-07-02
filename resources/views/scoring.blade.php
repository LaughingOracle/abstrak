<form action="{{ route('score') }}" method="POST">
    @csrf

    <input type="hidden" name="event_id" value="{{ $eventId }}">
    <input type="hidden" name="abstract_paper_id" value="{{ $abstractPaperId }}">

    @foreach($forms as $form)
        <div class="form-block" id="form-{{ $form->id }}">
            {!! preg_replace('/name="([^"]+)"/', 'name="forms[' . $form->id . '][$1]"', $form->html) !!}
        </div>
    @endforeach

    <button type="submit">Submit All Forms</button>
</form>

<script>
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
