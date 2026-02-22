document.addEventListener('DOMContentLoaded', function () {

    const steps = document.querySelectorAll('.wizard-step');

    function showStep(i) {
        steps.forEach((s, index) => s.classList.toggle('active', index === i));
    }

    function hasChecked(step) {
        return [...step.querySelectorAll('.mni-checkbox')]
            .some(cb => cb.checked);
    }

    function syncSelectAll(step) {
        const selectAll = step.querySelector('.mni-select-all');
        if (!selectAll) return;

        const boxes = step.querySelectorAll('.mni-checkbox');
        selectAll.checked = boxes.length &&
            [...boxes].every(cb => cb.checked);
    }

    document.addEventListener('change', e => {

        if (e.target.classList.contains('mni-select-all')) {
            const step = e.target.closest('.wizard-step');
            step.querySelectorAll('.mni-checkbox')
                .forEach(cb => cb.checked = e.target.checked);
        }

        if (e.target.classList.contains('mni-checkbox')) {
            syncSelectAll(e.target.closest('.wizard-step'));
        }
    });

    document.addEventListener('click', e => {

        if (e.target.classList.contains('wizard-next')) {
            const step = e.target.closest('.wizard-step');

            if (step.dataset.require === 'checkbox' && !hasChecked(step)) {
                alert('Select at least one option.');
                return;
            }

            // Build messenger configs before step 4
            if (step.dataset.step === "2") {
                buildMessengerConfigs();
            }

            showStep([...steps].indexOf(step) + 1);
        }

        if (e.target.classList.contains('wizard-prev')) {
            const step = e.target.closest('.wizard-step');
            showStep([...steps].indexOf(step) - 1);
        }
    });
    
    /* =========================
 * PREPARE DATA BEFORE SUBMIT
 * ========================= */
document.getElementById('mni-wizard-form').addEventListener('submit', function () {

    // Messengers
    const messengers = [...document.querySelectorAll('.mni-messenger-check:checked')]
        .map(cb => cb.value);

    document.getElementById('mni_messengers').value =
        JSON.stringify(messengers);

    // Actions
    const actions = [...document.querySelectorAll('[name="enabled_actions[]"]:checked')]
        .map(cb => cb.value);

    document.getElementById('mni_actions').value =
        JSON.stringify(actions);
});


    function buildMessengerConfigs() {
        const container = document.getElementById('mni-messenger-configs');
        container.innerHTML = '';

        document.querySelectorAll('.mni-messenger:checked').forEach(cb => {
            const id = cb.value;

            container.insertAdjacentHTML('beforeend', `
                <fieldset style="margin-bottom:20px">
                    <legend><strong>${id} Settings</strong></legend>

                    <label>Token<br>
                        <input type="text" name="settings[config][${id}][token]" style="width:100%">
                    </label><br><br>

                    <label>Channel ID<br>
                        <input type="text" name="settings[config][${id}][channel]" style="width:100%">
                    </label><br><br>

                    <button type="button" onclick="testMessenger('${id}')">
                        🧪 Test Connection
                    </button>
                </fieldset>
            `);
        });
    }
});

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('mni-test-btn')) {;

        const box = e.target.closest('.mni-config-box');
        const messenger = box.dataset.messenger;
        const token = box.querySelector('.mni-token').value;
        const channel = box.querySelector('.mni-channel').value;
        const resultEl = box.querySelector('.mni-test-result');

        resultEl.textContent = 'Sending...';

        fetch(mni-Wizard.ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'mni_free_test_messenger',
                messenger: messenger,
                token: token,
                channel: channel,
                nonce: mniWizard.nonce
            })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                resultEl.textContent = '✅ Success';
            } else {
                resultEl.textContent = '❌ ' + (res.data || 'Error');
            }
        })
        .catch(() => {
            resultEl.textContent = '❌ Network error';
        });
    }});

    function testMessenger(id) {
        const token   = document.querySelector(`[name="settings[config][${id}][token]"]`).value;
        const channel = document.querySelector(`[name="settings[config][${id}][channel]"]`).value;

        const data = new FormData();
        data.append('action', 'mni_test_messenger');
        data.append('messenger', id);
        data.append('config[token]', token);
        data.append('config[channel]', channel);

        fetch(ajaxurl, {
            method: 'POST',
            body: data
        })
        .then(res => res.json())
        .then(res => {
            alert(res.success ? '✅ اتصال موفق ' : '❌ خطا: ' + res.data.message);
        });
    }
