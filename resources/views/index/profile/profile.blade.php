
@extends('layouts.front.profile.main')
@section('content')
    <div class="main">
        <div class="profile-header">
            <div class="profile-av-ring">
                @if($user->avatar != null)
                    <img src="{{ asset('users/avatar/'.$user->avatar) }}" class="profile-av">
                @else
                    <div class="profile-av"></div>
                @endif
            </div>
            <div class="profile-info">
                <div class="profile-top">
                    <span class="profile-username">{{$user->username}}</span>
                    @if($user->role == 'verifiedUser' || $user->role == 'admin' )
                        <span class="verified"><svg width="14" height="14" fill="var(--accent)" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Verified</span>
                    @endif
                    @if($user->id == auth()->id())
                        <a href="{{route('profile.edit.show',$user->username)}}" class="btn-edit" style="text-decoration: none">Edit Profile</a>
                        <!-- Three-dot menu button -->
                        <div class="dropdown-wrapper">
                            <button class="btn-three-dot" onclick="toggleDropdown(event)">
                                <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                                    <circle cx="12" cy="5" r="2"/>
                                    <circle cx="12" cy="12" r="2"/>
                                    <circle cx="12" cy="19" r="2"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu" id="dropdownMenu">
                                <a href="{{route('profile.closeFriend.show',$user->username)}}" class="dropdown-item">Close Friends</a>
                            </div>
                        </div>
                    @else
                        @if($user->privacy == 'public')
                            <button class="btn-edit custom-a {{ $data['isFollowed'] ? 'following' : 'not-following' }}"
                                    data-username="{{ $user->username }}"
                                    data-follow-url="{{ route('profile.user.follow', $user->username) }}">
                                {{ $data['isFollowed'] ? 'Following' : 'Follow' }}
                            </button>
                        @elseif($user->privacy == 'private' && !$data['isFollowed'] && !$data['requested'])
                            <button class="btn-edit custom-a {{ $data['isFollowed'] ? 'following' : 'not-following' }}"
                                    data-username="{{ $user->username }}"
                                    data-follow-url="{{ route('profile.user.follow', $user->username) }}">
                                {{ $data['isFollowed'] ? 'Following' : 'Request Follow' }}
                            </button>
                        @elseif($user->privacy == 'private' && $data['isFollowed'])
                            <button class="btn-edit custom-a {{ $data['isFollowed'] ? 'following' : 'not-following' }}"
                                    data-username="{{ $user->username }}"
                                    data-follow-url="{{ route('profile.user.follow', $user->username) }}">
                                {{ $data['isFollowed'] ? 'Following' : 'Request Follow' }}
                            </button>
                        @else
                            <sapn class="btn-edit" style="color: gray" >
                                {{ $data['isFollowed'] ? 'Following' : 'Requested' }}
                            </sapn>
                        @endif
                        <a href="{{route('message.page.show',$user->id)}}" class="btn-edit" style="text-decoration: none">
                            <i class="fa-solid fa-message"></i>
                        </a>
                    @endif
                </div>
                <div class="profile-stats">
                    <div class="stat">
                        <div class="stat-num">{{$data['postsCount']}}</div>
                        <div class="stat-label">Posts</div>
                    </div>
                    <div class="stat" id="user-followers" style="cursor: pointer;">
                        <div class="stat-num">{{$data['followersCount']}}</div>
                        <div class="stat-label">Followers</div>
                    </div>
                    <div class="stat" id="user-followings" style="cursor: pointer;">
                        <div class="stat-num">{{$data['followingCount']}}</div>
                        <div class="stat-label">Following</div>
                    </div>
                </div>
                <div class="profile-bio">
                    <div class="profile-bio">
                        <span style="display: block; max-width: 47ch; word-wrap: break-word;">
                        {{$user->bio}}
                        </span>
                        <a href="{{$user->website}}" class="profile-link">{{preg_replace('/^https?:\/\//', '',$user->website)}}</a>
                    </div>
                </div>
            </div>
        </div>
                {{--followsModal--}}
        <div class="follow-modal-overlay" id="followModal">
            <div class="follow-modal">

                <div class="follow-modal-header">
                    <h3 id="followModalTitle">Followers</h3>

                    <button type="button" id="closeFollowModal">
                        &times;
                    </button>
                </div>

                <div class="follow-modal-body">

                    <div id="followLoading" class="follow-loading">
                        Loading...
                    </div>

                    <div id="followError" class="follow-error" style="display: none;">
                        Something went wrong!
                    </div>

                    <div id="followList" class="follow-list"></div>

                </div>

            </div>
        </div>

        @if($data['canViewProfile'] == 'true')
                        <!-- Highlights -->
                        <div class="highlights">
                            @if($user->id == auth()->id())
                                <div class="highlight">
                                    <a href="{{route('profile.highlights.show',$user->username)}}" style="text-decoration: none">
                                        <div class="hl-ring add"><div class="hl-av h5">＋</div></div>
                                    </a>
                                    <span class="hl-name">New</span>
                                </div>
                            @endif
                                @forelse($data['highlights'] as $highlight)
                                    <div class="highlight" data-highlight="{{ $highlight->cover }}" data-username="{{ $user->username }}">
                                        <div class="hl-ring"
                                             style="background-image: url('{{asset('users/highlights/'.strstr($user->email, '@', true).'/'.$highlight->cover)}}');
                                            background-size: cover;
                                            background-position: center;">
                                        </div>
                                        <span class="hl-name">{{$highlight->title}}</span>
                                    </div>
                                @empty
                                @endforelse
                        </div>
                        <!-- Highlight Modal -->
                        <div class="highlight-story-modal-overlay" id="highlightStoryModal" style="display: none;">
                            <div class="highlight-story-modal">
                                <!-- Close Button -->
                                <button class="highlight-story-close" id="closeHighlightStory">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>

                                <!-- Story Header -->
                                <div class="highlight-story-header">
                                    <div class="highlight-story-user-info">
                                        <img src="" class="highlight-story-avatar" id="highlightStoryAvatar" alt="">
                                        <span class="highlight-story-title" id="highlightStoryTitle"></span>
                                        <span class="highlight-story-count" id="highlightStoryCount"></span>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="highlight-story-progress-container" id="highlightStoryProgressContainer">
                                    <!-- Progress bars will be dynamically added here -->
                                </div>

                                <!-- Story Content -->
                                <div class="highlight-story-content" id="highlightStoryContent">
                                    <!-- Story media will be dynamically loaded here -->
                                </div>

                                <!-- Navigation Buttons -->
                                <button class="highlight-story-nav highlight-story-nav-prev" id="highlightStoryPrev">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                </button>
                                <button class="highlight-story-nav highlight-story-nav-next" id="highlightStoryNext">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Grid Tabs -->
                        <div class="grid-tabs">
                            <div class="gtab active" onclick="switchTab('posts', this)">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                Posts
                            </div>
                            @if($user->id == auth()->id())
                                <div class="gtab" onclick="switchTab('saved', this)"  data-tab="saved">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
                                    Saved
                                </div>
                            @endif
                            <div class="gtab" onclick="switchTab('tagged', this)">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                Tagged
                            </div>
                        </div>

                        <!-- Posts Tab Content -->
                        <div class="tab-content" id="posts-content">
                            <div class="post-grid">

                                @forelse($data['posts'] as $post)
                                    @php
                                        $showPost = true;

                                        if($post->post_audience == 'closeFriends' && !$data['isCloseFriend']) {
                                            $showPost = false;
                                        }
                                    @endphp

                                    @if($showPost)
                                        @php
                                            $firstFile = strtok($post->post_files, ',');
                                            $fileExtension = strtolower(pathinfo($firstFile, PATHINFO_EXTENSION));
                                            $isVideo = in_array($fileExtension, ['mp4', 'mov', 'avi', 'webm']);
                                            $postPath = 'users/posts/' . strstr($user->email, '@', true) . '-posts/' . $post->created_at->format('Y-m-d') . '/' . $firstFile;
                                        @endphp

                                        <div class="pg-item">
                                            <a href="{{route('post.show', $post->id)}}">
                                                @if($isVideo)
                                                    <video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;"
                                                           muted
                                                           preload="metadata"
                                                           disablepictureinpicture
                                                           disableremoteplayback>
                                                        <source src="{{ asset($postPath) }}" type="video/{{ $fileExtension }}">
                                                    </video>
                                                @else
                                                    <img src="{{ asset($postPath) }}"
                                                         alt="Post image"
                                                         loading="lazy"
                                                         style="object-fit: cover; object-position: center; width: 100%; height: 100%;">
                                                @endif

                                                <div class="pg-overlay">
                                                    {{-- Like count --}}
                                                    @if($post->like_count == 'visible')
                                                        <span>♥ {{$post->post_likes}}</span>
                                                    @elseif($post->like_count == 'notVisible' && $post->user_id != auth()->id())
                                                        <span>♥ Hidden</span>
                                                    @else
                                                        <span>♥ {{$post->post_likes}}</span>
                                                    @endif

                                                    {{-- Comment count --}}
                                                    @if($post->comment_status == 'open')
                                                        <span>💬 {{$post->post_comments}}</span>
                                                    @else
                                                        <span>💬 Closed</span>
                                                    @endif
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                @empty
                                    <div class="no-posts">
                                        <p>No posts to display</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                            @if($user->id == auth()->id())

                                    <div
                                        class="tab-content"
                                        id="saved-content"
                                    >

                                    <div class="saved-scroll-area" id="saved-scroll-area">

                                        <div
                                            class="post-grid"
                                            id="saved-posts-grid"
                                        ></div>

                                        <div
                                            id="saved-posts-loader"
                                            class="saved-loader"
                                        >
                                            Loading...
                                        </div>

                                        <div
                                            id="saved-posts-end"
                                            class="saved-end"
                                        >
                                            No more saved posts
                                        </div>

                                    </div>

                                </div>

                            @endif

                        {{--        <!-- Tagged Tab Content -->--}}
                        {{--        <div class="tab-content" id="tagged-content" style="display: none;">--}}
                        {{--            <div class="post-grid">--}}
                        {{--                @forelse($taggedPosts as $taggedPost)--}}
                        {{--                    @php--}}
                        {{--                        $firstFile = strtok($taggedPost->post_files, ',');--}}
                        {{--                        $fileExtension = pathinfo($firstFile, PATHINFO_EXTENSION);--}}
                        {{--                        $isVideo = in_array(strtolower($fileExtension), ['mp4', 'mov', 'avi', 'webm']);--}}
                        {{--                    @endphp--}}
                        {{--                    <div class="pg-item">--}}
                        {{--                        <a href="{{route('post.show', $taggedPost->id)}}">--}}
                        {{--                            @if($isVideo)--}}
                        {{--                                <video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;"--}}
                        {{--                                       muted--}}
                        {{--                                       preload="metadata"--}}
                        {{--                                       disablepictureinpicture--}}
                        {{--                                       disableremoteplayback>--}}
                        {{--                                    <source src="{{ asset('users/posts/' . strstr($taggedPost->user->email, '@', true) . '-posts' . '/' . $taggedPost->created_at->format('Y-m-d') . '/' . $firstFile) }}"--}}
                        {{--                                            type="video/{{ $fileExtension }}">--}}
                        {{--                                </video>--}}
                        {{--                            @else--}}
                        {{--                                <img src="{{ asset('users/posts/' . strstr($taggedPost->user->email, '@', true) . '-posts' . '/' . $taggedPost->created_at->format('Y-m-d') . '/' . $firstFile) }}"--}}
                        {{--                                     style="object-fit: cover; object-position: center; width: 100%; height: 100%;">--}}
                        {{--                            @endif--}}

                        {{--                            <div class="pg-overlay">--}}
                        {{--                                <span>♥ {{$taggedPost->post_likes}}</span>--}}
                        {{--                                <span>💬 {{$taggedPost->post_comments}}</span>--}}
                        {{--                            </div>--}}
                        {{--                        </a>--}}
                        {{--                    </div>--}}
                        {{--                @empty--}}
                        {{--                    <div class="empty-state">--}}
                        {{--                        <svg width="62" height="62" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">--}}
                        {{--                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>--}}
                        {{--                            <line x1="7" y1="7" x2="7.01" y2="7"/>--}}
                        {{--                        </svg>--}}
                        {{--                        <p>No tagged posts yet</p>--}}
                        {{--                    </div>--}}
                        {{--                @endforelse--}}
                </div>
                </div>

                </div>

            @else
                <div class="private-profile">
                    <i class="fa-solid fa-lock"></i>

                    <h3>This Account is Private</h3>

                    <p>Follow this account to see their photos and videos.</p>
                </div>

            @endif

@endsection
