@extends('layouts.admin.users.main')
@section('content')
    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Users</span>
            <div class="spacer"></div>
            <button class="topbar-btn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add User
            </button>
        </div>

        <div class="content">
            <!-- Mini stats -->
            <div class="mini-stats">
                <div class="mini-stat"><div class="mini-stat-val">{{number_format($totalUsers)}}</div><div class="mini-stat-label">Total users</div></div>
                <div class="mini-stat"><div class="mini-stat-val" style="color:var(--green)">{{number_format($activeUser)}}</div><div class="mini-stat-label">Active</div></div>
                <div class="mini-stat"><div class="mini-stat-val" style="color:var(--amber)">{{number_format($suspendedUsers)}}</div><div class="mini-stat-label">Suspended</div></div>
                <div class="mini-stat"><div class="mini-stat-val" style="color:var(--red)">{{number_format($bannedUsers)}}</div><div class="mini-stat-label">Banned</div></div>
            </div>

            <!-- Filters -->
            <div class="filters-row">
                <div class="filter-search">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" placeholder="Search by username or email…"/>
                </div>
                <select>
                    <option>All statuses</option>
                    <option>Active</option>
                    <option>Suspended</option>
                    <option>Banned</option>
                </select>
                <select>
                    <option>All roles</option>
                    <option>User</option>
                    <option>Verified</option>
                    <option>Admin</option>
                </select>
                <select>
                    <option>Sort: Newest</option>
                    <option>Sort: Oldest</option>
                    <option>Sort: Most posts</option>
                    <option>Sort: Most followers</option>
                </select>
            </div>

            <!-- Bulk action bar (shown when items selected) -->
            <div class="bulk-bar">
                <input type="checkbox"/>
                <span class="bulk-bar-text">3 users selected</span>
                <div class="spacer"></div>
                <button class="bulk-btn">Send email</button>
                <button class="bulk-btn">Verify</button>
                <button class="bulk-btn danger">Suspend</button>
                <button class="bulk-btn danger">Delete</button>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-head">
                    <span class="card-title">All Users</span>
                    <span class="result-count">{{number_format($totalUsers)}} results</span>
                </div>
                <table>
                    <thead>
                    <tr>
                        <th><input type="checkbox"/></th>
                        <th class="sortable">User</th>
                        <th class="sortable">Email</th>
                        <th class="sortable">Joined</th>
                        <th class="sortable">Posts</th>
                        <th class="sortable">Followers</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><input type="checkbox" value="{{$user->id}}" data-user-id="{{$user->id}}"/></td>
                            <td><div class="cell-user">
                                    <div class="cell-av a">
                                        <img src="{{ asset('users/avatar/' . $user->avatar) }}" class="sidebar-avatar" alt="{{ $user->username }} avatar">
                                    </div>
                                    <div>
                                        <div class="cell-name">{{$user->username}}</div>
                                        <div class="cell-handle">@ {{$user->username}}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{$user->email}}</td><td>{{$user->created_at->format('M d, Y')}}</td><td>{{$user->posts}}</td><td>
                            {{$user->followers}}</td>
                            @if($user->role == 'verifiedUser')
                                <td><span class="badge-pill pill-muted" style="color:var(--accent2)">Verified</span></td>
                            @elseif($user->role == 'admin')
                                <td><span class="badge-pill pill-amber">admin</span></td>
                            @else
                                <td><span class="badge-pill pill-muted">User</span></td>
                            @endif

                        @if($user->role == 'verifiedUser' || $user->role == 'user')
                                @if($user->status == 'active')
                                    <td><span class="badge-pill pill-green">Active</span></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{route('profile',$user->username)}}" class="act-btn primary" style="text-decoration: none">View</a>
                                            <button class="act-btn">Suspend</button>
                                            <button class="act-btn danger">Ban</button>
                                        </div>
                                    </td>
                                @elseif($user->status == 'suspend')
                                    <td><span class="badge-pill pill-amber">Suspended</span></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{route('profile',$user->username)}}" class="act-btn primary" style="text-decoration: none">View</a>
                                            <button class="act-btn">Restore</button>
                                            <button class="act-btn danger">Ban</button>
                                        </div>
                                    </td>
                                @else
                                    <td><span class="badge-pill pill-red">Banned</span></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{route('profile',$user->username)}}" class="act-btn primary" style="text-decoration: none">View</a>
                                            <button class="act-btn">Restore</button>
                                            <button class="act-btn danger">Email</button>
                                        </div>
                                    </td>
                                @endif
                            @else
                                <td><span class="badge-pill pill-green">Active</span></td>
                                <td>
                                    <div class="action-group">
                                        <a href="{{route('profile',$user->username)}}" class="act-btn primary" style="text-decoration: none">View</a>
                                    </div>
                                </td>
                        @endif
                        </tr>
                    @empty

                    @endforelse
                    </tbody>
                </table>
                <div class="pagination">
                    <span class="page-info">Showing 1–6 of 284,910</span>
                    <div class="page-btns">
                        <button class="page-btn">←</button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn">3</button>
                        <span style="color:var(--muted);padding:6px 4px;">…</span>
                        <button class="page-btn">47,485</button>
                        <button class="page-btn">→</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSRF token setup
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Create the modal HTML
            const modalHTML = `
        <div id="confirmModal" class="modal-overlay" style="display: none;">
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
                    <p id="modalMessage" class="modal-message"></p>
                </div>
                <div class="modal-footer">
                    <button class="modal-btn modal-btn-cancel" id="modalCancel">Cancel</button>
                    <button class="modal-btn modal-btn-confirm" id="modalConfirm">Confirm</button>
                </div>
            </div>
        </div>
    `;

            document.body.insertAdjacentHTML('beforeend', modalHTML);

            // Add modal styles
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
                text-align: center;
                border-bottom: 1px solid #2d3039;
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

            document.head.insertAdjacentHTML('beforeend', modalStyles);

            // Modal functionality
            let modalCallback = null;
            const modal = document.getElementById('confirmModal');
            const modalMessage = document.getElementById('modalMessage');
            const modalConfirm = document.getElementById('modalConfirm');
            const modalCancel = document.getElementById('modalCancel');
            const modalIcon = document.querySelector('.modal-icon');

            function showModal(message, action, onConfirm) {
                modalMessage.innerHTML = message;

                modalIcon.className = 'modal-icon';
                modalConfirm.className = 'modal-btn modal-btn-confirm';

                switch(action) {
                    case 'Ban':
                        modalIcon.classList.add('danger');
                        modalConfirm.classList.add('danger-btn');
                        modalConfirm.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    Ban User
                `;
                        break;

                    case 'Suspend':
                        modalConfirm.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    Suspend User
                `;
                        break;

                    case 'Restore':
                        modalIcon.classList.add('success');
                        modalConfirm.classList.add('success-btn');
                        modalConfirm.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Restore User
                `;
                        break;
                }

                modal.style.display = 'flex';
                modalCallback = onConfirm;
            }

            function hideModal() {
                modal.style.display = 'none';
                modalCallback = null;
            }

            modalConfirm.addEventListener('click', () => {
                if (modalCallback) {
                    modalCallback();
                }
                hideModal();
            });

            modalCancel.addEventListener('click', hideModal);

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    hideModal();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    hideModal();
                }
            });

            // Toast notification function
            function showToast(message, type = 'success') {
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

            // Function to update user status
            function updateUserStatus(route, userId, action, buttonElement) {
                if (buttonElement) {
                    buttonElement.style.opacity = '0.5';
                    buttonElement.style.pointerEvents = 'none';
                    const originalText = buttonElement.textContent.trim();
                    buttonElement.innerHTML = '<span class="loading-spinner"></span> Processing...';
                    buttonElement.dataset.originalText = originalText;
                }

                fetch(route, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            let successMessage = '';
                            switch(action) {
                                case 'Suspend':
                                    successMessage = '✓ User has been suspended successfully';
                                    break;
                                case 'Ban':
                                    successMessage = '✓ User has been banned successfully';
                                    break;
                                case 'Restore':
                                    successMessage = '✓ User has been restored successfully';
                                    break;
                                default:
                                    successMessage = '✓ ' + data.message;
                            }

                            showToast(successMessage, 'success');
                            updateUserRowUI(userId, action);

                        } else {
                            showToast('✗ ' + (data.message || 'Something went wrong. Please try again.'), 'error');
                            resetButton(buttonElement);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('✗ An error occurred. Please try again.', 'error');
                        resetButton(buttonElement);
                    });
            }

            // Function to update the user row UI dynamically
            function updateUserRowUI(userId, action) {
                const row = document.querySelector(`input[data-user-id="${userId}"]`)?.closest('tr');
                if (!row) return;

                const statusCell = row.querySelector('td:nth-child(8)');
                const actionsCell = row.querySelector('td:nth-child(9)');
                const userProfileUrl = row.querySelector('.act-btn.primary')?.getAttribute('href') || '#';

                let statusHTML = '';
                let actionsHTML = '';

                switch(action) {
                    case 'Suspend':
                        statusHTML = '<span class="badge-pill pill-amber">Suspended</span>';
                        actionsHTML = `
                    <div class="action-group">
                        <a href="${userProfileUrl}" class="act-btn primary" style="text-decoration: none">View</a>
                        <button class="act-btn">Restore</button>
                        <button class="act-btn danger">Ban</button>
                    </div>
                `;
                        break;

                    case 'Ban':
                        statusHTML = '<span class="badge-pill pill-red">Banned</span>';
                        actionsHTML = `
                    <div class="action-group">
                        <a href="${userProfileUrl}" class="act-btn primary" style="text-decoration: none">View</a>
                        <button class="act-btn">Restore</button>
                        <button class="act-btn danger">Email</button>
                    </div>
                `;
                        break;

                    case 'Restore':
                        statusHTML = '<span class="badge-pill pill-green">Active</span>';
                        actionsHTML = `
                    <div class="action-group">
                        <a href="${userProfileUrl}" class="act-btn primary" style="text-decoration: none">View</a>
                        <button class="act-btn">Suspend</button>
                        <button class="act-btn danger">Ban</button>
                    </div>
                `;
                        break;
                }

                if (statusCell) {
                    statusCell.style.opacity = '0';
                    statusCell.innerHTML = statusHTML;
                    setTimeout(() => {
                        statusCell.style.transition = 'all 0.3s ease';
                        statusCell.style.opacity = '1';
                    }, 50);
                }

                if (actionsCell) {
                    actionsCell.style.opacity = '0';
                    actionsCell.innerHTML = actionsHTML;
                    setTimeout(() => {
                        actionsCell.style.transition = 'all 0.3s ease';
                        actionsCell.style.opacity = '1';
                        attachButtonListeners();
                    }, 100);
                }

                updateStatsCounters(action);
            }

            // Function to update stats counters
            function updateStatsCounters(action) {
                const activeStat = document.querySelector('.mini-stat:nth-child(2) .mini-stat-val');
                const suspendedStat = document.querySelector('.mini-stat:nth-child(3) .mini-stat-val');
                const bannedStat = document.querySelector('.mini-stat:nth-child(4) .mini-stat-val');

                if (!activeStat || !suspendedStat || !bannedStat) return;

                let activeCount = parseInt(activeStat.textContent) || 0;
                let suspendedCount = parseInt(suspendedStat.textContent) || 0;
                let bannedCount = parseInt(bannedStat.textContent) || 0;

                switch(action) {
                    case 'Suspend':
                        activeCount = Math.max(0, activeCount - 1);
                        suspendedCount = suspendedCount + 1;
                        break;
                    case 'Ban':
                        activeCount = Math.max(0, activeCount - 1);
                        suspendedCount = Math.max(0, suspendedCount - 1);
                        bannedCount = bannedCount + 1;
                        break;
                    case 'Restore':
                        suspendedCount = Math.max(0, suspendedCount - 1);
                        bannedCount = Math.max(0, bannedCount - 1);
                        activeCount = activeCount + 1;
                        break;
                }

                animateNumber(activeStat, activeCount);
                animateNumber(suspendedStat, suspendedCount);
                animateNumber(bannedStat, bannedCount);
            }

            // Function to animate number changes
            function animateNumber(element, newValue) {
                const currentValue = parseInt(element.textContent) || 0;
                const diff = newValue - currentValue;
                const duration = 500;
                const start = performance.now();

                function update(currentTime) {
                    const elapsed = currentTime - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const easeProgress = 1 - Math.pow(1 - progress, 3);

                    const currentNumber = Math.round(currentValue + (diff * easeProgress));
                    element.textContent = currentNumber;

                    if (progress < 1) {
                        requestAnimationFrame(update);
                    }
                }

                requestAnimationFrame(update);
            }

            // Function to reset button state
            function resetButton(button) {
                if (!button) return;
                button.style.opacity = '1';
                button.style.pointerEvents = 'auto';
                button.textContent = button.dataset.originalText || button.textContent;
            }

            // Function to attach event listeners to buttons
            function attachButtonListeners() {
                // Handle all buttons
                document.querySelectorAll('.act-btn').forEach(button => {
                    // Skip View buttons and already processed buttons
                    if (button.classList.contains('primary') || button.dataset.listenerAttached === 'true') {
                        return;
                    }

                    button.dataset.listenerAttached = 'true';

                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const row = this.closest('tr');
                        const userId = row.querySelector('input[type="checkbox"]')?.dataset.userId;
                        const userName = row.querySelector('.cell-name')?.textContent.trim() || 'this user';

                        if (!userId) return;

                        const actionText = this.textContent.trim();

                        if (actionText === 'Suspend') {
                            const message = `<span style="color: #f59e0b; font-weight: 600;">${userName}</span> will be <span style="color: #f59e0b; font-weight: 600;">suspended</span> and lose access to their account until restored.`;

                            showModal(message, 'Suspend', () => {
                                const route = `/admin/users/${userId}/statusToSuspended`;
                                updateUserStatus(route, userId, 'Suspend', this);
                            });
                        }
                        else if (actionText === 'Restore') {
                            const message = `<span style="color: #10b981; font-weight: 600;">${userName}</span> will be <span style="color: #10b981; font-weight: 600;">restored</span> and regain full access to their account.`;

                            showModal(message, 'Restore', () => {
                                const route = `/admin/users/${userId}/statusToActive`;
                                updateUserStatus(route, userId, 'Restore', this);
                            });
                        }
                        else if (actionText === 'Ban') {
                            const message = `<span style="color: #ef4444; font-weight: 600;">${userName}</span> will be <span style="color: #ef4444; font-weight: 600;">permanently banned</span>. This action is severe and should only be used for users violating terms of service.`;

                            showModal(message, 'Ban', () => {
                                const route = `/admin/users/${userId}/statusToBanned`;
                                updateUserStatus(route, userId, 'Ban', this);
                            });
                        }
                        else if (actionText === 'Email') {
                            showToast('Email functionality coming soon!', 'success');
                        }
                    });
                });
            }

            // Initial attachment of event listeners
            attachButtonListeners();
        });
    </script>

@endsection
