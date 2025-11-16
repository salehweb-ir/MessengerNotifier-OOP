document.addEventListener("DOMContentLoaded", () => {

// Save selected messengers in JS variable
function getSelectedMessengers() {
    const checks = document.querySelectorAll('.mni-messenger-check:checked');
    let list = [];
    checks.forEach(c => list.push(c.value));
    return list;
}


    /* --------------------------
       STEP NAVIGATION
    --------------------------- */
    function showStep(step) {
        document.querySelectorAll(".mni-step").forEach(s => s.style.display = "none");
        document.querySelector('.mni-step[data-step="' + step + '"]').style.display = "block";

        document.querySelectorAll(".mni-steps li").forEach(li => li.classList.remove("active"));
        document.querySelector('.mni-steps li[data-step="' + step + '"]').classList.add("active");
    }

    let step = 1;
    document.querySelectorAll(".mni-next-step").forEach(btn => {
        btn.onclick = () => { step++; showStep(step); };
    });
    document.querySelectorAll(".mni-prev-step").forEach(btn => {
        btn.onclick = () => { step--; showStep(step); };
    });

    showStep(1);

    /* --------------------------
       ENSURE AT LEAST ONE MESSENGER CHECKED
    --------------------------- */
    const checks = document.querySelectorAll(".mni-messenger-check");

    function enforceOneChecked() {
        const checked = document.querySelectorAll(".mni-messenger-check:checked");
        if (checked.length === 1) {
            checked[0].disabled = true;
        } else {
            checks.forEach(c => c.disabled = false);
        }
    }

    checks.forEach(c => c.addEventListener("change", enforceOneChecked));
    enforceOneChecked();


    /* --------------------------
       TABS
    --------------------------- */
    const tabs = document.querySelectorAll(".mni-tab-header li");
    const contents = document.querySelectorAll(".mni-tab-content");

    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            tabs.forEach(t => t.classList.remove("active"));
            contents.forEach(c => c.style.display = "none");

            tab.classList.add("active");
            document.querySelector('.mni-tab-content[data-tab="' + tab.dataset.tab + '"]').style.display = "block";
        });
    });

    if (tabs.length > 0) tabs[0].click();

});
