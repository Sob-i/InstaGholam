function closeModal(element) {
    element.remove();
}

function showModal(type, confirmHandler) {

    const modalContent = {
        flag: {
            title: "Flag Post",
            message: `This post will be <span class="warning-text">flagged</span> and restricted.`,
            icon: "warning",
            button: "Flag",
            className: "warning-btn"
        },

        approve: {
            title: "Approve Post",
            message: `This post will be <span class="success-text">approved</span> and visible to users.`,
            icon: "success",
            button: "Approve",
            className: "success-btn"
        }
    };


    const content = modalContent[type] || modalContent.flag;


    const modalStyles = `
    <style id="modalStyles">

    .modal-overlay {
        position: fixed;
        inset: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,.7);
        backdrop-filter: blur(6px);
        display:flex;
        align-items:center;
        justify-content:center;
        z-index:10000;
        animation:fadeIn .2s ease;
    }


    .modal-container {
        background:#1a1d23;
        border:1px solid #2d3039;
        border-radius:16px;
        width:90%;
        max-width:440px;
        overflow:hidden;
        box-shadow:
            0 20px 60px rgba(0,0,0,.5);
        animation:slideUp .3s ease;
    }


    .modal-header {
        padding:24px 24px 16px;
        border-bottom:1px solid #2d3039;
        display:flex;
        flex-direction:column;
        align-items:center;
        gap:10px;
    }


    .modal-icon {
        width:48px;
        height:48px;
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
    }


    .modal-icon.warning {
        background:rgba(245,158,11,.15);
        border:1px solid rgba(245,158,11,.3);
        color:#f59e0b;
    }


    .modal-icon.success {
        background:rgba(16,185,129,.15);
        border:1px solid rgba(16,185,129,.3);
        color:#10b981;
    }


    .modal-title {
        font-size:18px;
        font-weight:600;
        color:#e5e7eb;
        margin:0;
    }


    .modal-body {
        padding:20px 24px;
    }


    .modal-message {
        color:#9ca3af;
        font-size:14px;
        line-height:1.6;
        text-align:center;
        margin:0;
    }


    .warning-text {
        color:#f59e0b;
        font-weight:600;
    }


    .success-text {
        color:#10b981;
        font-weight:600;
    }


    .modal-footer {
        padding:16px 24px 24px;
        display:flex;
        gap:12px;
        justify-content:flex-end;
    }


    .modal-btn {
        padding:10px 24px;
        border-radius:8px;
        font-size:14px;
        font-weight:500;
        cursor:pointer;
        border:none;
        transition:.2s;
    }


    .modal-btn-cancel {
        background:#2d3039;
        color:#d1d5db;
    }


    .modal-btn-cancel:hover {
        background:#3f434e;
    }


    .modal-btn-confirm {
        color:white;
    }


    .success-btn {
        background:#10b981;
    }


    .success-btn:hover {
        background:#059669;
        box-shadow:0 4px 15px rgba(16,185,129,.3);
    }


    .warning-btn {
        background:#f59e0b;
        color:#1a1d23;
    }


    .warning-btn:hover {
        background:#d97706;
        box-shadow:0 4px 15px rgba(245,158,11,.3);
    }


    @keyframes fadeIn {
        from {
            opacity:0;
        }
        to {
            opacity:1;
        }
    }


    @keyframes slideUp {
        from {
            opacity:0;
            transform:translateY(20px) scale(.95);
        }
        to {
            opacity:1;
            transform:translateY(0) scale(1);
        }
    }


    </style>
    `;


    if (!document.getElementById("modalStyles")) {
        document.head.insertAdjacentHTML(
            "beforeend",
            modalStyles
        );
    }


    const modalContainer = document.createElement("div");

    modalContainer.className = "modal-overlay";


    modalContainer.innerHTML = `

        <div class="modal-container">


            <div class="modal-header">


                <div class="modal-icon ${content.icon}">

                    <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2">

                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>

                    </svg>

                </div>


                <h3 class="modal-title">
                    ${content.title}
                </h3>


            </div>



            <div class="modal-body">

                <p class="modal-message">
                    ${content.message}
                </p>

            </div>



            <div class="modal-footer">

                <button class="modal-btn modal-btn-cancel">
                    Cancel
                </button>


                <button class="modal-btn modal-btn-confirm ${content.className}">
                    ${content.button}
                </button>

            </div>


        </div>

    `;


    document.body.appendChild(modalContainer);



    const cancelBtn =
        modalContainer.querySelector(".modal-btn-cancel");


    cancelBtn.onclick = () => {
        closeModal(modalContainer);
    };



    const confirmBtn =
        modalContainer.querySelector(".modal-btn-confirm");


    confirmBtn.onclick = () => {

        closeModal(modalContainer);

        confirmHandler();

    };

}


export default showModal;
