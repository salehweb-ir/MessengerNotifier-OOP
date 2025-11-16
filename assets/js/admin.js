document.addEventListener("DOMContentLoaded", function() {

    const buttons = document.querySelectorAll(".mni-tab-btn");
    const tabs    = document.querySelectorAll(".mni-tab-content");

    buttons.forEach(btn => {
        btn.addEventListener("click", function () {

            // Remove active classes
            buttons.forEach(b => b.classList.remove("active"));
            tabs.forEach(t => t.classList.remove("active"));

            // Activate clicked button
            btn.classList.add("active");

            // Activate tab
            const tabId = "mni-tab-" + btn.dataset.tab;
            document.getElementById(tabId).classList.add("active");
        });
    });

});