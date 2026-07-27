
@extends('layouts.front.index.main')

@section('content')
    <div class="main">
        <div class="feed-col">
            <div class="post">
                <div class="post-header">
                    <a class="sidebar-user" href="{{route('profile',$post->user->username)}}" style="text-decoration: none; color: white">
                        <img src="{{asset('users/avatar/'.$post->user->avatar)}}" class="sidebar-avatar" alt="{{$post->user->username}}">
                        <div class="post-username">{{$post->user->username}}</div>
                    </a>
                    <div class="post-meta">
                        <div class="post-location">{{$post->post_location}}</div>
                    </div>
                    @if($post->user_id != $user->id)
                        <button class="btn-edit custom-a {{ $postWithInfo['isFollowed'] ? 'following' : 'not-following' }}"
                                data-username="{{ $post->user->username }}">
                            {{ $postWithInfo['isFollowed'] ? 'Following' : 'Follow' }}
                        </button>
                    @endif
                    <div class="post-more">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <circle cx="12" cy="5" r="1"/>
                            <circle cx="12" cy="12" r="1"/>
                            <circle cx="12" cy="19" r="1"/>
                        </svg>
                    </div>
                </div>

                <!-- Image Slider -->
                <div class="post-img-slider" style="position: relative; height:420px; overflow: hidden;">
                    @php
                        $isVideo = false;
                        $postFiles = $postWithInfo['postFiles'];
                        $folderName = $postWithInfo['folderName'];
                        $date = $postWithInfo['date'];
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
                                    $fileUrl = asset('users/posts/'.$folderName.'-posts/'.$date.'/'.$file);
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
                        <button class="action-btn like-btn {{ $post->isLikedByUser(auth()->id()) ? 'liked' : '' }}"
                                data-post-id="{{ $post->id }}"
                                data-show-count="true"
                                aria-label="{{ $post->isLikedByUser(auth()->id()) ? 'Unlike' : 'Like' }}">
                            <svg class="like-icon" width="20" height="20" fill="{{ $post->isLikedByUser(auth()->id()) ? '#ed4956' : 'none' }}"
                                 stroke="{{ $post->isLikedByUser(auth()->id()) ? '#ed4956' : 'currentColor' }}"
                                 stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                            </svg>
                            <span class="likes-count">{{ $post->post_likes }}</span>
                        </button>
                    @elseif($post->like_count == 'notVisible' && $post->user_id != auth()->id())
                        <button class="action-btn like-btn {{ $post->isLikedByUser(auth()->id()) ? 'liked' : '' }}"
                                data-post-id="{{ $post->id }}"
                                data-show-count="false"
                                aria-label="{{ $post->isLikedByUser(auth()->id()) ? 'Unlike' : 'Like' }}">
                            <svg class="like-icon" width="20" height="20" fill="{{ $post->isLikedByUser(auth()->id()) ? '#ed4956' : 'none' }}"
                                 stroke="{{ $post->isLikedByUser(auth()->id()) ? '#ed4956' : 'currentColor' }}"
                                 stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                            </svg>
                            <span class="likes-count" style="display: none;"></span>
                        </button>
                    @else
                        <button class="action-btn like-btn {{ $post->isLikedByUser(auth()->id()) ? 'liked' : '' }}"
                                data-post-id="{{ $post->id }}"
                                data-show-count="true"
                                aria-label="{{ $post->isLikedByUser(auth()->id()) ? 'Unlike' : 'Like' }}">
                            <svg class="like-icon" width="20" height="20" fill="{{ $post->isLikedByUser(auth()->id()) ? '#ed4956' : 'none' }}"
                                 stroke="{{ $post->isLikedByUser(auth()->id()) ? '#ed4956' : 'currentColor' }}"
                                 stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                            </svg>
                            <span class="likes-count">{{ $post->post_likes }}</span>
                        </button>
                    @endif

                    <button class="action-btn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                        </svg>
                        {{$post->post_comments}}
                    </button>
                    <button class="action-btn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8"/>
                            <polyline points="16 6 12 2 8 6"/>
                            <line x1="12" y1="2" x2="12" y2="15"/>
                        </svg>
                    </button>
                    <div class="spacer"></div>
                    <button class="action-btn save-btn {{ $post->isSavedByUser(auth()->id()) ? 'saved' : '' }}"
                            data-post-id="{{ $post->id }}"
                            aria-label="{{ $post->isSavedByUser(auth()->id()) ? 'Unsave' : 'Save' }}">
                        <svg class="save-icon" width="20" height="20"
                             fill="{{ $post->isSavedByUser(auth()->id()) ? 'var(--accent)' : 'none' }}"
                             stroke="{{ $post->isSavedByUser(auth()->id()) ? 'var(--accent)' : 'currentColor' }}"
                             stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/>
                        </svg>
                    </button>
                </div>

                <div class="post-caption"><strong>{{$post->user->username}}</strong> {!! $post->post_caption !!}</div>
                <div class="post-caption">
                    <a href="{{route('explore',['search' => $post->post_tags])}}">{{$post->post_tags}}</a>
                </div>
                <div class="post-time">{{ $post->created_at->diffForHumans() }}</div>

                @if($post->comment_status == 'closed')
                    <div class="no-comments" style="color: #8e8e8e; font-size: 14px; padding: 15px 0; padding-left: 12px;">
                        No comments allowed for this post.
                    </div>
                @else
                    <!-- Comments Section -->
                    <div class="post-comments" style="padding: 0 16px; max-height: 200px; overflow-y: auto;">
                        <div class="comments-list" id="comments-list-{{ $post->id }}">
                            @if(isset($postWithInfo['comments']) && count($postWithInfo['comments']) > 0)
                                @foreach($postWithInfo['comments'] as $comment)
                                    <div class="comment-item">
                                        <img src="{{asset('users/avatar/'.$comment->user->avatar)}}"
                                             class="comment-avatar"
                                             alt="{{ $comment->user->username }}">
                                        <div class="comment-content">
                                            <strong class="comment-username">{{ $comment->user->username ?? 'Unknown' }}</strong>
                                            <span class="comment-text">{{ $comment->content }}</span>
                                            <span class="post-time" style="color: #8e8e8e; font-size: 10px; margin-right: 8px; direction: ltr; display: inline-block;">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="no-comments" id="no-comments-{{ $post->id }}" style="color: #8e8e8e; font-size: 14px; padding: 10px 0;">
                                    No comments yet. Be the first to comment!
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Comment Input -->
                    <div class="container">
                        <div class="comment-box">
                            <input
                                type="text"
                                class="comment-input"
                                name="comment"
                                placeholder="Add a comment…"
                                id="comment-input-{{ $post->id }}"
                            />
                            <button
                                class="post-btn submit-comment"
                                data-post-id="{{ $post->id }}"
                            >
                                Post
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
