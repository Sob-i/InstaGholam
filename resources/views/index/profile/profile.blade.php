
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
                                <div class="gtab" onclick="switchTab('saved', this)">
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
                            <!-- Saved Tab Content -->
                            <div class="tab-content" id="saved-content" style="display: none;">
                                <div class="post-grid">
                                    @forelse($data['savedPosts'] as $savedPost)
                                        @php
                                            $post = $savedPost->post;
                                            $firstFile = strtok($post->post_files, ',');
                                            $folderName = strstr($firstFile , '-' , true).'-posts';
                                            $fileExtension = pathinfo($firstFile, PATHINFO_EXTENSION);
                                            $isVideo = in_array(strtolower($fileExtension), ['mp4', 'mov', 'avi', 'webm']);
                                        @endphp
                                        <div class="pg-item">
                                            <a href="{{route('post.show', $post->id)}}">
                                                @if($isVideo)
                                                    <video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;"
                                                           muted
                                                           preload="metadata"
                                                           disablepictureinpicture
                                                           disableremoteplayback>
                                                        <source src="{{ asset('users/posts/'.$folderName). '/' . $post->created_at->format('Y-m-d') . '/' . $firstFile }}"
                                                                type="video/{{ $fileExtension }}">
                                                    </video>
                                                @else
                                                    <img src="{{ asset('users/posts/' . $folderName). '/' . $post->created_at->format('Y-m-d') . '/' . $firstFile}}"
                                                         style="object-fit: cover; object-position: center; width: 100%; height: 100%;">
                                                @endif

                                                <div class="pg-overlay">
                                                    <span>♥ {{$post->post_likes}}</span>
                                                    <span>💬 {{$post->post_comments}}</span>
                                                </div>
                                            </a>
                                        </div>
                                    @empty
                                        <div class="empty-state">
                                            <svg width="62" height="62" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/>
                                            </svg>
                                            <p>No saved posts yet</p>
                                        </div>
                                    @endforelse
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



    <script>
        function toggleDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('dropdownMenu');
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdownMenu');
            const wrapper = document.querySelector('.dropdown-wrapper');

            if (dropdown && wrapper && !wrapper.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
    <script>
        const followersButton = document.getElementById('user-followers');
        const followingButton = document.getElementById('user-followings');

        const followModal = document.getElementById('followModal');
        const closeFollowModal = document.getElementById('closeFollowModal');

        const followModalTitle = document.getElementById('followModalTitle');
        const followList = document.getElementById('followList');

        const followLoading = document.getElementById('followLoading');
        const followError = document.getElementById('followError');


        followersButton.addEventListener('click', function () {
            openFollowModal(
                'Followers',
                "{{ route('profile.followers.show' , $user->username) }}"
            );
        });


        followingButton.addEventListener('click', function () {
            openFollowModal(
                'Following',
                "{{ route('profile.followings.show', $user->username) }}"
            );
        });


        async function openFollowModal(title, url) {

            followModalTitle.textContent = title;

            followModal.classList.add('active');

            // Reset modal
            followList.innerHTML = '';
            followError.style.display = 'none';
            followLoading.style.display = 'block';

            try {

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                followLoading.style.display = 'none';

                if (!result.status) {
                    followError.textContent = result.message || 'Something went wrong!';
                    followError.style.display = 'block';
                    return;
                }

                renderFollowUsers(result.data);

            } catch (error) {

                console.error(error);

                followLoading.style.display = 'none';

                followError.textContent = 'Something went wrong!';
                followError.style.display = 'block';
            }
        }


        function renderFollowUsers(users) {

            followList.innerHTML = '';

            if (!users || users.length === 0) {

                followList.innerHTML = `
            <div class="follow-empty">
                No users found.
            </div>
        `;

                return;
            }

            users.forEach(follow => {

                const user = follow.follower_info ?? follow.following_info;

                if (!user) {
                    return;
                }

                const username = user.username ?? '';
                const name = user.name ?? '';

                const avatar = user.avatar
                    ? `/users/avatar/${user.avatar}`
                    : '/images/default-avatar.png';

                followList.innerHTML += `
            <a href="/profile/${encodeURIComponent(username)}" class="follow-user" style="text-decoration: none;">

                <img
                    src="${avatar}"
                    class="follow-user-avatar"
                    alt="${username}"
                >

                <div class="follow-user-info">

                    <div class="follow-user-username">
                        ${username}
                    </div>

                    <div class="follow-user-name">
                        ${name}
                    </div>
                </div>

            </a>
        `;
            });
        }


        // Close button
        closeFollowModal.addEventListener('click', function () {
            closeModal();
        });


        // Click outside modal
        followModal.addEventListener('click', function (event) {

            if (event.target === followModal) {
                closeModal();
            }

        });


        // ESC key
        document.addEventListener('keydown', function (event) {

            if (event.key === 'Escape') {
                closeModal();
            }

        });


        function closeModal() {
            followModal.classList.remove('active');
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get modal elements
            const storyModal = document.getElementById('highlightStoryModal');
            const closeStoryModal = document.getElementById('closeHighlightStory');
            const storyContent = document.getElementById('highlightStoryContent');
            const storyProgressContainer = document.getElementById('highlightStoryProgressContainer');
            const storyUserAvatar = document.getElementById('highlightStoryAvatar');
            const storyTitle = document.getElementById('highlightStoryTitle');
            const storyCount = document.getElementById('highlightStoryCount');
            const storyPrev = document.getElementById('highlightStoryPrev');
            const storyNext = document.getElementById('highlightStoryNext');

            let currentStories = [];
            let currentStoryIndex = 0;
            let progressInterval;
            let isPaused = false;
            let videoEnded = false;
            let currentVideo = null;
            let highlightTitle = '';
            let highlightCover = '';

            // Click handler for highlight items
            document.querySelectorAll('.highlight:not(.add-highlight)').forEach((highlightItem) => {
                highlightItem.addEventListener('click', function() {
                    const highlightCover = this.dataset.highlight;
                    const username = this.dataset.username;

                    if (highlightCover && username) {
                        openHighlightStories(username, highlightCover);
                    }
                });
            });

            // Close handlers
            closeStoryModal.addEventListener('click', closeStoryViewer);
            storyModal.addEventListener('click', function(e) {
                if (e.target === storyModal) closeStoryViewer();
            });

            // Navigation
            storyPrev.addEventListener('click', (e) => {
                e.stopPropagation();
                navigateStory('prev');
            });

            storyNext.addEventListener('click', (e) => {
                e.stopPropagation();
                navigateStory('next');
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (storyModal.style.display === 'none') return;
                if (e.key === 'ArrowLeft') navigateStory('prev');
                if (e.key === 'ArrowRight') navigateStory('next');
                if (e.key === 'Escape') closeStoryViewer();
            });

            function openHighlightStories(username, highlightCover) {

                // Show loading state
                storyContent.innerHTML = '<div class="highlight-story-loading">Loading stories...</div>';
                storyModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';

                // Fetch highlight stories with GET request
                fetch(`/profile/${username}/${highlightCover}/show`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status && data.data && data.data.length > 0) {
                            // Combine stories from all highlight records
                            let allStories = [];
                            let title = '';
                            let cover = '';

                            data.data.forEach((highlight, index) => {
                                if (highlight.stories && highlight.stories.length > 0) {
                                    highlight.stories.forEach(story => {
                                        allStories.push(story);
                                    });
                                }
                                if (!title) title = highlight.title || 'Highlight';
                                if (!cover) cover = highlight.cover || '';
                            });

                            currentStories = allStories;
                            currentStoryIndex = 0;
                            highlightTitle = title;
                            highlightCover = cover;
                            videoEnded = false;
                            currentVideo = null;


                            if (currentStories.length === 0) {
                                storyContent.innerHTML = '<div class="highlight-story-loading">No stories in this highlight</div>';
                                return;
                            }

                            // Set highlight cover as avatar
                            const username = currentStories[0]?.user?.email?.split('@')[0] || '';
                            storyUserAvatar.src = `/users/highlights/${username}/${highlightCover}`;
                            storyTitle.textContent = highlightTitle;

                            updateStoryUI();
                            createProgressBars();
                            setTimeout(() => startProgress(), 100);
                        } else {
                            storyContent.innerHTML = '<div class="highlight-story-loading">No stories available</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        storyContent.innerHTML = '<div class="highlight-story-loading">Error loading stories</div>';
                    });
            }

            function updateStoryUI() {
                // Clean up previous video
                if (currentVideo) {
                    currentVideo.pause();
                    currentVideo.removeAttribute('src');
                    currentVideo.load();
                    currentVideo = null;
                }

                clearInterval(progressInterval);
                videoEnded = false;

                const story = currentStories[currentStoryIndex];
                if (!story) return;

                // Get email_prefix from the user relationship
                const emailPrefix = story.user?.email ? story.user.email.split('@')[0] : '';
                const mediaUrl = `/users/stories/${story.media_type}/${emailPrefix}/${story.media}`;

                const isVideo = story.media_type === 'video' || mediaUrl.match(/\.(mp4|mov|avi|webm)$/i);
                const isGif = mediaUrl.match(/\.gif$/i);

                // Clear content
                storyContent.innerHTML = '';

                if (isVideo) {
                    const video = document.createElement('video');
                    video.className = 'highlight-story-media';
                    video.src = mediaUrl;
                    video.autoplay = true;
                    video.playsInline = true;
                    video.preload = 'auto';
                    video.id = 'highlightStoryVideo';

                    storyContent.appendChild(video);
                    currentVideo = video;

                    video.muted = false;
                    video.volume = 1.0;

                    video.play().catch(() => {
                        video.muted = true;
                        showUnmuteButton(video);
                    });

                    video.addEventListener('ended', function() {
                        if (!videoEnded) {
                            videoEnded = true;
                            markCurrentComplete();
                            setTimeout(() => navigateStory('next'), 500);
                        }
                    });

                    video.addEventListener('timeupdate', function() {
                        if (!isPaused && video.duration && !videoEnded) {
                            const progress = (video.currentTime / video.duration) * 100;
                            updateProgressBar(progress);
                        }
                    });

                } else if (isGif) {
                    const video = document.createElement('video');
                    video.className = 'highlight-story-media';
                    video.src = mediaUrl;
                    video.autoplay = true;
                    video.loop = true;
                    video.muted = true;
                    video.playsInline = true;
                    video.preload = 'auto';

                    storyContent.appendChild(video);
                    currentVideo = video;
                    startImageProgress(10000);
                } else {
                    const img = document.createElement('img');
                    img.className = 'highlight-story-media';
                    img.src = mediaUrl;
                    img.alt = 'Story';

                    storyContent.appendChild(img);
                    startImageProgress(10000);
                }

                // Add tap zones
                const leftZone = document.createElement('div');
                leftZone.className = 'highlight-story-tap-zone highlight-story-tap-zone-left';
                leftZone.onclick = (e) => { e.stopPropagation(); navigateStory('prev'); };

                const rightZone = document.createElement('div');
                rightZone.className = 'highlight-story-tap-zone highlight-story-tap-zone-right';
                rightZone.onclick = (e) => { e.stopPropagation(); navigateStory('next'); };

                storyContent.appendChild(leftZone);
                storyContent.appendChild(rightZone);
            }

            function showUnmuteButton(video) {
                const existingBtn = document.querySelector('.highlight-unmute-btn');
                if (existingBtn) existingBtn.remove();

                const unmuteBtn = document.createElement('button');
                unmuteBtn.className = 'highlight-unmute-btn';
                unmuteBtn.innerHTML = '🔇 Tap to unmute';

                unmuteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    video.muted = false;
                    video.volume = 1.0;
                    video.play().catch(() => {});
                    unmuteBtn.remove();
                });

                storyContent.appendChild(unmuteBtn);
            }

            function updateProgressBar(progress) {
                const bars = storyProgressContainer.querySelectorAll('.highlight-story-progress-bar-fill');
                const currentBar = bars[currentStoryIndex];
                if (currentBar) {
                    currentBar.style.width = Math.min(progress, 100) + '%';
                }
            }

            function markCurrentComplete() {
                const bars = storyProgressContainer.querySelectorAll('.highlight-story-progress-bar-fill');
                const currentBar = bars[currentStoryIndex];
                if (currentBar) {
                    currentBar.style.width = '100%';
                    currentBar.classList.add('completed');
                }
            }

            function createProgressBars() {
                storyProgressContainer.innerHTML = '';
                currentStories.forEach((story, index) => {
                    const bg = document.createElement('div');
                    bg.className = 'highlight-story-progress-bar-bg';

                    const fill = document.createElement('div');
                    fill.className = 'highlight-story-progress-bar-fill';

                    if (index < currentStoryIndex) {
                        fill.classList.add('completed');
                        fill.style.width = '100%';
                    }
                    if (index === currentStoryIndex) {
                        fill.classList.add('active');
                    }

                    bg.appendChild(fill);
                    storyProgressContainer.appendChild(bg);
                });
            }

            function startImageProgress(duration) {
                clearInterval(progressInterval);
                const bars = storyProgressContainer.querySelectorAll('.highlight-story-progress-bar-fill');
                const currentBar = bars[currentStoryIndex];
                if (!currentBar) return;

                let progress = 0;
                const interval = 50;
                const increment = (interval / duration) * 100;

                progressInterval = setInterval(() => {
                    if (isPaused) return;
                    progress += increment;
                    updateProgressBar(progress);
                    if (progress >= 100) {
                        clearInterval(progressInterval);
                        markCurrentComplete();
                        setTimeout(() => navigateStory('next'), 500);
                    }
                }, interval);
            }

            function startProgress() {
                clearInterval(progressInterval);
                const story = currentStories[currentStoryIndex];
                if (!story) return;

                const emailPrefix = story.user?.email ? story.user.email.split('@')[0] : '';
                const mediaUrl = `/users/stories/${story.media_type}/${emailPrefix}/${story.media}`;
                const isVideo = story.media_type === 'video' || mediaUrl.match(/\.(mp4|mov|avi|webm)$/i);
                const isGif = mediaUrl.match(/\.gif$/i);

                if (!isVideo && !isGif) {
                    startImageProgress(10000);
                } else if (isGif) {
                    startImageProgress(10000);
                }
                // Video progress is handled by timeupdate event
            }

            function navigateStory(direction) {
                clearInterval(progressInterval);

                if (currentVideo) {
                    currentVideo.pause();
                    currentVideo.removeAttribute('src');
                    currentVideo.load();
                    currentVideo = null;
                }

                videoEnded = false;

                if (direction === 'next') {
                    if (currentStoryIndex < currentStories.length - 1) {
                        currentStoryIndex++;
                        updateStoryUI();
                        createProgressBars();
                        setTimeout(() => startProgress(), 100);
                    } else {
                        closeStoryViewer();
                        return;
                    }
                } else if (direction === 'prev') {
                    if (currentStoryIndex > 0) {
                        currentStoryIndex--;
                        updateStoryUI();
                        createProgressBars();
                        setTimeout(() => startProgress(), 100);
                    }
                }
            }

            function closeStoryViewer() {
                clearInterval(progressInterval);

                if (currentVideo) {
                    currentVideo.pause();
                    currentVideo.removeAttribute('src');
                    currentVideo.load();
                    currentVideo = null;
                }

                storyModal.style.display = 'none';
                document.body.style.overflow = '';
                storyContent.innerHTML = '';
                storyProgressContainer.innerHTML = '';
                currentStories = [];
                currentStoryIndex = 0;
                videoEnded = false;
                isPaused = false;
                highlightTitle = '';
                highlightCover = '';
            }

            // Pause on hover
            storyContent.addEventListener('mouseenter', () => {
                isPaused = true;
                if (currentVideo) currentVideo.pause();
            });

            storyContent.addEventListener('mouseleave', () => {
                isPaused = false;
                if (currentVideo) {
                    currentVideo.play().catch(() => {});
                }
            });

            // Touch events for mobile
            let touchStartX = 0;
            let touchStartY = 0;

            storyContent.addEventListener('touchstart', (e) => {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
                isPaused = true;
                if (currentVideo) currentVideo.pause();
            });

            storyContent.addEventListener('touchend', (e) => {
                const diffX = touchStartX - e.changedTouches[0].clientX;
                const diffY = touchStartY - e.changedTouches[0].clientY;

                if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                    diffX > 0 ? navigateStory('next') : navigateStory('prev');
                }

                isPaused = false;
                if (currentVideo) {
                    currentVideo.play().catch(() => {});
                }
            });
        });
    </script>
@endsection
