document.addEventListener('DOMContentLoaded', function () {

    /* ---------------- Tabs ---------------- */

    const tabs     = document.querySelectorAll('.nav-tab');
    const sections = document.querySelectorAll('.mni-settings-section');

    function activateTab(targetId) {
        tabs.forEach(tab => {
            tab.classList.toggle(
                'nav-tab-active',
                tab.getAttribute('href') === '#' + targetId
            );
        });

        sections.forEach(section => {
            section.style.display =
                section.id === targetId ? 'block' : 'none';
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const target = this.getAttribute('href').replace('#', '');
            activateTab(target);
        });
    });

    // تب اول پیش‌فرض
    activateTab('contact');

    /* ---------------- Messenger toggle ---------------- */

    const messengerCheckboxes = document.querySelectorAll('.mni-messenger-toggle');
    const configBlocks        = document.querySelectorAll('.mni-messenger-config');

    function updateMessengerConfigs() {
        const active = new Set();

        messengerCheckboxes.forEach(cb => {
            if (cb.checked) {
                active.add(cb.dataset.messenger);
            }
        });

        configBlocks.forEach(block => {
            const id = block.dataset.messenger;
            block.style.display = active.has(id) ? 'block' : 'none';
        });
    }

    // وضعیت اولیه
    updateMessengerConfigs();

    // تغییرات داینامیک
    messengerCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateMessengerConfigs);
    });

});
