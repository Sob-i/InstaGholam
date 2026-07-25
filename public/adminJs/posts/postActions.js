import showModal from "../modal/Modal.js";
import showToast from "../toast/Toast.js";

const postFlagBtn = document.getElementById("post-flag-btn");

function loaderHandler(action, id){
    const postPgActionsContainer = document.getElementById("post-pg-actions-container");
    const containerChildren = postPgActionsContainer.children;
    const childrenArray = [...containerChildren];

    if(action === "show"){
        childrenArray.forEach((child) => {
            child.style.display = "none";
        })
        const loader = document.createElement("div");
        loader.style.width = "100%";
        loader.id = "loader" + id;
        loader.innerHTML = `
            <div class="pg-btn">
                <span style="font-weight: bold; color: #4df0a8">Processing...</span>
            </div>
        `;
        postPgActionsContainer.appendChild(loader);
    }
    else if(action === "close"){
        const loader  = document.getElementById("loader" + id);
        console.log(loader);
        loader.remove();
        childrenArray.forEach((child) => {
            child.style.display = "block";
        })
    }
}

function removeCard(id){
    const card = document.getElementById("postCard" + id);
    card.remove();
}
async function flagHandler(dataPostId){
    console.log("flagHandler activated : " +  dataPostId);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    loaderHandler("show", dataPostId);
    try{
        const res = await fetch(`/admin/posts/statusToFlagged/${dataPostId}`, {
            method : "PUT",
            headers : {
                "content-type" : "application/json",
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            }
        });
        const data = await res.json();
        console.log(data);
        if(data.success){
            const flaggedMiniStatValue = document.getElementById("flagged-mini-stat-value");
            flaggedMiniStatValue.textContent = Number(flaggedMiniStatValue.textContent) + data.flaggedPostCount;
            removeCard(dataPostId);
            showToast(data.message, "success");
        }
        else{
            loaderHandler("close", dataPostId)
            showToast(data.message, "error");
        }
    }
    catch(err){
        console.log("flagHandler error : " + err.message);
    }
}

postFlagBtn.addEventListener("click", () => {
    const dataPostId = postFlagBtn.dataset.postId;
    showModal("flag",  () => flagHandler(dataPostId));
})


