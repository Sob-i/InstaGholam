import showModal from "../modal/Modal.js";
import showToast from "../toast/Toast.js";

function loaderHandler(action, id) {
    const container = document.getElementById("post-pg-actions-container");

    if (!container) return;

    const children = [...container.children];

    if (action === "show") {
        children.forEach(child => child.style.display = "none");

        const loader = document.createElement("div");
        loader.id = `loader${id}`;
        loader.style.width = "100%";
        loader.innerHTML = `
            <div class="pg-btn">
                <span style="font-weight:bold;color:#4df0a8">
                    Processing...
                </span>
            </div>
        `;

        container.appendChild(loader);

    } else {

        const loader = document.getElementById(`loader${id}`);

        if (loader) loader.remove();

        children.forEach(child => child.style.display = "block");
    }
}

function removeCard(id) {
    const card = document.getElementById(`postCard${id}`);

    if (card) card.remove();
}

async function flagHandler(dataPostId) {

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    loaderHandler("show", dataPostId);

    try {

        const res = await fetch(`/admin/posts/statusToFlagged/${dataPostId}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            }
        });

        const data = await res.json();

        if (data.success) {

            const flaggedMiniStatValue = document.getElementById("flagged-mini-stat-value");

            if (flaggedMiniStatValue) {
                flaggedMiniStatValue.textContent =
                    Number(flaggedMiniStatValue.textContent) + data.flaggedPostCount;
            }

            removeCard(dataPostId);

            showToast(data.message, "success");

        } else {

            loaderHandler("close", dataPostId);
            showToast(data.message, "error");

        }

    } catch (err) {

        loaderHandler("close", dataPostId);
        showToast("Something went wrong.", "error");

    }
}

document.addEventListener("click", (e) => {

    const btn = e.target.closest("#post-flag-btn");

    if (!btn) return;

    const dataPostId = btn.dataset.postId;

    showModal("flag", () => flagHandler(dataPostId));

});
