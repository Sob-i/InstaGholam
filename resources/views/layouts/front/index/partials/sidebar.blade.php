
<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">InstaGholam<span>.</span></div>
    <nav>
        <a href="{{route('homepage')}}" class="nav-link active">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/><path d="M9 21V12h6v9"/></svg>Home
        </a>
        <a href="{{route('explore')}}" class="nav-link">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>Explore
        </a>
        <a href="{{route('newPost')}}" class="nav-link">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M12 8v8M8 12h8"/></svg>New Post
        </a>
        <a href="{{route('story.new.show')}}" class="nav-link">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>Add Story
        </a>
        <a href="notifications.html" class="nav-link">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Notifications
        </a>
        <a href="messages.html" class="nav-link">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>Messages
        </a>
        <a href="{{route('profile',Auth::user()->username)}}" class="nav-link">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>Profile
        </a>
        @if(Auth::user()->role == 'admin')
            <a href="{{route('admin.dashboard')}}" class="nav-link">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="9" rx="1"/>
                    <rect x="14" y="3" width="7" height="5" rx="1"/>
                    <rect x="14" y="12" width="7" height="9" rx="1"/>
                    <rect x="3" y="16" width="7" height="5" rx="1"/>
                </svg>AdminPanel
            </a>
        @endif
    </nav>
    <a class="sidebar-user" href="{{route('profile',Auth::user()->username)}}" style="text-decoration: none; color: white">
        <img src="{{asset('users/avatar/'.Auth::user()->avatar)}}" class="sidebar-avatar">
        <div><div class="sidebar-user-name">{{Auth::user()->username}}</div><div class="sidebar-user-handle">{{"@" . Auth::user()->username}}</div></div>
    </a>
</aside>

