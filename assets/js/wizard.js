document.addEventListener('DOMContentLoaded', function () {

    /**
     * Validate that at least ONE checkbox is checked in a step
     */
    function hasChecked(step) {
        const checkboxes = step.querySelectorAll('.mni-checkbox:not(:disabled)');
        return Array.from(checkboxes).some(cb => cb.checked);
    }

    /**
     * Sync "Select All" checkbox state
     */
    function syncSelectAll(step) {
        const selectAll = step.querySelector('.mni-select-all');
        if (!selectAll) return;

        const checkboxes = step.querySelectorAll('.mni-checkbox:not(:disabled)');
        if (!checkboxes.length) return;

        selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
    }

    /**
     * Handle Select All click
     */
    document.addEventListener('change', function (e) {

        // Select All toggled
        if (e.target.classList.contains('mni-select-all')) {

            const step = e.target.closest('.wizard-step');
            if (!step) return;

            const checkboxes = step.querySelectorAll('.mni-checkbox:not(:disabled)');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
        }

        // Individual checkbox toggled
        if (e.target.classList.contains('mni-checkbox')) {

            const step = e.target.closest('.wizard-step');
            if (!step) return;

            syncSelectAll(step);
        }
    });

    /**
     * Prevent next if invalid
     */
    document.addEventListener('click', function (e) {

        if (!e.target.classList.contains('wizard-next')) return;

        const step = e.target.closest('.wizard-step');
        if (!step) return;

        if (step.dataset.require === 'checkbox' && !hasChecked(step)) {
            e.preventDefault();
            alert('Please select at least one option.');
        }
    });

});
