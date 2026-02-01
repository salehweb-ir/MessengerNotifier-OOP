document.addEventListener('DOMContentLoaded', function () {

    const steps = document.querySelectorAll('.wizard-step');

    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle('active', i === index);
        });
    }

    function hasChecked(step) {
        const boxes = step.querySelectorAll('.mni-checkbox:not(:disabled)');
        return Array.from(boxes).some(cb => cb.checked);
    }

    function syncSelectAll(step) {
        const selectAll = step.querySelector('.mni-select-all');
        if (!selectAll) return;

        const boxes = step.querySelectorAll('.mni-checkbox:not(:disabled)');
        selectAll.checked = boxes.length > 0 &&
            Array.from(boxes).every(cb => cb.checked);
    }

    document.addEventListener('change', function (e) {

        // Select All
        if (e.target.classList.contains('mni-select-all')) {
            const step = e.target.closest('.wizard-step');
            step.querySelectorAll('.mni-checkbox:not(:disabled)')
                .forEach(cb => cb.checked = e.target.checked);
        }

        // Individual checkbox
        if (e.target.classList.contains('mni-checkbox')) {
            const step = e.target.closest('.wizard-step');
            syncSelectAll(step);
        }
    });

    document.addEventListener('click', function (e) {

        if (e.target.classList.contains('wizard-next')) {
            const step = e.target.closest('.wizard-step');

            if (step.dataset.require === 'checkbox' && !hasChecked(step)) {
                alert('Please select at least one option.');
                return;
            }

            showStep([...steps].indexOf(step) + 1);
        }

        if (e.target.classList.contains('wizard-prev')) {
            const step = e.target.closest('.wizard-step');
            showStep([...steps].indexOf(step) - 1);
        }
    });

});
