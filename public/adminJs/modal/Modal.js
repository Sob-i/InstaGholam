function closeModal(element){
    document.body.removeChild(element);
}
function showModal(type, confirmHandler){
    const flagText = `this post will be <span style="color : #f59e0b">flagged</span> and it will be restricted`;
    const modalStyles = `
<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        animation: fadeIn 0.2s ease;
    }

    .modal-container {
        background: #1a1d23;
        border: 1px solid #2d3039;
        border-radius: 16px;
        width: 90%;
        max-width: 440px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(245, 158, 11, 0.1);
        animation: slideUp 0.3s ease;
        overflow: hidden;
    }

    .modal-header {
        padding: 24px 24px 16px;
        border-bottom: 1px solid #2d3039;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .modal-icon {
        width: 48px;
        height: 48px;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        color: #f59e0b;
        transition: all 0.3s ease;
    }

    .modal-icon.danger {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.3);
        color: #ef4444;
    }

    .modal-icon.success {
        background: rgba(16, 185, 129, 0.15);
        border-color: rgba(16, 185, 129, 0.3);
        color: #10b981;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #e5e7eb;
        margin: 0;
    }

    .modal-body {
        padding: 20px 24px;
    }

    .modal-message {
        color: #9ca3af;
        font-size: 14px;
        line-height: 1.6;
        margin: 0;
        text-align: center;
    }

    .modal-footer {
        padding: 16px 24px 24px;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .modal-btn {
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        outline: none;
        font-family: inherit;
    }

    .modal-btn-cancel {
        background: #2d3039;
        color: #d1d5db;
        border: 1px solid #3f434e;
    }

    .modal-btn-cancel:hover {
        background: #3f434e;
        border-color: #525766;
        transform: translateY(-1px);
    }

    .modal-btn-confirm {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #1a1d23;
        font-weight: 600;
        border: 1px solid #f59e0b;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .modal-btn-confirm:hover {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }

    .modal-btn-confirm.danger-btn {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border-color: #ef4444;
    }

    .modal-btn-confirm.danger-btn:hover {
        background: linear-gradient(135deg, #f87171, #ef4444);
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .modal-btn-confirm.success-btn {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-color: #10b981;
    }

    .modal-btn-confirm.success-btn:hover {
        background: linear-gradient(135deg, #34d399, #10b981);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }


</style>
`;
    document.head.insertAdjacentHTML('beforeend', modalStyles);
    const modalContainer = document.createElement("div");
    modalContainer.classList.add("modal-overlay");
    modalContainer.innerHTML = `
        <div class="modal-container">
            <div class="modal-header">
                <div class="modal-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h3 class="modal-title">Confirm Action</h3>
            </div>
            <div class="modal-body">
                <p id="modalMessage" class="modal-message">
                    ${ type === "flag" && flagText }
                </p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-cancel" id="modalCancel">Cancel</button>
                <button class="modal-btn modal-btn-confirm" id="modalConfirm">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    Confirm
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modalContainer);

    const cancelBtn = document.getElementById("modalCancel");
    cancelBtn.addEventListener("click", () => closeModal(modalContainer));

    const modalConfirm = document.getElementById("modalConfirm");
    modalConfirm.addEventListener("click", () => {
        closeModal(modalContainer);
        confirmHandler();
    })
}

export default showModal;
