
    document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const cfItems = document.querySelectorAll('.cf-item');
    const cfList = document.getElementById('cfList');
    const cfEmpty = document.getElementById('cfEmpty');
    const cfCount = document.getElementById('cfCount');
    const bigCount = document.getElementById('bigCount');

    // Store original count of close friends (items with star icon)
    let originalCloseFriendsCount = document.querySelectorAll('.cf-item .cf-toggle.in-list svg polygon').length;

    searchInput.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase().trim();
    let visibleCount = 0;
    let visibleCloseFriendsCount = 0;

    cfItems.forEach(item => {
    const name = item.querySelector('.cf-name').textContent.toLowerCase();
    const handle = item.querySelector('.cf-handle').textContent.toLowerCase().replace('@ ', '');

    if (searchTerm === '' || name.includes(searchTerm) || handle.includes(searchTerm)) {
    item.style.display = 'flex';
    visibleCount++;

    // Count if this is a close friend (has star polygon in button)
    const toggleButton = item.querySelector('.cf-toggle');
    if (toggleButton && toggleButton.querySelector('svg polygon')) {
    visibleCloseFriendsCount++;
}
} else {
    item.style.display = 'none';
}
});

    // Update counts
    if (searchTerm === '') {
    // Show original counts when search is empty
    cfCount.textContent = originalCloseFriendsCount;
    bigCount.textContent = originalCloseFriendsCount;

    // Hide empty state if there are items
    if (cfItems.length > 0) {
    cfEmpty.style.display = 'none';
}
} else {
    // Update counts based on filtered results
    cfCount.textContent = visibleCloseFriendsCount;
    bigCount.textContent = visibleCloseFriendsCount;

    // Show empty state if no results found
    if (visibleCount === 0) {
    cfEmpty.style.display = 'flex';
    cfEmpty.querySelector('p').innerHTML = 'No followers match your search.<br/>Try a different name or username.';
} else {
    cfEmpty.style.display = 'none';
}
}
});

    // Initial setup - hide empty state if there are followers
    if (cfItems.length > 0) {
    cfEmpty.style.display = 'none';
} else {
    cfEmpty.style.display = 'flex';
}

    // Update original count based on actual close friends
    updateOriginalCount();

    function updateOriginalCount() {
    const closeFriendsItems = document.querySelectorAll('.cf-item .cf-toggle svg polygon');
    const count = closeFriendsItems.length;
    originalCloseFriendsCount = count;
    cfCount.textContent = count;
    bigCount.textContent = count;
}

    // Optional: Update counts when toggling close friends (if you add that functionality later)
    document.addEventListener('click', function(e) {
    if (e.target.closest('.cf-toggle')) {
    // Small delay to let DOM update if you add toggle functionality
    setTimeout(updateOriginalCount, 100);
}
});
});


    document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
    document.querySelector('input[name="_token"]')?.value;

    const currentUsername = '{{ Auth::user()->username }}';

    // Create toast container
    const toastContainer = document.createElement('div');
    toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        `;
    document.body.appendChild(toastContainer);

    // Toast function
    function showToast(message, isSuccess = true) {
    const toast = document.createElement('div');
    toast.style.cssText = `
                background: ${isSuccess ? '#4CAF50' : '#f44336'};
                color: white;
                padding: 16px 24px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                font-size: 14px;
                font-weight: 500;
                min-width: 250px;
                transform: translateX(100%);
                transition: transform 0.3s ease-in-out;
                display: flex;
                align-items: center;
                gap: 10px;
            `;

    // Add icon based on success/failure
    const icon = document.createElement('span');
    icon.innerHTML = isSuccess ? '✓' : '✕';
    icon.style.cssText = `
                font-weight: bold;
                font-size: 16px;
            `;

    toast.appendChild(icon);
    toast.appendChild(document.createTextNode(message));
    toastContainer.appendChild(toast);

    // Animate in
    setTimeout(() => {
    toast.style.transform = 'translateX(0)';
}, 100);

    // Remove after 3 seconds
    setTimeout(() => {
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => {
    toast.remove();
}, 300);
}, 3000);
}

    // Handle toggle button clicks
    document.addEventListener('click', async function(e) {
    const toggleButton = e.target.closest('.cf-toggle');
    if (!toggleButton) return;

    e.preventDefault();

    const friendId = toggleButton.getAttribute('data-toggle');
    const cfItem = toggleButton.closest('.cf-item');

    // Disable button during request
    toggleButton.disabled = true;
    toggleButton.style.opacity = '0.5';

    try {
    const response = await fetch(`/profile/${currentUsername}/closeFriends`, {
    method: 'POST',
    headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrfToken,
    'Accept': 'application/json'
},
    body: JSON.stringify({
    friend_id: friendId
})
});

    const data = await response.json();

    if (data.success) {
    // Toggle button appearance
    if (data.isCloseFriend) {
    toggleButton.className = 'cf-toggle in-list';
    toggleButton.title = 'Remove from Close Friends';
    toggleButton.innerHTML = `
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        `;

    // Add ring and badge
    const avWrap = cfItem.querySelector('.cf-av-wrap');
    if (avWrap) {
    if (!avWrap.querySelector('.cf-ring')) {
    const ring = document.createElement('div');
    ring.className = 'cf-ring';
    avWrap.appendChild(ring);
}
    if (!avWrap.querySelector('.cf-badge')) {
    const badge = document.createElement('div');
    badge.className = 'cf-badge';
    badge.textContent = '★';
    avWrap.appendChild(badge);
}
}
} else {
    toggleButton.className = 'cf-toggle follow';
    toggleButton.title = 'Add to Close Friends';
    toggleButton.innerHTML = `
                            <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                        `;

    // Remove ring and badge
    const avWrap = cfItem.querySelector('.cf-av-wrap');
    if (avWrap) {
    const ring = avWrap.querySelector('.cf-ring');
    const badge = avWrap.querySelector('.cf-badge');
    if (ring) ring.remove();
    if (badge) badge.remove();
}
}

    // Update count
    const closeFriendsCount = document.querySelectorAll('.cf-toggle.in-list').length;
    document.getElementById('bigCount').textContent = closeFriendsCount;

    // Show toast
    showToast(data.message, data.success);

} else {
    showToast(data.message || 'An error occurred', false);
}
} catch (error) {
    console.error('Error:', error);
    showToast('Network error. Please try again.', false);
} finally {
    toggleButton.disabled = false;
    toggleButton.style.opacity = '1';
}
});
});
