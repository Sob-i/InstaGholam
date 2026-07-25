
    // Follow Button Toggle Script
    document.addEventListener('DOMContentLoaded', function() {
    const followButton = document.querySelector('.btn-edit.custom-a');

    if (followButton) {
    followButton.addEventListener('click', async function(e) {
    e.preventDefault();

    const button = this;
    const username = button.dataset.username;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!username) {
    console.error('Username not found in button data attribute');
    return;
}

    // Store original state in case of error
    const originalText = button.textContent;
    const wasFollowing = button.classList.contains('following');

    // Disable button during request
    button.disabled = true;
    button.textContent = 'Processing...';

    try {
    const response = await fetch(`/profile/${username}/follow`, {
    method: 'POST',
    headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrfToken,
    'Accept': 'application/json'
}
});

    const data = await response.json();

    if (data.success) {
    // Update button state
    updateButtonState(button, data.isFollowed);

    // Update ONLY followers count
    const statNums = document.querySelectorAll('.stat-num');
    statNums.forEach(statNum => {
    const statLabel = statNum.nextElementSibling;
    if (statLabel && statLabel.classList.contains('stat-label')) {
    if (statLabel.textContent.trim() === 'Followers') {
    // Add updating animation
    statNum.classList.add('updating');
    statNum.textContent = data.formatted_count || data.followersCount;
    setTimeout(() => {
    statNum.classList.remove('updating');
}, 300);
}
}
});

    // Add animation class to button
    button.classList.add(data.animation);
    setTimeout(() => {
    button.classList.remove(data.animation);
}, 1000);

    // Show success toast
    showToast(data.message, 'success');
} else {
    // Reset button to original state
    button.textContent = originalText;
    if (wasFollowing) {
    button.classList.add('following');
    button.classList.remove('not-following');
} else {
    button.classList.add('not-following');
    button.classList.remove('following');
}
    showToast(data.message || 'An error occurred', 'error');
}

} catch (error) {
    console.error('Error:', error);
    // Reset button to original state
    button.textContent = originalText;
    if (wasFollowing) {
    button.classList.add('following');
    button.classList.remove('not-following');
} else {
    button.classList.add('not-following');
    button.classList.remove('following');
}
    showToast('Network error. Please try again.', 'error');
} finally {
    button.disabled = false;
}
});

    // Add hover effect for following state
    followButton.addEventListener('mouseenter', function() {
    if (this.classList.contains('following')) {
    this.textContent = 'Unfollow';
    this.style.backgroundColor = '#ff4444';
    this.style.color = '#fff';
    this.style.borderColor = '#ff4444';
}
});

    followButton.addEventListener('mouseleave', function() {
    if (this.classList.contains('following')) {
    this.textContent = 'Following';
    this.style.backgroundColor = '';
    this.style.color = '';
    this.style.borderColor = '';
}
});
}

    function updateButtonState(button, isFollowed) {
    if (isFollowed) {
    button.textContent = 'Following';
    button.classList.add('following');
    button.classList.remove('not-following');
} else {
    button.textContent = 'Follow';
    button.classList.add('not-following');
    button.classList.remove('following');
}
}

    function showToast(message, type) {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
    console.error('Toast container not found');
    return;
}

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;

    // Style the toast
    Object.assign(toast.style, {
    padding: '12px 24px',
    marginBottom: '10px',
    borderRadius: '8px',
    color: '#fff',
    fontWeight: '500',
    fontSize: '14px',
    boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
    animation: 'fadeIn 0.3s ease-out',
    backgroundColor: type === 'success' ? '#4CAF50' : '#f44336',
    opacity: '1',
    transition: 'opacity 0.3s ease-out'
});

    toastContainer.appendChild(toast);

    // Remove after 3 seconds
    setTimeout(() => {
    toast.style.opacity = '0';
    setTimeout(() => toast.remove(), 300);
}, 3000);
}

    // Show initial toasts from session data if available
    if (window.toastMessages) {
    if (window.toastMessages.success) {
    showToast(window.toastMessages.success, 'success');
}
    if (window.toastMessages.fail) {
    showToast(window.toastMessages.fail, 'error');
}
    if (window.toastMessages.errors) {
    window.toastMessages.errors.forEach(error => {
    showToast(error, 'error');
});
}
}
});

    // Add required CSS styles
    const style = document.createElement('style');
    style.textContent = `
    @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

    @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

    @keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

    #toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

    .toast {
    min-width: 250px;
    max-width: 400px;
}

    .btn-edit.custom-a {
    transition: all 0.3s ease;
    cursor: pointer;
    padding: 8px 24px;
    border: 1px solid transparent;
    border-radius: 4px;
    font-weight: 600;
    font-size: 14px;
    min-width: 120px;
    outline: none;
}

    .btn-edit.custom-a.following {
    background-color: #efefef;
    color: #262626;
    border-color: #dbdbdb;
}

    .btn-edit.custom-a.not-following {
    background-color: #c8f04d;
    color: #262626;
    border-color: #c8f04d;
}

    .btn-edit.custom-a.following:hover {
    background-color: #ff4444;
    color: #ffffff;
    border-color: #ff4444;
}

    .btn-edit.custom-a.not-following:hover {
    background-color: #b8dd2f;
    border-color: #b8dd2f;
}

    .btn-edit.custom-a:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

    .btn-edit.custom-a.followed {
    animation: pulse 0.5s ease;
}

    .btn-edit.custom-a.unFollowed {
    animation: shake 0.5s ease;
}

    /* Counter animation */
    .stat-num {
    transition: all 0.3s ease;
    display: inline-block;
}

    .stat-num.updating {
    animation: pulse 0.3s ease;
    color: #0095f6;
}

    .post-comments::-webkit-scrollbar {
    width: 4px;
}
    .post-comments::-webkit-scrollbar-thumb {
    background: #c7c7c7;
    border-radius: 2px;
}
    `;
    document.head.appendChild(style);
