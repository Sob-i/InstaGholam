
    const reportModal = document.getElementById("reportModal");
    const reportForm = document.getElementById("reportForm");

    document.addEventListener("click", function (e) {

    const btn = e.target.closest(".report-btn");

    if (!btn) return;

    document.getElementById("reported-user-id").value = btn.dataset.id;
    document.getElementById("report-type").value = btn.dataset.type;
    document.getElementById("reporter-uid").value = btn.dataset.uid;

    reportModal.style.display = "flex";
});

    document.getElementById("closeReportModal").onclick = () => {
    reportModal.style.display = "none";
};

    document.getElementById("cancelReport").onclick = () => {
    reportModal.style.display = "none";
};

    window.onclick = function(e){
    if(e.target === reportModal){
    reportModal.style.display = "none";
}
};

    reportForm.addEventListener("submit", async function(e){

    e.preventDefault();

    const uid = document.getElementById("reporter-uid").value;

    const formData = new FormData(reportForm);

    try{

    const response = await fetch(`/report/${uid}`,{

    method:"POST",
    headers:{
    "X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').content,
    "Accept":"application/json"
},
    body:formData

});

    const data = await response.json();

    if(data.status){

    alert(data.message);

    reportModal.style.display = "none";

    reportForm.reset();

}else{

    alert(data.message);

}

}catch(error){

    console.error(error);

    alert("Something went wrong.");

}

});
