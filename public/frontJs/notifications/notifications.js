document.addEventListener("DOMContentLoaded", () => {
    const tabs = document.querySelectorAll(".filter-tabs .ftab");
    const sections = document.querySelectorAll(".notif-section");

    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            // Active tab
            tabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");

            const filter = tab.textContent.trim().toLowerCase();

            sections.forEach(section => {
                const items = section.querySelectorAll(".notif-item");

                items.forEach(item => {
                    let type = "unknown";

                    if (item.querySelector(".badge-heart")) {
                        type = "likes";
                    } else if (item.querySelector(".badge-comment")) {
                        type = "comments";
                    }else if (item.querySelector(".badge-reply")) {
                        type = "replies";
                    } else if (item.querySelector(".badge-follow") || item.querySelector(".custom-a")) {
                        type = "follows";
                    } else if (item.querySelector(".badge-tag")) {
                        type = "mentions";
                    }

                    if (filter === "all") {
                        item.style.display = "";
                    } else {
                        item.style.display = (type === filter) ? "" : "none";
                    }
                });

                // Hide section if it has no visible notifications
                const visible = [...section.querySelectorAll(".notif-item")]
                    .some(item => item.style.display !== "none");

                section.style.display = visible ? "" : "none";
            });
        });
    });
});
