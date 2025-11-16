document.addEventListener("DOMContentLoaded", function () {

    // Steps
    var currentStep = 1;
    var totalSteps = document.querySelectorAll('.mni-step').length || 3;

    function showStep(n) {
        document.querySelectorAll('.mni-step').forEach(function (el) {
            el.style.display = (parseInt(el.dataset.step, 10) === n) ? '' : 'none';
        });
        currentStep = n;
    }

    // get checked messengers
    function getSelectedMessengers() {
        var list = [];
        document.querySelectorAll('.mni-messenger-check:checked').forEach(function (ch) {
            list.push(ch.value);
        });
        return list;
    }

    // enforce at least one messenger (disable the last checked)
    function enforceOneChecked() {
        var checks = Array.from(document.querySelectorAll('.mni-messenger-check'));
        var checked = checks.filter(function (c) { return c.checked; });
        checks.forEach(function (c) { c.disabled = false; });
        if (checked.length === 1) {
            checked[0].disabled = true;
        }
    }

    // when moving to step 3, store selected messengers in hidden field and build tabs
    function buildMessengerTabsFromSelection() {
        var list = getSelectedMessengers();
        var hidden = document.getElementById('mni_selected_messengers');
        hidden.value = JSON.stringify(list);

        var container = document.getElementById('mni_messenger_tabs_container');
        if (!container) return;

        // remove existing dynamic panels
        // But keep DB fallback panels as baseline; we will hide those not selected.
        // First hide all panels
        container.querySelectorAll('.mni-messenger-panel').forEach(function (panel) {
            panel.style.display = 'none';
        });

        // For every selected messenger, try to find an existing panel. If not exist, create one.
        list.forEach(function (m) {
            var panel = container.querySelector('.mni-messenger-panel[data-msgr="' + m + '"]');
            if (panel) {
                panel.style.display = 'block';
                return;
            }

            // create dynamic panel (no saved values)
            var panelEl = document.createElement('div');
            panelEl.className = 'mni-messenger-panel';
            panelEl.dataset.msgr = m;

            var title = document.createElement('h3');
            title.textContent = m.charAt(0).toUpperCase() + m.slice(1) + ' Settings';
            panelEl.appendChild(title);

            var labelToken = document.createElement('label');
            labelToken.textContent = 'API Token';
            panelEl.appendChild(labelToken);

            var inputToken = document.createElement('input');
            inputToken.type = 'text';
            inputToken.name = 'messenger_settings[' + m + '][token]';
            inputToken.className = 'mni-input';
            panelEl.appendChild(inputToken);

            var labelChannel = document.createElement('label');
            labelChannel.textContent = 'Channel ID';
            panelEl.appendChild(labelChannel);

            var inputChannel = document.createElement('input');
            inputChannel.type = 'text';
            inputChannel.name = 'messenger_settings[' + m + '][channel]';
            inputChannel.className = 'mni-input';
            panelEl.appendChild(inputChannel);

            var labelTest = document.createElement('label');
            labelTest.textContent = 'Test Message (optional)';
            panelEl.appendChild(labelTest);

            var textarea = document.createElement('textarea');
            textarea.name = 'messenger_settings[' + m + '][test]';
            textarea.className = 'mni-textarea';
            panelEl.appendChild(textarea);

            var p = document.createElement('p');
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'button mni-test-api';
            btn.dataset.msgr = m;
            btn.textContent = 'Test API';
            p.appendChild(btn);

            var span = document.createElement('span');
            span.className = 'mni-test-result';
            span.id = 'mni-test-' + m;
            p.appendChild(span);

            panelEl.appendChild(p);

            container.appendChild(panelEl);
        });
    }

    // init enforce
    document.querySelectorAll('.mni-messenger-check').forEach(function (c) {
        c.addEventListener('change', enforceOneChecked);
    });
    enforceOneChecked();

    // Next buttons
    document.querySelectorAll('.mni-next-step').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (currentStep === 1) {
                var sel = getSelectedMessengers();
                if (!sel || sel.length === 0) {
                    alert('Please select at least one messenger.');
                    return;
                }
                // prepare step 3 content
                buildMessengerTabsFromSelection();
            }
            if (currentStep < totalSteps) showStep(currentStep + 1);
            window.scrollTo(0,0);
        });
    });

    // Prev buttons
    document.querySelectorAll('.mni-prev-step').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (currentStep > 1) showStep(currentStep - 1);
            window.scrollTo(0,0);
        });
    });

    // Test API click (delegated)
    document.addEventListener('click', function (e) {
        if (e.target && e.target.matches('.mni-test-api')) {
            var msgr = e.target.dataset.msgr;
            var panel = document.querySelector('.mni-messenger-panel[data-msgr="' + msgr + '"]');
            if (!panel) return;
            var token = panel.querySelector('input[name="messenger_settings[' + msgr + '][token]"]')?.value || '';
            var channel = panel.querySelector('input[name="messenger_settings[' + msgr + '][channel]"]')?.value || '';
            var testmsg = panel.querySelector('textarea[name="messenger_settings[' + msgr + '][test]"]')?.value || '';

            var resultEl = document.getElementById('mni-test-' + msgr);
            if (resultEl) resultEl.textContent = 'Testing...';

            // Send AJAX to admin-ajax endpoint (wp_ajax_mni_free_test_api should exist)
            var data = new FormData();
            data.append('action', 'mni_free_test_api');
            data.append('nonce', mniWizard.nonce); // we will localize this below
            data.append('messenger', msgr);
            data.append('token', token);
            data.append('channel', channel);
            data.append('message', testmsg);

            fetch(mniWizard.ajaxurl, {
                method: 'POST',
                credentials: 'same-origin',
                body: data
            })
            .then(function(res){ return res.json(); })
            .then(function(json){
                if ( json && json.success ) {
                    resultEl.textContent = json.data.message || 'OK';
                } else {
                    resultEl.textContent = (json && json.data && json.data.message) ? json.data.message : 'Error';
                }
            })
            .catch(function(){
                resultEl.textContent = 'AJAX error';
            });
        }
    });

    // initial show
    showStep(1);
});
