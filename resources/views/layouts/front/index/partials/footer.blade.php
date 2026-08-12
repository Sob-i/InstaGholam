
<script>
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;

        const icon = type === 'success'
            ? `<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M5 8l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`
            : `<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M8 5v4M8 11h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>`;

        let contentHtml = message;
        if (Array.isArray(message)) {
            contentHtml = '<ul>' + message.map(m => `<li>${m}</li>`).join('') + '</ul>';
        }

        toast.innerHTML = `
        ${icon}
        <div class="toast-content">${contentHtml}</div>
        <button class="toast-close" aria-label="Close">&times;</button>
        <div class="toast-progress"></div>
    `;

        container.appendChild(toast);

        const closeBtn = toast.querySelector('.toast-close');
        const progressBar = toast.querySelector('.toast-progress');

        let startTime = Date.now();
        let timer;

        const remove = () => {
            toast.classList.add('removing');
            setTimeout(() => toast.remove(), 250);
        };

        // Set initial timer
        const startTimer = (duration) => {
            clearTimeout(timer);
            // Reset progress animation
            progressBar.style.animation = 'none';
            progressBar.offsetHeight; // Trigger reflow
            progressBar.style.animation = `toastProgress ${duration}ms linear forwards`;

            timer = setTimeout(remove, duration);
        };

        startTimer(5000);

        closeBtn.addEventListener('click', remove);

        // Pause on hover
        toast.addEventListener('mouseenter', () => {
            clearTimeout(timer);
            const elapsed = Date.now() - startTime;
            const remaining = Math.max(5000 - elapsed, 500);
            // Pause progress bar
            const progressAfter = progressBar.querySelector('::after');
            progressBar.style.animationPlayState = 'paused';
        });

        toast.addEventListener('mouseleave', () => {
            const elapsed = Date.now() - startTime;
            const remaining = Math.max(5000 - elapsed, 500);
            progressBar.style.animationPlayState = 'running';
            timer = setTimeout(remove, remaining);
        });
    }

    // Trigger on page load if session data exists
    document.addEventListener('DOMContentLoaded', () => {
        if (window.toastMessages) {
            if (window.toastMessages.success) {
                showToast(window.toastMessages.success, 'success');
            }
            if (window.toastMessages.fail) {
                showToast(window.toastMessages.fail, 'error');
            }
            if (window.toastMessages.errors) {
                showToast(window.toastMessages.errors, 'error');
            }
        }
    });
</script>
<script>
    function toggleDropdown(event) {
        event.stopPropagation();

        const button = event.currentTarget;
        const menu = button.nextElementSibling;

        // close all other menus
        document.querySelectorAll('.dropdown-menu').forEach(item => {
            if (item !== menu) {
                item.classList.remove('show');
            }
        });

        // toggle current menu
        menu.classList.toggle('show');
    }

    document.addEventListener('click', function(event) {
        document.querySelectorAll('.dropdown-wrapper').forEach(wrapper => {
            const dropdown = wrapper.querySelector('.dropdown-menu');

            if (!wrapper.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
    });
</script>
<script src="{{asset('frontJs/profile/follow.js')}}"></script>
<script src="{{asset('frontJs/postSingle/video.js')}}"></script>
<script src="{{asset('frontJs/postSingle/comment.js')}}"></script>
<script src="{{asset('frontJs/comments/deleteComment.js')}}"></script>
<script src="{{asset('frontJs/postSingle/like.js')}}"></script>
<script src="{{asset('frontJs/postSingle/save.js')}}"></script>
<script src="{{asset('frontJs/report/report.js')}}"></script>
</body>
</html>
