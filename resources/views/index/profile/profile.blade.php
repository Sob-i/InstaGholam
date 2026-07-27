
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
                        <span class="verified"><svg width="14" height="14" fill="var(--accent2)" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Verified</span>
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

                    @endif
                </div>
                <div class="profile-stats">
                    <div class="stat"><div class="stat-num">{{$data['postsCount']}}</div><div class="stat-label">Posts</div></div>
                    <div class="stat"><div class="stat-num">{{$data['followersCount']}}</div><div class="stat-label">Followers</div></div>
                    <div class="stat"><div class="stat-num">{{$data['followingCount']}}</div><div class="stat-label">Following</div></div>
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

        @if($data['canViewProfile'] == 'true')
                        <!-- Highlights -->
                        <div class="highlights">
                            @if($user->id == auth()->id())
                                <div class="highlight">
                                    <div class="hl-ring add"><div class="hl-av h5">＋</div></div>
                                    <span class="hl-name">New</span>
                                </div>
                            @endif

                            <div class="highlight">
                                <div class="hl-ring"><div class="hl-av h1"></div></div>
                                <span class="hl-name">Lisbon</span>
                            </div>
                            <div class="highlight">
                                <div class="hl-ring"><div class="hl-av h2"></div></div>
                                <span class="hl-name">Morocco</span>
                            </div>
                            <div class="highlight">
                                <div class="hl-ring"><div class="hl-av h3"></div></div>
                                <span class="hl-name">Work</span>
                            </div>
                            <div class="highlight">
                                <div class="hl-ring"><div class="hl-av h4"></div></div>
                                <span class="hl-name">Studio</span>
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
@endsection
