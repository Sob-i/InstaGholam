
@extends('layouts.front.index.main')
@section('content')
    @php
        $storiesForCircles = $data['storiesForCircles'];
        $posts = $data['posts'];
        $isCloseFriendsId = $data['isCloseFriendsId'];
        $storiesJson = $data['stories'];
    @endphp
    <div class="main">
        <!-- FEED -->
        <div class="feed-col">
            <!-- Stories Section -->
            <div class="stories-section">
                <div class="stories-row">

                    @unless($storiesForCircles->contains('user_id', auth()->id()))
                        <div class="story-item">
                            <div class="story-ring add-btn">
                                <a href="{{route('story.new.show')}}" class="story-av me" style="text-decoration: none">＋</a>
                            </div>
                            <span class="story-name">Your Story</span>
                        </div>
                    @endunless

                    @php
                        $sortedStories = $storiesForCircles->sortBy(function($story) {
                            return $story->user_id == auth()->id() ? 0 : 1;
                        });
                    @endphp

                    @foreach($sortedStories as $story)
                        @if($story->audience == 'closeFriends' && !in_array($user->id,$isCloseFriendsId) && $story->user_id != $user->id)
                            @continue
                        @endif

                        <div class="story-item" data-username="{{ $story->user->username }}">
                            <div class="story-ring has-story">
                                <img src="{{ asset('users/avatar/'.$story->user->avatar) }}"
                                     class="story-av"
                                     alt="{{ $story->user->username }}">
                            </div>
                            <span class="story-name">{{ $story->user->username }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            @foreach($posts as $post)
                @php
                    $showPost = true;

                    if($post->post_audience == 'closeFriends') {
                        if(in_array($user->id, $isCloseFriendsId) && $post->user_id != $user->id) {
                            $showPost = false;
                        }
                    }
                @endphp

                @if($showPost)
                    <div class="post">
                        <div class="post-header">
                            <a class="sidebar-user" href="{{ route('profile',$post->user->username) }}" style="text-decoration: none; color: white">
                                <img src="{{ asset('users/avatar/'.$post->user->avatar) }}" class="sidebar-avatar">
                                <div class="post-username">{{ $post->user->username }}</div>
                            </a>
                            <div class="post-meta">
                                <div class="post-location">{{ $post->post_location }}</div>
                            </div>
                            @if($post->user_id != $user->id)
                                <button class="btn-edit custom-a {{ $post->isFollowed ? 'following' : 'not-following' }}"
                                        data-username="{{ $post->user->username }}">
                                    {{ $post->isFollowed ? 'Following' : 'Follow' }}
                                </button>
                            @endif
                            <!-- Three-dot menu button -->
                            <div class="dropdown-wrapper">
                                <button class="btn-three-dot" onclick="toggleDropdown(event)">
                                    <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                                        <circle cx="12" cy="5" r="2"/>
                                        <circle cx="12" cy="12" r="2"/>
                                        <circle cx="12" cy="19" r="2"/>
                                    </svg>
                                </button>

                                <div class="dropdown-menu">
                                    @if(auth()->id() != $post->user_id)
                                        <span
                                            class="dropdown-item report-btn"
                                            data-id="{{ $post->id }}"
                                            data-type="post"
                                            data-uid="{{$post->user_id}}">
                                        Report
                                         </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <!-- Report Modal -->
                        <div id="reportModal" class="report-modal">
                            <div class="report-modal-content">
                                <div class="report-modal-header">
                                    <h3>Report</h3>
                                    <button type="button" id="closeReportModal">&times;</button>
                                </div>

                                <form id="reportForm">
                                    @csrf

                                    <input type="hidden" id="reported-user-id" name="id">
                                    <input type="hidden" id="report-type" name="type">
                                    <input type="hidden" id="reporter-uid">

                                    <div class="report-options">

                                        <label>
                                            <input type="radio" name="report-subject" value="spam" required>
                                            Spam
                                        </label>

                                        <label>
                                            <input type="radio" name="report-subject" value="harassment">
                                            Harassment or Bullying
                                        </label>

                                        <label>
                                            <input type="radio" name="report-subject" value="hate_speech">
                                            Hate Speech
                                        </label>

                                        <label>
                                            <input type="radio" name="report-subject" value="violence">
                                            Violence or Dangerous Content
                                        </label>

                                        <label>
                                            <input type="radio" name="report-subject" value="nudity">
                                            Nudity or Sexual Content
                                        </label>

                                        <label>
                                            <input type="radio" name="report-subject" value="false_information">
                                            False Information
                                        </label>

                                        <label>
                                            <input type="radio" name="report-subject" value="other">
                                            Other
                                        </label>

                                    </div>

                                    <div class="report-buttons">
                                        <button type="button" id="cancelReport">Cancel</button>
                                        <button type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Image Slider -->
                        <div class="post-img-slider" style="position: relative; height:420px; overflow: hidden;">
                            @php
                                $postFiles = $post->media_files;
                                $folderName = $post->post_folder_name;
                                $isVideo = false;
                                if(count($postFiles) > 0) {
                                    $firstFileExtension = pathinfo($postFiles[0], PATHINFO_EXTENSION);
                                    $isVideo = in_array(strtolower($firstFileExtension), ['mp4', 'mov', 'avi', 'webm']);
                                }
                            @endphp

                            @if(count($postFiles) > 0)
                                <div class="slides-container" style="display: flex; transition: transform 0.3s ease-in-out; height: 100%;">
                                    @foreach($postFiles as $index => $file)
                                        @php
                                            $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                            $fileUrl = asset('users/posts/'.$folderName.'-posts/'.$post->post_date.'/'.$file);
                                        @endphp
                                        <div class="slide" style="min-width: 100%; height: 100%; position: relative;">
                                            @if(in_array(strtolower($fileExtension), ['mp4', 'mov', 'avi', 'webm']))
                                                <video class="slide-video"
                                                       src="{{ $fileUrl }}"
                                                       style="width: 100%; height: 100%; object-fit: cover;"
                                                       autoplay
                                                       loop
                                                       muted
                                                       playsinline
                                                       preload="metadata">
                                                    Your browser does not support the video tag.
                                                </video>
                                                <button class="mute-toggle-btn"
                                                        style="position: absolute; bottom: 40px; right: 10px; background: rgba(0,0,0,0.6); border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10;"
                                                        aria-label="Toggle mute">
                                                    <svg class="muted-icon" width="16" height="16" fill="white" viewBox="0 0 24 24">
                                                        <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                                                    </svg>
                                                    <svg class="unmuted-icon" width="16" height="16" fill="white" viewBox="0 0 24 24" style="display: none;">
                                                        <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <img src="{{ $fileUrl }}"
                                                     style="width: 100%; height: 100%; object-fit: cover;"
                                                     alt="Post image {{ $index + 1 }}"
                                                     loading="lazy">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                @if(count($postFiles) > 1)
                                    <button class="slider-btn prev-btn"
                                            style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.8); border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10;"
                                            aria-label="Previous slide">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="15 18 9 12 15 6"/>
                                        </svg>
                                    </button>
                                    <button class="slider-btn next-btn"
                                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.8); border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10;"
                                            aria-label="Next slide">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="9 18 15 12 9 6"/>
                                        </svg>
                                    </button>

                                    <div class="dots-container" style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); display: flex; gap: 6px; z-index: 10;">
                                        @foreach($postFiles as $index => $file)
                                            <span class="dot {{ $index == 0 ? 'active' : '' }}"
                                                  data-index="{{ $index }}"
                                                  style="width: 8px; height: 8px; border-radius: 50%; background: {{ $index == 0 ? '#0095f6' : '#a8a8a8' }}; cursor: pointer;">
                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div style="width: 100%; height: 100%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999;">
                                    No Media
                                </div>
                            @endif
                        </div>

                        <div class="post-actions">
                            @if($post->like_count == 'visible')
                                <!-- Like Button with Animation -->
                                <button class="action-btn like-btn {{ $post->isLikedByUser ? 'liked' : '' }}"
                                        data-post-id="{{ $post->id }}"
                                        aria-label="{{ $post->isLikedByUser ? 'Unlike' : 'Like' }}">
                                    <svg class="like-icon" width="20" height="20" fill="{{ $post->isLikedByUser ? '#ed4956' : 'none' }}"
                                         stroke="{{ $post->isLikedByUser ? '#ed4956' : 'currentColor' }}"
                                         stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                                    </svg>
                                    <span class="likes-count">{{ $post->post_likes }}</span>
                                </button>
                            @elseif($post->like_count == 'notVisible' and $post->user_id == auth()->id())
                                <!-- Like Button with Animation -->
                                <button class="action-btn like-btn {{ $post->isLikedByUser ? 'liked' : '' }}"
                                        data-post-id="{{ $post->id }}"
                                        aria-label="{{ $post->isLikedByUser ? 'Unlike' : 'Like' }}">
                                    <svg class="like-icon" width="20" height="20" fill="{{ $post->isLikedByUser ? '#ed4956' : 'none' }}"
                                         stroke="{{ $post->isLikedByUser ? '#ed4956' : 'currentColor' }}"
                                         stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                                    </svg>
                                    <span class="likes-count">{{ $post->post_likes }}</span>
                                </button>
                            @else
                                <!-- Like Button with Animation -->
                                <button class="action-btn like-btn {{ $post->isLikedByUser ? 'liked' : '' }}"
                                        data-post-id="{{ $post->id }}"
                                        aria-label="{{ $post->isLikedByUser ? 'Unlike' : 'Like' }}">
                                    <svg class="like-icon" width="20" height="20" fill="{{ $post->isLikedByUser ? '#ed4956' : 'none' }}"
                                         stroke="{{ $post->isLikedByUser ? '#ed4956' : 'currentColor' }}"
                                         stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                                    </svg>
                                    <span class="likes-count">Hidden</span>
                                </button>
                            @endif
                                @if($post->comment_status != 'closed')
                                    <button
                                        class="action-btn comments-count"
                                        data-post-id="{{ $post->id }}"
                                        data-post-user-id="{{ $post->user_id }}"
                                    >
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                         viewBox="0 0 24 24">
                                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                                    </svg>

                                    <span class="comment-count-number">
                                        {{ $post->post_comments }}
                                    </span>
                                </button>
                                @endif
                            <button class="action-btn">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24">
                                    <path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8"/>
                                    <polyline points="16 6 12 2 8 6"/>
                                    <line x1="12" y1="2" x2="12" y2="15"/>
                                </svg>
                            </button>
                            <div class="spacer"></div>
                            <button class="action-btn save-btn {{ $post->isSavedByUser ? 'saved' : '' }}"
                                    data-post-id="{{ $post->id }}"
                                    aria-label="{{ $post->isSavedByUser ? 'Unsave' : 'Save' }}">
                                <svg class="save-icon" width="20" height="20"
                                     fill="{{ $post->isSavedByUser ? 'var(--accent)' : 'none' }}"
                                     stroke="{{ $post->isSavedByUser ? 'var(--accent)' : 'currentColor' }}"
                                     stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/>
                                </svg>
                            </button>
                        </div>

                        <div class="post-caption"><strong>{{ $post->user->username }}</strong> {!! $post->post_caption !!}</div>
                        <div class="post-caption"><a href="{{route('explore',['search' => $post->post_tags])}}">{{$post->post_tags}}</a></div>
                        <div class="post-time">{{ $post->created_at->diffForHumans() }}</div>

                    </div>
                @endif

            @endforeach
            <!-- Comments Modal -->
            <div id="commentsModal" class="comments-modal">

                <div class="comments-modal-content">

                    <!-- Header -->
                    <div class="comments-modal-header">

                        <strong>Comments</strong>

                        <button
                            type="button"
                            class="comments-modal-close"
                            id="closeCommentsModal"
                        >
                            &times;
                        </button>

                    </div>


                    <!-- Comments -->
                    <div
                        class="comments-modal-body"
                        id="modalCommentsList"
                    >

                        <div
                            class="comments-loading"
                            id="commentsInitialLoading"
                        >
                            Loading comments...
                        </div>

                    </div>


                    <!-- Bottom loading indicator -->
                    <div
                        id="commentsMoreLoading"
                        class="comments-more-loading"
                    >
                        Loading more comments...
                    </div>


                    <!-- Input -->
                    <div class="comments-modal-footer">

                        <div
                            id="replyingIndicator"
                            class="replying-indicator"
                        >
                            <span id="replyingText"></span>

                            <button
                                type="button"
                                id="cancelReply"
                            >
                                ×
                            </button>
                        </div>

                        <div class="comments-input-row">

                            <input
                                type="text"
                                id="modalCommentInput"
                                class="modal-comment-input"
                                placeholder="Add a comment…"
                                autocomplete="off"
                            >

                            <button
                                type="button"
                                id="modalSubmitComment"
                                class="modal-comment-submit"
                            >
                                Post
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel">
            <div class="rp-section">
                <div class="rp-title">Suggested for you</div>
                <div class="suggest-user">
                    <div class="suggest-av sa"></div>
                    <div class="suggest-info">
                        <div class="suggest-name">lena_arts</div>
                        <div class="suggest-sub">Followed by maya_k</div>
                    </div>
                    <button class="follow-btn">Follow</button>
                </div>
                <div class="suggest-user">
                    <div class="suggest-av sb"></div>
                    <div class="suggest-info">
                        <div class="suggest-name">trail.run</div>
                        <div class="suggest-sub">New to Gram</div>
                    </div>
                    <button class="follow-btn">Follow</button>
                </div>
                <div class="suggest-user">
                    <div class="suggest-av sc"></div>
                    <div class="suggest-info">
                        <div class="suggest-name">drone.life</div>
                        <div class="suggest-sub">Followed by sunseeker</div>
                    </div>
                    <button class="follow-btn">Follow</button>
                </div>
                <div class="suggest-user">
                    <div class="suggest-av sd"></div>
                    <div class="suggest-info">
                        <div class="suggest-name">nxt.studio</div>
                        <div class="suggest-sub">Popular creator</div>
                    </div>
                    <button class="follow-btn">Follow</button>
                </div>
            </div>
            <div class="rp-section">
                <div class="rp-title">Trending Tags</div>
                <span class="trending-tag">#travel</span>
                <span class="trending-tag">#goldenhour</span>
                <span class="trending-tag">#surflife</span>
                <span class="trending-tag">#architecture</span>
                <span class="trending-tag">#streetart</span>
                <span class="trending-tag">#minimalism</span>
            </div>
            <div class="rp-footer">
                <a href="#">About</a> · <a href="#">Help</a> · <a href="#">Privacy</a> · <a href="#">Terms</a><br/>
                © 2025 Gram
            </div>
        </div>

        <!-- Story Modal -->
        <div class="story-modal-overlay" id="storyModal" style="display: none;">
            <div class="story-modal">
                <!-- Close Button -->
                <button class="story-modal-close" id="closeStoryModal">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>

                <!-- Story Header -->
                <div class="story-modal-header">
                    <div class="story-user-info">
                        <img src="" class="story-user-avatar" id="storyUserAvatar" alt="">
                        <span class="story-username" id="storyUsername"></span>
                        <span class="story-time" id="storyTime"></span>
                        <span class="story-username" id="storyCloseFriend" style="background-color: green"></span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="story-progress-container" id="storyProgressContainer">
                    <!-- Progress bars will be dynamically added here -->
                </div>

                <!-- Story Content -->
                <div class="story-content" id="storyContent">
                    <!-- Story media will be dynamically loaded here -->
                </div>

                <!-- Navigation Buttons -->
                <button class="story-nav story-nav-prev" id="storyPrev">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button class="story-nav story-nav-next" id="storyNext">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>

                <!-- Story Input/Reply -->
                <div class="story-reply-bar">
                    <input type="text" class="story-reply-input" placeholder="Send message">
                    <button class="story-reply-send">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <script>

            const rawStories = @json($storiesJson);
            const currentUserId = @json(auth()->id());
            let filteredStories = [];
            rawStories.forEach(story => {
                if (story.audience === 'closeFriends' && story.is_close_friend === false) {
                    return;
                }
                filteredStories.push(story);
            });

            window.allStories = filteredStories;
        </script>

        <script>const CURRENT_USER_ID = {{ auth()->id() ?? 'null' }};</script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const storyModal = document.getElementById('storyModal');
                const closeStoryModal = document.getElementById('closeStoryModal');
                const storyContent = document.getElementById('storyContent');
                const storyProgressContainer = document.getElementById('storyProgressContainer');
                const storyUserAvatar = document.getElementById('storyUserAvatar');
                const storyUsername = document.getElementById('storyUsername');
                const storyTime = document.getElementById('storyTime');
                const storyCloseFriend = document.getElementById('storyCloseFriend');
                const storyPrev = document.getElementById('storyPrev');
                const storyNext = document.getElementById('storyNext');

                let currentStories = [];
                let currentStoryIndex = 0;
                let progressInterval;
                let isPaused = false;
                let videoEnded = false;
                let currentVideo = null;

                // Group stories by username
                const storiesByUser = {};
                if (window.allStories) {
                    window.allStories.forEach(story => {
                        const username = story.user.username;
                        if (!storiesByUser[username]) {
                            storiesByUser[username] = [];
                        }
                        storiesByUser[username].push(story);
                    });
                }

                // Click handler for story items
                document.querySelectorAll('.story-item').forEach((storyItem) => {
                    storyItem.addEventListener('click', function() {
                        if (this.querySelector('.add-btn')) return;

                        const username = this.querySelector('.story-name')?.textContent || this.dataset.username;

                        if (username && storiesByUser[username]) {
                            openUserStories(username);
                        }
                    });
                });

                closeStoryModal.addEventListener('click', closeStoryViewer);
                storyModal.addEventListener('click', function(e) {
                    if (e.target === storyModal) closeStoryViewer();
                });

                storyPrev.addEventListener('click', () => navigateStory('prev'));
                storyNext.addEventListener('click', () => navigateStory('next'));

                document.addEventListener('keydown', function(e) {
                    if (storyModal.style.display === 'none') return;
                    if (e.key === 'ArrowLeft') navigateStory('prev');
                    if (e.key === 'ArrowRight') navigateStory('next');
                    if (e.key === 'Escape') closeStoryViewer();
                });

                function openUserStories(username) {
                    const stories = storiesByUser[username];

                    if (!stories || stories.length === 0) {
                        storyContent.innerHTML = '<div style="color: white; text-align: center; padding: 40px;">No stories available</div>';
                        storyModal.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                        return;
                    }

                    currentStories = stories;
                    currentStoryIndex = 0;
                    videoEnded = false;
                    currentVideo = null;

                    storyModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';

                    updateStoryUI();
                    createProgressBars();
                    startProgress();
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

                    storyUserAvatar.src = `/users/avatar/${story.user.avatar}`;
                    storyUsername.textContent = story.user.username;
                    storyTime.textContent = story.created_at;
                    if (story.audience === 'closeFriends') {
                        storyCloseFriend.textContent = '★';
                        storyCloseFriend.style.display = 'inline-block';
                    } else {
                        storyCloseFriend.textContent = '';
                        storyCloseFriend.style.display = 'none';
                    }


                    const mediaUrl = `/users/stories/${story.media_type}/${story.email_prefix}/${story.media}`;
                    const isVideo = story.media_type === 'video' || mediaUrl.match(/\.(mp4|mov|avi|webm)$/i);
                    const isGif = mediaUrl.match(/\.gif$/i);

                    // Clear content
                    storyContent.innerHTML = '';

                    if (isVideo) {
                        // Video - NO LOOP so it ends properly
                        storyContent.innerHTML = `<video class="story-media" src="${mediaUrl}" autoplay playsinline preload="auto" id="storyVideo"></video>`;
                        currentVideo = storyContent.querySelector('#storyVideo');

                        if (currentVideo) {
                            currentVideo.muted = false;
                            currentVideo.volume = 1.0;

                            currentVideo.play().catch(() => {
                                currentVideo.muted = true;
                                showUnmuteButton(currentVideo);
                            });

                            // Listen for video end
                            currentVideo.addEventListener('ended', function() {
                                if (!videoEnded) {
                                    videoEnded = true;
                                    markCurrentComplete();
                                    nextStory();
                                }
                            });

                            // Update progress bar as video plays
                            currentVideo.addEventListener('timeupdate', function() {
                                if (!isPaused && currentVideo.duration && !videoEnded) {
                                    const progress = (currentVideo.currentTime / currentVideo.duration) * 100;
                                    updateProgressBar(progress);
                                }
                            });
                        }
                    } else if (isGif) {
                        // GIF - looped and muted
                        storyContent.innerHTML = `<video class="story-media" src="${mediaUrl}" autoplay loop muted playsinline preload="auto" id="storyVideo"></video>`;
                        currentVideo = storyContent.querySelector('#storyVideo');
                        startImageProgress(10000);
                    } else {
                        // Regular image - 10 seconds
                        storyContent.innerHTML = `<img class="story-media" src="${mediaUrl}" alt="Story">`;
                        startImageProgress(10000);
                    }

                    // Add tap zones
                    const leftZone = document.createElement('div');
                    leftZone.className = 'story-tap-zone story-tap-zone-left';
                    leftZone.onclick = (e) => { e.stopPropagation(); navigateStory('prev'); };

                    const rightZone = document.createElement('div');
                    rightZone.className = 'story-tap-zone story-tap-zone-right';
                    rightZone.onclick = (e) => { e.stopPropagation(); navigateStory('next'); };

                    storyContent.appendChild(leftZone);
                    storyContent.appendChild(rightZone);
                }

                function showUnmuteButton(video) {
                    const existingBtn = document.querySelector('.unmute-btn');
                    if (existingBtn) existingBtn.remove();

                    const unmuteBtn = document.createElement('button');
                    unmuteBtn.className = 'unmute-btn';
                    unmuteBtn.innerHTML = '🔇 Tap to unmute';
                    unmuteBtn.style.cssText = `
            position: absolute;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.7);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            z-index: 20;
            font-size: 14px;
        `;

                    unmuteBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        video.muted = false;
                        video.volume = 1.0;
                        video.play();
                        unmuteBtn.remove();
                    });

                    storyContent.appendChild(unmuteBtn);
                }

                function updateProgressBar(progress) {
                    const bars = storyProgressContainer.querySelectorAll('.story-progress-bar-fill');
                    const currentBar = bars[currentStoryIndex];
                    if (currentBar) {
                        currentBar.style.width = Math.min(progress, 100) + '%';
                    }
                }

                function markCurrentComplete() {
                    const bars = storyProgressContainer.querySelectorAll('.story-progress-bar-fill');
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
                        bg.className = 'story-progress-bar-bg';
                        const fill = document.createElement('div');
                        fill.className = 'story-progress-bar-fill';
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
                    const bars = storyProgressContainer.querySelectorAll('.story-progress-bar-fill');
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
                            nextStory();
                        }
                    }, interval);
                }

                function startProgress() {
                    clearInterval(progressInterval);
                    const story = currentStories[currentStoryIndex];
                    if (!story) return;

                    const mediaUrl = `/users/stories/${story.media_type}/${story.email_prefix}/${story.media}`;
                    const isVideo = story.media_type === 'video' || mediaUrl.match(/\.(mp4|mov|avi|webm)$/i);
                    const isGif = mediaUrl.match(/\.gif$/i);

                    if (!isVideo && !isGif) {
                        startImageProgress(10000);
                    } else if (isGif) {
                        startImageProgress(10000);
                    }
                    // Video progress is handled by timeupdate event
                }

                function nextStory() {
                    clearInterval(progressInterval);

                    if (currentVideo) {
                        currentVideo.pause();
                        currentVideo.removeAttribute('src');
                        currentVideo.load();
                        currentVideo = null;
                    }

                    videoEnded = false;

                    if (currentStoryIndex < currentStories.length - 1) {
                        currentStoryIndex++;
                        updateStoryUI();
                        createProgressBars();
                        startProgress();
                    } else {
                        closeStoryViewer();
                    }
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
                        } else {
                            closeStoryViewer();
                            return;
                        }
                    } else if (direction === 'prev') {
                        if (currentStoryIndex > 0) {
                            currentStoryIndex--;
                        } else {
                            return;
                        }
                    }

                    updateStoryUI();
                    createProgressBars();
                    startProgress();
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
                }

                storyContent.addEventListener('mouseenter', () => {
                    isPaused = true;
                    if (currentVideo) currentVideo.pause();
                });

                storyContent.addEventListener('mouseleave', () => {
                    isPaused = false;
                    if (currentVideo) currentVideo.play();
                });

                let touchStartX = 0;
                storyContent.addEventListener('touchstart', (e) => {
                    touchStartX = e.touches[0].clientX;
                    isPaused = true;
                    if (currentVideo) currentVideo.pause();
                });

                storyContent.addEventListener('touchend', (e) => {
                    const diff = touchStartX - e.changedTouches[0].clientX;
                    if (Math.abs(diff) > 50) {
                        diff > 0 ? navigateStory('next') : navigateStory('prev');
                    }
                    isPaused = false;
                    if (currentVideo) currentVideo.play();
                });
            });
        </script>

        <script>
            $(document).ready(function () {

                /*
                |--------------------------------------------------------------------------
                | GLOBAL STATE
                |--------------------------------------------------------------------------
                */

                let currentPostId = null;

                let currentPostUserId = null;

                let currentCommentsPage = 1;

                let commentsHasMore = false;

                let commentsLoading = false;

                let replyingToCommentId = null;


                /*
                |--------------------------------------------------------------------------
                | OPEN COMMENTS MODAL
                |--------------------------------------------------------------------------
                */

                $(document).on('click', '.comments-count', function (e) {

                    e.preventDefault();

                    const $button = $(this);

                    currentPostId = $button.data('post-id');

                    currentPostUserId = $button.data('post-user-id');

                    openCommentsModal();

                });


                function openCommentsModal() {

                    currentCommentsPage = 1;

                    commentsHasMore = false;

                    commentsLoading = false;

                    replyingToCommentId = null;


                    resetReplyState();


                    $('#commentsModal').addClass('active');


                    $('#modalCommentsList').html(`
            <div class="comments-loading">
                Loading comments...
            </div>
        `);


                    $('#commentsMoreLoading')
                        .removeClass('active');


                    loadComments(1, true);

                }


                /*
                |--------------------------------------------------------------------------
                | GET COMMENTS
                |--------------------------------------------------------------------------
                */

                function loadComments(page, initialLoad = false) {

                    if (commentsLoading) {
                        return;
                    }


                    if (!initialLoad && !commentsHasMore) {
                        return;
                    }


                    commentsLoading = true;


                    if (!initialLoad) {

                        $('#commentsMoreLoading')
                            .addClass('active');

                    }


                    $.ajax({

                        url: `/post/${currentPostId}/comments`,

                        method: 'GET',

                        data: {
                            page: page
                        },


                        success: function (response) {

                            if (!response.status) {

                                if (initialLoad) {

                                    $('#modalCommentsList').html(`
                            <div class="no-modal-comments">
                                No comments yet. Be the first to comment!
                            </div>
                        `);

                                }

                                commentsHasMore = false;

                                return;
                            }


                            if (initialLoad) {

                                $('#modalCommentsList').empty();

                            }


                            if (
                                response.comments &&
                                response.comments.length > 0
                            ) {

                                response.comments.forEach(function (comment) {

                                    appendComment(comment);

                                });

                            }


                            currentCommentsPage =
                                response.current_page;

                            commentsHasMore =
                                response.has_more;


                            if (
                                initialLoad &&
                                (
                                    !response.comments ||
                                    response.comments.length === 0
                                )
                            ) {

                                $('#modalCommentsList').html(`
                        <div class="no-modal-comments">
                            No comments yet. Be the first to comment!
                        </div>
                    `);

                            }

                        },


                        error: function (xhr) {

                            console.error(
                                'Comments loading error:',
                                xhr.responseText
                            );


                            if (initialLoad) {

                                $('#modalCommentsList').html(`
                        <div class="no-modal-comments">
                            Failed to load comments.
                        </div>
                    `);

                            }

                        },


                        complete: function () {

                            commentsLoading = false;

                            $('#commentsMoreLoading')
                                .removeClass('active');

                        }

                    });

                }


                /*
                |--------------------------------------------------------------------------
                | CREATE COMMENT HTML
                |--------------------------------------------------------------------------
                */

                function appendComment(comment) {

                    const avatar = comment.user.avatar
                        ? `/users/avatar/${comment.user.avatar}`
                        : `/users/avatar/default-avatar.png`;


                    let verifiedIcon = '';


                    if (
                        comment.user.role === 'admin' ||
                        comment.user.role === 'verifiedUser'
                    ) {

                        verifiedIcon = `
                <svg
                    class="verified-icon"
                    width="14"
                    height="14"
                    fill="var(--accent)"
                    viewBox="0 0 24 24"
                >
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            `;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SHOW REPLIES BUTTON
                    |--------------------------------------------------------------------------
                    */

                    let repliesButton = '';


                    if (
                        comment.replies_count &&
                        comment.replies_count > 0
                    ) {

                        repliesButton = `

                <button
                    type="button"
                    class="show-replies-btn"
                    data-post-id="${currentPostId}"
                    data-comment-id="${comment.id}"
                    data-replies-count="${comment.replies_count}"
                >

                    View
                    ${comment.replies_count}
                    ${
                            comment.replies_count == 1
                                ? 'reply'
                                : 'replies'
                        }

                </button>

            `;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | REPORT BUTTON
                    |--------------------------------------------------------------------------
                    */

                    let reportButton = '';


                    if (
                        CURRENT_USER_ID != comment.user.id
                    ) {

                        reportButton = `

                <button
                    type="button"
                    class="dropdown-item report-btn"
                    data-id="${comment.id}"
                    data-type="comment"
                    data-uid="${comment.user.id}"
                >
                    Report
                </button>

            `;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DELETE BUTTON
                    |--------------------------------------------------------------------------
                    */

                    let deleteButton = '';


                    if (
                        CURRENT_USER_ID == comment.user.id ||
                        CURRENT_USER_ID == currentPostUserId
                    ) {

                        deleteButton = `

                <button
                    type="button"
                    class="dropdown-item delete-comment"
                    data-id="${comment.id}"
                    data-uid="${comment.user.id}"
                    data-post-id="${currentPostId}"
                    data-post-uid="${currentPostUserId}"
                >
                    Delete
                </button>

            `;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | COMPLETE COMMENT HTML
                    |--------------------------------------------------------------------------
                    */

                    const html = `

            <div
                class="modal-comment-item"
                data-comment-id="${comment.id}"
            >

                <a href="/profile/${encodeURIComponent(comment.user.username)}">

                    <img
                        src="${avatar}"
                        class="modal-comment-avatar"
                        alt="${escapeHtml(comment.user.username)}"
                    >

                </a>


                <div class="modal-comment-content">

                    <div class="modal-comment-top">

                        <a
                            href="/profile/${encodeURIComponent(comment.user.username)}"
                            class="modal-comment-username"
                        >
                            ${escapeHtml(comment.user.username)}
                        </a>

                        ${verifiedIcon}

                    </div>


                    <div class="modal-comment-text">

                        ${escapeHtml(comment.content)}

                    </div>


                    <div class="modal-comment-meta">

                        <span class="modal-comment-time">

                            ${escapeHtml(comment.created_at)}

                        </span>


                        <button
                            type="button"
                            class="modal-reply-btn"
                            data-comment-id="${comment.id}"
                            data-username="${escapeHtml(comment.user.username)}"
                        >
                            Reply
                        </button>

                    </div>


                    ${repliesButton}

                </div>


                <!-- THREE DOT MENU -->

                <div class="modal-comment-menu">

                    <button
                        type="button"
                        class="modal-three-dot"
                        aria-label="Comment options"
                    >

                        <svg
                            width="16"
                            height="16"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <circle
                                cx="12"
                                cy="5"
                                r="2"
                            />

                            <circle
                                cx="12"
                                cy="12"
                                r="2"
                            />

                            <circle
                                cx="12"
                                cy="19"
                                r="2"
                            />

                        </svg>

                    </button>


                    <div class="modal-comment-dropdown">

                        ${reportButton}

                        ${deleteButton}

                    </div>

                </div>

            </div>

        `;


                    $('#modalCommentsList')
                        .append(html);

                }


                /*
                |--------------------------------------------------------------------------
                | LOAD MORE COMMENTS ON SCROLL
                |--------------------------------------------------------------------------
                */

                $('#modalCommentsList').on(
                    'scroll',
                    function () {

                        const element = this;


                        const distanceFromBottom =
                            element.scrollHeight -
                            element.scrollTop -
                            element.clientHeight;


                        if (
                            distanceFromBottom < 150 &&
                            commentsHasMore &&
                            !commentsLoading
                        ) {

                            loadComments(
                                currentCommentsPage + 1,
                                false
                            );

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | THREE DOT MENU
                |--------------------------------------------------------------------------
                */

                $(document).on(
                    'click',
                    '.modal-three-dot',
                    function (e) {

                        e.preventDefault();

                        e.stopPropagation();


                        const $menu =
                            $(this)
                                .siblings('.modal-comment-dropdown');


                        $('.modal-comment-dropdown')
                            .not($menu)
                            .removeClass('active');


                        $menu.toggleClass('active');

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | CLOSE DROPDOWNS WHEN CLICKING OUTSIDE
                |--------------------------------------------------------------------------
                */

                $(document).on(
                    'click',
                    function () {

                        $('.modal-comment-dropdown')
                            .removeClass('active');

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | SHOW REPLIES
                |--------------------------------------------------------------------------
                */

                $(document).on(
                    'click',
                    '.show-replies-btn',
                    function (e) {

                        e.preventDefault();

                        e.stopPropagation();


                        const $button = $(this);


                        const postId =
                            $button.data('post-id');


                        const commentId =
                            $button.data('comment-id');


                        const commentItem =
                            $(
                                `.modal-comment-item[data-comment-id="${commentId}"]`
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Don't load twice
                        |--------------------------------------------------------------------------
                        */

                        if (
                            commentItem.find('.comment-replies').length
                        ) {

                            return;

                        }


                        $button
                            .prop('disabled', true)
                            .text('Loading replies...');


                        $.ajax({

                            url:
                                `/post/${postId}/comment/${commentId}/replies`,

                            method: 'GET',

                            data: {
                                page: 1
                            },


                            success: function (response) {

                                if (!response.status) {

                                    $button
                                        .prop('disabled', false)
                                        .text('No replies');

                                    return;

                                }


                                const $repliesContainer = $(`
                        <div
                            class="comment-replies"
                            data-comment-id="${commentId}"
                        ></div>
                    `);


                                if (
                                    response.replies &&
                                    response.replies.length > 0
                                ) {

                                    response.replies.forEach(
                                        function (reply) {

                                            appendReply(
                                                $repliesContainer,
                                                reply
                                            );

                                        }
                                    );

                                }


                                commentItem
                                    .find('.modal-comment-content')
                                    .append($repliesContainer);


                                $button.remove();


                                /*
                                |--------------------------------------------------------------------------
                                | More replies
                                |--------------------------------------------------------------------------
                                */

                                if (response.has_more) {

                                    $repliesContainer.append(`

                            <button
                                type="button"
                                class="load-more-replies"
                                data-post-id="${postId}"
                                data-comment-id="${commentId}"
                                data-page="${response.current_page}"
                            >
                                View more replies
                            </button>

                        `);

                                }

                            },


                            error: function (xhr) {

                                console.error(
                                    'Replies loading error:',
                                    xhr.responseText
                                );


                                $button
                                    .prop('disabled', false)
                                    .text(
                                        `View ${$button.data('replies-count')} replies`
                                    );

                            }

                        });

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | CREATE REPLY HTML
                |--------------------------------------------------------------------------
                */

                function appendReply($container, reply) {

                    const avatar = reply.user.avatar
                        ? `/users/avatar/${reply.user.avatar}`
                        : `/users/avatar/default-avatar.png`;


                    let verifiedIcon = '';


                    if (
                        reply.user.role === 'admin' ||
                        reply.user.role === 'verifiedUser'
                    ) {

                        verifiedIcon = `
                <svg
                    class="verified-icon"
                    width="13"
                    height="13"
                    fill="var(--accent)"
                    viewBox="0 0 24 24"
                >
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            `;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | REPORT
                    |--------------------------------------------------------------------------
                    */

                    let reportButton = '';


                    if (
                        CURRENT_USER_ID != reply.user.id
                    ) {

                        reportButton = `

                <button
                    type="button"
                    class="dropdown-item report-btn"
                    data-id="${reply.id}"
                    data-type="comment"
                    data-uid="${reply.user.id}"
                >
                    Report
                </button>

            `;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DELETE
                    |--------------------------------------------------------------------------
                    */

                    let deleteButton = '';


                    if (
                        CURRENT_USER_ID == reply.user.id ||
                        CURRENT_USER_ID == currentPostUserId
                    ) {

                        deleteButton = `

                <button
                    type="button"
                    class="dropdown-item delete-comment"
                    data-id="${reply.id}"
                    data-uid="${reply.user.id}"
                    data-post-id="${currentPostId}"
                    data-post-uid="${currentPostUserId}"
                >
                    Delete
                </button>

            `;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | REPLY HTML
                    |--------------------------------------------------------------------------
                    */

                    const html = `

            <div
                class="modal-reply-item"
                data-comment-id="${reply.id}"
            >

                <a href="/profile/${encodeURIComponent(reply.user.username)}">

                    <img
                        src="${avatar}"
                        class="modal-comment-avatar"
                        alt="${escapeHtml(reply.user.username)}"
                    >

                </a>


                <div class="modal-comment-content">

                    <div class="modal-comment-top">

                        <a
                            href="/profile/${encodeURIComponent(reply.user.username)}"
                            class="modal-comment-username"
                        >
                            ${escapeHtml(reply.user.username)}
                        </a>

                        ${verifiedIcon}

                    </div>


                    <div class="modal-comment-text">

                        ${escapeHtml(reply.content)}

                    </div>


                    <div class="modal-comment-meta">

                        <span class="modal-comment-time">

                            ${escapeHtml(reply.created_at)}

                        </span>


                        <!-- REPLY BUTTON ON REPLY -->

                        <button
                            type="button"
                            class="modal-reply-btn"
                            data-comment-id="${reply.id}"
                            data-username="${escapeHtml(reply.user.username)}"
                        >
                            Reply
                        </button>

                    </div>

                </div>


                <!-- THREE DOT MENU -->

                <div class="modal-comment-menu">

                    <button
                        type="button"
                        class="modal-three-dot"
                        aria-label="Reply options"
                    >

                        <svg
                            width="16"
                            height="16"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <circle
                                cx="12"
                                cy="5"
                                r="2"
                            />

                            <circle
                                cx="12"
                                cy="12"
                                r="2"
                            />

                            <circle
                                cx="12"
                                cy="19"
                                r="2"
                            />

                        </svg>

                    </button>


                    <div class="modal-comment-dropdown">

                        ${reportButton}

                        ${deleteButton}

                    </div>

                </div>

            </div>

        `;


                    $container.append(html);

                }


                /*
                |--------------------------------------------------------------------------
                | REPLY BUTTON
                |--------------------------------------------------------------------------
                */

                $(document).on(
                    'click',
                    '.modal-reply-btn',
                    function (e) {

                        e.preventDefault();


                        const commentId =
                            $(this).data('comment-id');


                        const username =
                            $(this).data('username');


                        replyingToCommentId =
                            commentId;


                        $('#replyingText').text(
                            `Replying to @${username}`
                        );


                        $('#replyingIndicator')
                            .addClass('active');


                        $('#modalCommentInput')
                            .attr(
                                'placeholder',
                                `Reply to @${username}...`
                            )
                            .focus();

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | CANCEL REPLY
                |--------------------------------------------------------------------------
                */

                $('#cancelReply').on(
                    'click',
                    function () {

                        resetReplyState();

                    }
                );


                function resetReplyState() {

                    replyingToCommentId = null;


                    $('#replyingIndicator')
                        .removeClass('active');


                    $('#replyingText')
                        .text('');


                    $('#modalCommentInput')
                        .attr(
                            'placeholder',
                            'Add a comment…'
                        )
                        .val('');

                }


                /*
                |--------------------------------------------------------------------------
                | SUBMIT COMMENT / REPLY
                |--------------------------------------------------------------------------
                */

                $('#modalSubmitComment').on(
                    'click',
                    function () {

                        const $button = $(this);

                        const $input =
                            $('#modalCommentInput');


                        const content =
                            $input.val().trim();


                        if (content === '') {

                            $input.focus();

                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | REPLY
                        |--------------------------------------------------------------------------
                        */

                        if (replyingToCommentId) {

                            sendReply(
                                content,
                                $button,
                                $input
                            );

                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | NORMAL COMMENT
                        |--------------------------------------------------------------------------
                        */

                        sendComment(
                            content,
                            $button,
                            $input
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | SEND NORMAL COMMENT
                |--------------------------------------------------------------------------
                */

                function sendComment(
                    content,
                    $button,
                    $input
                ) {

                    $button
                        .prop('disabled', true)
                        .text('Posting...');


                    $.ajax({

                        url:
                            `/post/${currentPostId}/sendComment`,

                        method: 'POST',

                        data: {

                            _token:
                                $('meta[name="csrf-token"]').attr(
                                    'content'
                                ),

                            comment: content

                        },


                        success: function (response) {

                            if (!response.success) {
                                return;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Remove empty state
                            |--------------------------------------------------------------------------
                            */

                            $('#modalCommentsList')
                                .find('.no-modal-comments')
                                .remove();


                            const comment =
                                response.comment;


                            appendCommentToTop(comment);


                            /*
                            |--------------------------------------------------------------------------
                            | Update count
                            |--------------------------------------------------------------------------
                            */

                            updatePostCommentCount(
                                currentPostId,
                                1
                            );


                            $input
                                .val('')
                                .focus();

                        },


                        error: handleAjaxError,


                        complete: function () {

                            $button
                                .prop('disabled', false)
                                .text('Post');

                        }

                    });

                }


                /*
                |--------------------------------------------------------------------------
                | INSERT NEW COMMENT AT TOP
                |--------------------------------------------------------------------------
                */

                function appendCommentToTop(comment) {

                    const avatar = comment.user.avatar
                        ? `/users/avatar/${comment.user.avatar}`
                        : `/users/avatar/default-avatar.png`;


                    let verifiedIcon = '';


                    if (
                        comment.user.role === 'admin' ||
                        comment.user.role === 'verifiedUser'
                    ) {

                        verifiedIcon = `
                <svg
                    class="verified-icon"
                    width="14"
                    height="14"
                    fill="var(--accent)"
                    viewBox="0 0 24 24"
                >
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            `;

                    }


                    let repliesButton = '';


                    if (
                        comment.replies_count &&
                        comment.replies_count > 0
                    ) {

                        repliesButton = `

                <button
                    type="button"
                    class="show-replies-btn"
                    data-post-id="${currentPostId}"
                    data-comment-id="${comment.id}"
                    data-replies-count="${comment.replies_count}"
                >
                    View ${comment.replies_count}
                    ${
                            comment.replies_count == 1
                                ? 'reply'
                                : 'replies'
                        }
                </button>

            `;

                    }


                    let reportButton = '';


                    if (
                        CURRENT_USER_ID != comment.user.id
                    ) {

                        reportButton = `

                <button
                    type="button"
                    class="dropdown-item report-btn"
                    data-id="${comment.id}"
                    data-type="comment"
                    data-uid="${comment.user.id}"
                >
                    Report
                </button>

            `;

                    }


                    let deleteButton = '';


                    if (
                        CURRENT_USER_ID == comment.user.id ||
                        CURRENT_USER_ID == currentPostUserId
                    ) {

                        deleteButton = `

                <button
                    type="button"
                    class="dropdown-item delete-comment"
                    data-id="${comment.id}"
                    data-uid="${comment.user.id}"
                    data-post-id="${currentPostId}"
                    data-post-uid="${currentPostUserId}"
                >
                    Delete
                </button>

            `;

                    }


                    const html = `

            <div
                class="modal-comment-item"
                data-comment-id="${comment.id}"
            >

                <a href="/profile/${encodeURIComponent(comment.user.username)}">

                    <img
                        src="${avatar}"
                        class="modal-comment-avatar"
                        alt="${escapeHtml(comment.user.username)}"
                    >

                </a>


                <div class="modal-comment-content">

                    <div class="modal-comment-top">

                        <a
                            href="/profile/${encodeURIComponent(comment.user.username)}"
                            class="modal-comment-username"
                        >
                            ${escapeHtml(comment.user.username)}
                        </a>

                        ${verifiedIcon}

                    </div>


                    <div class="modal-comment-text">

                        ${escapeHtml(comment.content)}

                    </div>


                    <div class="modal-comment-meta">

                        <span class="modal-comment-time">
                            ${escapeHtml(
                        comment.created_at || 'Just now'
                    )}
                        </span>


                        <button
                            type="button"
                            class="modal-reply-btn"
                            data-comment-id="${comment.id}"
                            data-username="${escapeHtml(comment.user.username)}"
                        >
                            Reply
                        </button>

                    </div>


                    ${repliesButton}

                </div>


                <div class="modal-comment-menu">

                    <button
                        type="button"
                        class="modal-three-dot"
                    >

                        <svg
                            width="16"
                            height="16"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <circle cx="12" cy="5" r="2"/>
                            <circle cx="12" cy="12" r="2"/>
                            <circle cx="12" cy="19" r="2"/>

                        </svg>

                    </button>


                    <div class="modal-comment-dropdown">

                        ${reportButton}

                        ${deleteButton}

                    </div>

                </div>

            </div>

        `;


                    $('#modalCommentsList')
                        .prepend(html);

                }


                /*
                |--------------------------------------------------------------------------
                | SEND REPLY
                |--------------------------------------------------------------------------
                */

                function sendReply(
                    content,
                    $button,
                    $input
                ) {

                    const parentCommentId =
                        replyingToCommentId;


                    $button
                        .prop('disabled', true)
                        .text('Replying...');


                    $.ajax({

                        url:
                            `/post/${currentPostId}/${parentCommentId}/sendCommentReply`,

                        method: 'POST',

                        data: {

                            _token:
                                $('meta[name="csrf-token"]').attr(
                                    'content'
                                ),

                            reply: content

                        },


                        success: function (response) {

                            if (!response.success) {
                                return;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Your backend returns:
                            |
                            | response.reply
                            |--------------------------------------------------------------------------
                            */

                            const reply = response.reply;


                            /*
                            |--------------------------------------------------------------------------
                            | Find parent comment/reply
                            |--------------------------------------------------------------------------
                            */

                            let $repliesContainer =
                                $(
                                    `.modal-comment-item[data-comment-id="${parentCommentId}"]`
                                ).find('.comment-replies').first();


                            if (!$repliesContainer.length) {

                                $repliesContainer =
                                    $(
                                        `.modal-reply-item[data-comment-id="${parentCommentId}"]`
                                    ).closest('.comment-replies');

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | If replies aren't open yet,
                            | create the container.
                            |--------------------------------------------------------------------------
                            */

                            if (!$repliesContainer.length) {

                                const $parentComment =
                                    $(
                                        `.modal-comment-item[data-comment-id="${parentCommentId}"]`
                                    );


                                if ($parentComment.length) {

                                    $repliesContainer = $(`
                            <div
                                class="comment-replies"
                                data-comment-id="${parentCommentId}"
                            ></div>
                        `);


                                    $parentComment
                                        .find('.modal-comment-content')
                                        .append($repliesContainer);

                                }

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Append the new reply
                            |--------------------------------------------------------------------------
                            */

                            if ($repliesContainer.length) {

                                appendReply(
                                    $repliesContainer,
                                    reply
                                );

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Update reply counter
                            |--------------------------------------------------------------------------
                            */

                            const $parent =
                                $(
                                    `.modal-comment-item[data-comment-id="${parentCommentId}"]`
                                );


                            const $showReplies =
                                $parent.find('.show-replies-btn');


                            if ($showReplies.length) {

                                let count =
                                    parseInt(
                                        $showReplies
                                            .data('replies-count')
                                    ) || 0;


                                count++;


                                $showReplies
                                    .data(
                                        'replies-count',
                                        count
                                    )
                                    .text(
                                        `View ${count} ${
                                            count === 1
                                                ? 'reply'
                                                : 'replies'
                                        }`
                                    );

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Clear reply mode
                            |--------------------------------------------------------------------------
                            */

                            resetReplyState();


                            $input.focus();


                            /*
                            |--------------------------------------------------------------------------
                            | Total post comment count
                            |--------------------------------------------------------------------------
                            */

                            updatePostCommentCount(
                                currentPostId,
                                1
                            );

                        },


                        error: handleAjaxError,


                        complete: function () {

                            $button
                                .prop('disabled', false)
                                .text('Post');

                        }

                    });

                }


                /*
                |--------------------------------------------------------------------------
                | LOAD MORE REPLIES
                |--------------------------------------------------------------------------
                */

                $(document).on(
                    'click',
                    '.load-more-replies',
                    function (e) {

                        e.preventDefault();


                        const $button = $(this);


                        const postId =
                            $button.data('post-id');


                        const commentId =
                            $button.data('comment-id');


                        const currentPage =
                            parseInt(
                                $button.data('page')
                            ) || 1;


                        const nextPage =
                            currentPage + 1;


                        const $container =
                            $button.closest('.comment-replies');


                        $button
                            .prop('disabled', true)
                            .text('Loading...');


                        loadReplies(
                            postId,
                            commentId,
                            nextPage,
                            $container,
                            $button
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | LOAD REPLIES
                |--------------------------------------------------------------------------
                */

                function loadReplies(
                    postId,
                    commentId,
                    page,
                    $container,
                    $button = null
                ) {

                    $.ajax({

                        url:
                            `/post/${postId}/comment/${commentId}/replies`,

                        method: 'GET',

                        data: {
                            page: page
                        },


                        success: function (response) {

                            if (!response.status) {
                                return;
                            }


                            response.replies.forEach(
                                function (reply) {

                                    appendReply(
                                        $container,
                                        reply
                                    );

                                }
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | Remove old button
                            |--------------------------------------------------------------------------
                            */

                            $container
                                .find('.load-more-replies')
                                .remove();


                            /*
                            |--------------------------------------------------------------------------
                            | Add another button if needed
                            |--------------------------------------------------------------------------
                            */

                            if (response.has_more) {

                                $container.append(`

                        <button
                            type="button"
                            class="load-more-replies"
                            data-post-id="${postId}"
                            data-comment-id="${commentId}"
                            data-page="${response.current_page}"
                        >
                            View more replies
                        </button>

                    `);

                            }

                        },


                        error: function (xhr) {

                            console.error(
                                'Replies loading error:',
                                xhr.responseText
                            );


                            if ($button) {

                                $button
                                    .prop('disabled', false)
                                    .text('View more replies');

                            }

                        }

                    });

                }


                /*
                |--------------------------------------------------------------------------
                | ENTER KEY
                |--------------------------------------------------------------------------
                */

                $('#modalCommentInput').on(
                    'keypress',
                    function (e) {

                        if (e.which === 13) {

                            e.preventDefault();

                            $('#modalSubmitComment')
                                .click();

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | UPDATE COMMENT COUNT
                |--------------------------------------------------------------------------
                */

                function updatePostCommentCount(
                    postId,
                    amount
                ) {

                    const $count =
                        $(
                            `.comments-count[data-post-id="${postId}"]`
                        ).find('.comment-count-number');


                    if (!$count.length) {
                        return;
                    }


                    let currentCount =
                        parseInt(
                            $count.text()
                        ) || 0;


                    currentCount += amount;


                    if (currentCount < 0) {

                        currentCount = 0;

                    }


                    $count.text(currentCount);

                }


                /*
                |--------------------------------------------------------------------------
                | CLOSE MODAL
                |--------------------------------------------------------------------------
                */

                $('#closeCommentsModal').on(
                    'click',
                    function () {

                        closeCommentsModal();

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | CLICK OUTSIDE MODAL
                |--------------------------------------------------------------------------
                */

                $('#commentsModal').on(
                    'click',
                    function (e) {

                        if (e.target === this) {

                            closeCommentsModal();

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | ESCAPE
                |--------------------------------------------------------------------------
                */

                $(document).on(
                    'keydown',
                    function (e) {

                        if (
                            e.key === 'Escape' &&
                            $('#commentsModal').hasClass('active')
                        ) {

                            closeCommentsModal();

                        }

                    }
                );


                function closeCommentsModal() {

                    $('#commentsModal')
                        .removeClass('active');


                    currentPostId = null;

                    currentPostUserId = null;

                    currentCommentsPage = 1;

                    commentsHasMore = false;

                    commentsLoading = false;

                    replyingToCommentId = null;


                    resetReplyState();


                    $('.modal-comment-dropdown')
                        .removeClass('active');

                }


                /*
                |--------------------------------------------------------------------------
                | AJAX ERROR HANDLER
                |--------------------------------------------------------------------------
                */

                function handleAjaxError(xhr) {

                    if (xhr.status === 422) {

                        const errors =
                            xhr.responseJSON.errors;

                        let errorMessage = '';


                        for (let field in errors) {

                            errorMessage +=
                                errors[field].join('\n') +
                                '\n';

                        }


                        alert(errorMessage);

                    }
                    else if (xhr.status === 401) {

                        alert('Please login');

                    }
                    else if (xhr.status === 403) {

                        alert('You are not allowed to do this.');

                    }
                    else if (xhr.status === 500) {

                        alert('An error occurred.');

                        console.error(
                            'Server Error:',
                            xhr.responseText
                        );

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | ESCAPE HTML
                |--------------------------------------------------------------------------
                */

                function escapeHtml(value) {

                    if (
                        value === null ||
                        value === undefined
                    ) {

                        return '';

                    }


                    return $('<div>')
                        .text(value)
                        .html();

                }

            });
        </script>
@endsection
