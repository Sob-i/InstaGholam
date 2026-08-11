@extends('layouts.admin.posts.main')
@section('content')

    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Posts</span>
            <div class="spacer"></div>

            {{-- Tabs --}}
            <div class="tabs">
                <button class="tab-btn active" data-tab="recent">Recent</button>
                <button class="tab-btn" data-tab="flagged">Flagged</button>
                <button class="tab-btn" data-tab="hidden">Hidden</button>
            </div>

        </div>

        <div class="content">
            <div class="mini-stats">
                <div class="mini-stat"><div class="mini-stat-val">{{$data['postsCount']}}</div><div class="mini-stat-label">Total posts</div></div>
                <div class="mini-stat"><div class="mini-stat-val">{{$data['todayPosts']}}</div><div class="mini-stat-label">Posted today</div></div>
                <div class="mini-stat"><div id="flagged-mini-stat-value" class="mini-stat-val" style="color:var(--red)">{{$data['flaggedPosts']}}</div><div class="mini-stat-label">Flagged</div></div>
                <div class="mini-stat"><div class="mini-stat-val" style="color:var(--amber)">{{$data['hiddenPosts']}}</div><div class="mini-stat-label">Hidden</div></div>
            </div>

            <div class="filters-row">
                <div class="filter-search">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" placeholder="Search posts, captions, tags…"/>
                </div>
                <select><option>All types</option><option>Photo</option><option>Video</option></select>
                <select><option>Sort: Newest</option><option>Sort: Most liked</option><option>Sort: Most reported</option></select>
            </div>

            <div class="posts-grid" id="postsGrid">
                @foreach($data['posts'] as $post)
                    @php
                        $firstFile = strtok($post->post_files, ',');
                        $fileExtension = strtolower(pathinfo($firstFile, PATHINFO_EXTENSION));
                        $isVideo = in_array($fileExtension, ['mp4', 'mov', 'avi', 'webm']);
                        $postPath = 'users/posts/' . strstr($post->user->email, '@', true) . '-posts/' . $post->created_at->format('Y-m-d') . '/' . $firstFile;
                    @endphp
                        @if($post->status == 'active')
                        <div class="pg-card" id="{{'postCard'.$post->id}}">
                            @if($isVideo)
                                <div class="pg-thumb">
                                    <video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;" muted preload="metadata" disablepictureinpicture disableremoteplayback>
                                        <source src="{{ asset($postPath) }}" type="video/{{ $fileExtension }}">
                                    </video>
                                </div>
                            @else
                                <div class="pg-thumb">
                                    <img src="{{ asset($postPath) }}" alt="Post image" loading="lazy" style="object-fit: cover; object-position: center; width: 100%; height: 100%;">
                                </div>
                            @endif
                            <div class="pg-info">
                                <div class="pg-user">{{$post->user->username}}</div>
                                <div class="pg-caption">{{$post->post_caption ?? 'NoCaption'}}</div>
                                <div class="pg-stats">
                                    <span class="pg-stat">♥ {{$post->likes_formatted}}</span>
                                    <span class="pg-stat">💬 {{$post->comments_formatted}}</span>
                                    <span style="margin-left:auto;"><span class="status-dot dot-green"></span></span>
                                </div>
                            </div>
                            <div class="pg-actions" id="post-pg-actions-container">
                                <a href="{{route('post.show',$post->id)}}" class="pg-btn" style="text-decoration: none">View</a>
                                <button class="pg-btn flag" data-action="flag" id="post-flag-btn" data-post-id="{{$post->id}}">Flag</button>
                                <button class="pg-btn danger" data-action="delete" data-post-id="${{$post->id}}">Delete</button>
                            </div>
                    </div>
                        @endif
                @endforeach
            </div>
        </div>
    </div>

    <script>

        window.adminRoutes = {
            flagged: '{{ route("admin.posts.flagged") }}',
            hidden: '{{ route("admin.posts.hidden") }}',
            baseUrl: '{{ asset('') }}'
        };


    </script>
@endsection
