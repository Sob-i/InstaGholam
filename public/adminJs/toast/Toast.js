function showToast(message, type = 'success'){
    const toastStyles = `
    <style>
    .toast-notification {
        position: fixed;
        top: 24px;
        right: 24px;
        padding: 16px 24px;
        border-radius: 12px;
        color: #e5e7eb;
        font-weight: 500;
        font-size: 14px;
        z-index: 10001;
        animation: slideInRight 0.3s ease;
        max-width: 400px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        gap: 12px;
        backdrop-filter: blur(10px);
        border: 1px solid;
    }

    .toast-success {
        background: rgba(16, 185, 129, 0.15);
        border-color: rgba(16, 185, 129, 0.3);
        backdrop-filter: blur(20px);
    }

    .toast-success svg {
        color: #10b981;
    }

    .toast-error {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.3);
        backdrop-filter: blur(20px);
    }

    .toast-error svg {
        color: #ef4444;
    }

    .loading-spinner {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.6s linear infinite;
        margin-right: 6px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @keyframes slideInRight {
        from {
            transform: translateX(120%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(120%);
            opacity: 0;
        }
    }
    </style>
    `;
    document.head.insertAdjacentHTML('beforeend', toastStyles);
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;

    const icon = type === 'success'
        ? `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                 <polyline points="20 6 9 17 4 12"></polyline>
               </svg>`
        : `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                 <circle cx="12" cy="12" r="10"></circle>
                 <line x1="15" y1="9" x2="9" y2="15"></line>
                 <line x1="9" y1="9" x2="15" y2="15"></line>
               </svg>`;

    toast.innerHTML = `${icon}<span>${message}</span>`;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }, 3000);
}

export default showToast;
