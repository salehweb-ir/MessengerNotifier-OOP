document.addEventListener('DOMContentLoaded', function () {

    /* ======================================================
     * Helpers
     * ====================================================== */
    const qs  = (s, p = document) => p.querySelector(s);
    const qsa = (s, p = document) => [...p.querySelectorAll(s)];

    const steps = qsa('.mni-wizard-step');
    const nextBtn = qs('#mni-wizard-next');
    const prevBtn = qs('#mni-wizard-prev');

    let currentStep = parseInt(localStorage.getItem('mni_wizard_step') || '0', 10);

    /* ======================================================
     * Wizard Navigation
     * ====================================================== */
    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle('is-active', i === index);
        });

        prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
        nextBtn.textContent = index === steps.length - 1 ? 'Finish' : 'Next';

        localStorage.setItem('mni_wizard_step', index);
        currentStep = index;
        validateStep();
    }

    nextBtn.addEventListener('click', function () {
        if (!validateStep()) return;

        if (currentStep < steps.length - 1) {
            showStep(currentStep + 1);
        } else {
            qs('#mni-wizard-form').submit();
        }
    });

    prevBtn.addEventListener('click', function () {
        if (currentStep > 0) {
            showStep(currentStep - 1);
        }
    });

    /* ======================================================
     * Validation
     * ====================================================== */
    function validateStep() {
        const step = steps[currentStep];
        const requiredGroup = step.dataset.require;

        if (!requiredGroup) return true;

        const checked = qsa(`.${requiredGroup}:checked`).length > 0;
        nextBtn.disabled = !checked;
        return checked;
    }

    /* ======================================================
     * Checkbox Utilities
     * ====================================================== */
    function enforceOneChecked(checkboxes) {
        const checked = checkboxes.filter(cb => cb.checked);

        checkboxes.forEach(cb => cb.disabled = false);

        if (checked.length === 1) {
            checked[0].disabled = true;
        }
    }

    function syncSelectAll(selectAll, checkboxes) {
        const checkedCount = checkboxes.filter(cb => cb.checked).length;
        selectAll.checked = checkedCount === checkboxes.length;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
    }

    /* ======================================================
     * Messengers
     * ====================================================== */
    const messengerAll = qs('#mni-check-all-messengers');
    const messengers  = qsa('.mni-messenger-checkbox');

    messengers.forEach(cb => {
        cb.addEventListener('change', () => {
            enforceOneChecked(messengers);
            syncSelectAll(messengerAll, messengers);
            validateStep();
            saveState();
        });
    });

    if (messengerAll) {
        messengerAll.addEventListener('change', () => {
            if (!messengerAll.checked) {
                messengers.forEach((cb, i) => {
                    cb.checked = i === 0;
                    cb.disabled = i === 0;
                });
            } else {
                messengers.forEach(cb => {
                    cb.checked = true;
                    cb.disabled = false;
                });
            }
            validateStep();
            saveState();
        });
    }

    enforceOneChecked(messengers);
    syncSelectAll(messengerAll, messengers);

    /* ======================================================
     * Actions (WooCommerce aware)
     * ====================================================== */
    const actionAll = qs('#mni-check-all-actions');
    const actions  = qsa('.mni-action-checkbox');
    const wcActive = document.body.classList.contains('mni-wc-active');

    actions.forEach(cb => {
        if (!wcActive && cb.dataset.requires === 'woocommerce') {
            cb.checked = false;
            cb.disabled = true;
            cb.closest('label').classList.add('is-disabled');
        }

        cb.addEventListener('change', () => {
            enforceOneChecked(actions.filter(a => !a.disabled));
            syncSelectAll(actionAll, actions.filter(a => !a.disabled));
            validateStep();
            saveState();
        });
    });

    if (actionAll) {
        actionAll.addEventListener('change', () => {
            const enabled = actions.filter(a => !a.disabled);

            if (!actionAll.checked) {
                enabled.forEach((cb, i) => {
                    cb.checked = i === 0;
                    cb.disabled = i === 0;
                });
            } else {
                enabled.forEach(cb => {
                    cb.checked = true;
                    cb.disabled = false;
                });
            }

            validateStep();
            saveState();
        });
    }

    /* ======================================================
     * Local Storage Persistence
     * ====================================================== */
    function saveState() {
        const data = {
            messengers: messengers.filter(c => c.checked).map(c => c.value),
            actions: actions.filter(c => c.checked).map(c => c.value)
        };
        localStorage.setItem('mni_wizard_data', JSON.stringify(data));
    }

    function restoreState() {
        const data = JSON.parse(localStorage.getItem('mni_wizard_data') || '{}');

        messengers.forEach(cb => {
            cb.checked = data.messengers?.includes(cb.value);
        });

        actions.forEach(cb => {
            if (!cb.disabled) {
                cb.checked = data.actions?.includes(cb.value);
            }
        });
    }

    restoreState();
    showStep(currentStep);

});
