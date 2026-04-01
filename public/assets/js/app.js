document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("shipmentSearch");
    const shipmentRows = document.querySelectorAll(".shipment-row");
    const noMatchRow = document.getElementById("noMatchRow");

    if (searchInput && shipmentRows.length > 0) {
        searchInput.addEventListener("input", () => {
            const keyword = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            shipmentRows.forEach((row) => {
                const tracking = row.dataset.tracking || "";

                if (tracking.includes(keyword)) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            });

            if (noMatchRow) {
                noMatchRow.style.display = visibleCount === 0 ? "" : "none";
            }
        });
    }

    const forms = document.querySelectorAll("form");

    forms.forEach((form) => {
        form.addEventListener("submit", () => {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.dataset.originalText = submitButton.textContent;
                submitButton.textContent = "Processing...";
            }
        });
    });
});