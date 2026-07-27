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
            const wasRequested = button.classList.contains('requested');

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
                    updateButtonState(button, data.isFollowed, data.message);

                    // Update ONLY followers count
                    const statNums = document.querySelectorAll('.stat-num');
                    statNums.forEach(statNum => {
                        const statLabel = statNum.nextElementSibling;

                        if (statLabel && statLabel.classList.contains('stat-label')) {
                            if (statLabel.textContent.trim() === 'Followers') {

                                statNum.classList.add('updating');
                                statNum.textContent = data.formatted_count || data.followersCount;

                                setTimeout(() => {
                                    statNum.classList.remove('updating');
                                }, 300);
                            }
                        }
                    });

                    button.classList.add(data.animation);

                    setTimeout(() => {
                        button.classList.remove(data.animation);
                    }, 1000);

                    showToast(data.message, 'success');

                } else {

                    button.textContent = originalText;

                    if (wasRequested) {
                        button.classList.add('requested');
                        button.classList.remove('following', 'not-following');
                    } else if (wasFollowing) {
                        button.classList.add('following');
                        button.classList.remove('not-following', 'requested');
                    } else {
                        button.classList.add('not-following');
                        button.classList.remove('following', 'requested');
                    }

                    showToast(data.message || 'An error occurred', 'error');
                }

            } catch (error) {

                console.error('Error:', error);

                button.textContent = originalText;

                if (wasRequested) {
                    button.classList.add('requested');
                    button.classList.remove('following', 'not-following');
                } else if (wasFollowing) {
                    button.classList.add('following');
                    button.classList.remove('not-following', 'requested');
                } else {
                    button.classList.add('not-following');
                    button.classList.remove('following', 'requested');
                }

                showToast('Network error. Please try again.', 'error');

            } finally {
                button.disabled = false;
            }
        });

        // Hover effect ONLY for real followers
        followButton.addEventListener('mouseenter', function() {

            if (this.classList.contains('following') &&
                !this.classList.contains('requested')) {

                this.textContent = 'Unfollow';
                this.style.backgroundColor = '#ff4444';
                this.style.color = '#fff';
                this.style.borderColor = '#ff4444';
            }

        });

        followButton.addEventListener('mouseleave', function() {

            if (this.classList.contains('following') &&
                !this.classList.contains('requested')) {

                this.textContent = 'Following';
                this.style.backgroundColor = '';
                this.style.color = '';
                this.style.borderColor = '';
            }

        });
    }

    function updateButtonState(button, isFollowed, message) {

        if (!isFollowed) {

            button.textContent = 'Follow';

            button.classList.remove('following', 'requested');
            button.classList.add('not-following');

            return;
        }

        // Private account
        if (message === 'Follow request sent') {

            button.textContent = 'Requested';

            button.classList.remove('following', 'not-following');
            button.classList.add('requested');

            return;
        }

        // Public account
        button.textContent = 'Following';

        button.classList.remove('requested', 'not-following');
        button.classList.add('following');
    }

    function showToast(message, type) {
        const toastContainer = document.getElementById('toast-container');

        if (!toastContainer) {
            console.error('Toast container not found');
            return;
        }

        const toast = document.createElement('div');

        toast.className = `toast toast-${type}`;
        toast.textContent = message;

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

        setTimeout(() => {
            toast.style.opacity = '0';

            setTimeout(() => toast.remove(), 300);

        }, 3000);
    }

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
