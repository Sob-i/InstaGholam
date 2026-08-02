<style>
    .sidebar-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .sidebar-user {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        margin-top: auto;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-user div {
        min-width: 0;
    }

    .sidebar-user-name {
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-user-handle {
        font-size: 11px;
        opacity: 0.7;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar {
        display: flex;
        flex-direction: column;
        height: 100vh;
    }

    .sidebar nav {
        flex: 1;
        overflow-y: auto;
    }

</style>
<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div>
            <div class="brand-name">InstaGholam</div>
        </div>
        <div class="brand-badge">ADMIN</div>
    </div>

    <nav>
        <div class="nav-group">
            <div class="nav-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                </svg>
                Dashboard
            </a>
            <a href="admin-analytics.html" class="nav-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                Analytics
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-label">Content</div>
            <a href="{{route('admin.users')}}" class="nav-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                    <path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
                Users
            </a>
            <a href="{{route('admin.posts')}}" class="nav-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                Posts
            </a>
            <a href="{{route('admin.reports')}}" class="nav-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Reports
            </a>
            <a href="#" class="nav-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
                Hashtags
            </a>
            <a href="#" class="nav-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
                Comments
                <span class="badge amber">3</span>
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-label">System</div>
            <a href="#" class="nav-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M19.07 19.07l-1.41-1.41M4.93 19.07l1.41-1.41M12 2v2M12 20v2M2 12h2M20 12h2"/>
                </svg>
                Settings
            </a>
            <a href="#" class="nav-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                Audit Log
            </a>
        </div>
    </nav>

    <a class="sidebar-user" href="{{ route('profile', Auth::user()->username) }}" style="text-decoration: none; color: white">
        <img src="{{ asset('users/avatar/' . Auth::user()->avatar) }}" class="sidebar-avatar" alt="{{ Auth::user()->username }} avatar">
        <div>
            <div class="sidebar-user-name">{{ Auth::user()->username }}</div>
            <div class="sidebar-user-handle">@ {{ Auth::user()->username }}</div>
        </div>
    </a>
</aside>
