document.addEventListener('DOMContentLoaded', () => {

    const steps = document.querySelectorAll('.mni-step');
    let currentStep = 0;

    const showStep = index => {
        steps.forEach((s, i) => {
            s.classList.toggle('is-active', i === index);
        });
    };

    const validateStep = step => {
        const checkboxes = step.querySelectorAll('input[type="checkbox"]:not(:disabled)');
        return [...checkboxes].some(cb => cb.checked);
    };

    const updateNextButton = step => {
        const btn = step.querySelector('.mni-next-step');
        if (!btn) return;
        btn.disabled = !validateStep(step);
    };

    // Step navigation
    document.addEventListener('click', e => {

        if (e.target.classList.contains('mni-next-step')) {
            currentStep++;
            showStep(currentStep);
        }

        if (e.target.classList.contains('mni-prev-step')) {
            currentStep--;
            showStep(currentStep);
        }
    });

    // Checkbox validation
    document.querySelectorAll('.mni-step').forEach(step => {
        step.addEventListener('change', () => updateNextButton(step));
    });

    // Select all messengers
    const selectAllMessengers = document.getElementById('mni-select-all-messengers');
    if (selectAllMessengers) {
        selectAllMessengers.addEventListener('change', () => {
            document.querySelectorAll('.mni-messenger-checkbox').forEach(cb => {
                cb.checked = selectAllMessengers.checked;
            });
            updateNextButton(steps[0]);
        });
    }

    // Select all actions
    const selectAllActions = document.getElementById('mni-select-all-actions');
    if (selectAllActions) {
        selectAllActions.addEventListener('change', () => {
            document.querySelectorAll('.mni-action-checkbox:not(:disabled)').forEach(cb => {
                cb.checked = selectAllActions.checked;
            });
            updateNextButton(steps[1]);
        });
    }

    // Initial state
    showStep(1);
});
