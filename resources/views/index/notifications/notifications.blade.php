
@extends('layouts.front.notifications.main')
@section('content')
    <div class="main">
        <div class="page-title">Notifications</div>

        <div class="filter-tabs">
            <button class="ftab active">All</button>
            <button class="ftab">Likes</button>
            <button class="ftab">Comments</button>
            <button class="ftab">Follows</button>
            <button class="ftab">Mentions</button>
        </div>

        <!-- NEW -->
        <div class="notif-section">
            <div class="notif-section-label">New</div>
            @forelse($notifications as $notification)
                @php
                    if (!empty($notification->post->post_files)){
                        $firstFile = strtok($notification->post->post_files, ',');
                        $fileExtension = strtolower(pathinfo($firstFile, PATHINFO_EXTENSION));
                        $isVideo = in_array($fileExtension, ['mp4', 'mov', 'avi', 'webm']);
                        $postPath = 'users/posts/' . strstr($notification->targetUser->email, '@', true) . '-posts/' . $notification->post->created_at->format('Y-m-d') . '/' . $firstFile;
                    }
                @endphp
                <div class="notif-item unread">
                    <div class="notif-av"><img src="{{asset('users/avatar/'.$notification->user->avatar)}}" class="sidebar-avatar">
                        @if($notification->type == 'like')
                            <div class="notif-badge badge-heart">♥</div>
                        @elseif($notification->type == 'comment')
                            <div class="notif-badge badge-comment">💬</div>
                        @elseif($notification->type == 'tagged')
                            <div class="notif-badge badge-tag">🏷</div>
                        @elseif($notification->type == 'follow')
                            <div class="notif-badge badge-follow">+</div>
                        @endif
                    </div>
                    <div class="notif-content">
                        <div class="notif-text"><strong>{{$notification->user->username}}</strong> {{$notification->message}}</div>
                        <div class="notif-time">{{$notification->created_at->diffForHumans()}}</div>
                    </div>
                    @if($notification->post)
                        <a href="{{route('post.show',$notification->post->id)}}" class="notif-thumb t1">
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
                        </a>
                    @elseif($notification->type == 'follow')
                        <button class="btn-edit custom-a {{ $notification->isFollowed ? 'following' : 'not-following' }}"
                                data-username="{{ $notification->user->username }}"
                                data-follow-url="{{ route('profile.user.follow', $notification->user->username) }}">
                            {{ $notification->isFollowed  ? 'Following' : 'Follow' }}
                        </button>
                    @endif
                    <div class="unread-dot"></div>
                </div>
            @empty
            @endforelse
        </div>

        <!-- THIS WEEK -->
        <div class="notif-section">
            <div class="notif-section-label">This Week</div>

            <div class="notif-item">
                <div class="notif-av"><div class="av e"></div><div class="notif-badge badge-heart">♥</div></div>
                <div class="notif-content">
                    <div class="notif-text"><strong>trail.run</strong> liked your comment: "the warm tones here 🙌"</div>
                    <div class="notif-time">Yesterday</div>
                </div>
                <div class="notif-thumb t2"></div>
            </div>

            <div class="notif-item">
                <div class="notif-av"><div class="av f"></div><div class="notif-badge badge-follow">+</div></div>
                <div class="notif-content">
                    <div class="notif-text"><strong>nxt.studio</strong> started following you.</div>
                    <div class="notif-time">2 days ago</div>
                </div>
                <button class="following-btn">Following</button>
            </div>

            <div class="notif-item">
                <div class="notif-av"><div class="av a"></div><div class="notif-badge badge-comment">💬</div></div>
                <div class="notif-content">
                    <div class="notif-text"><strong>maya_k</strong> replied to your comment: "right?! I was there for sunset every evening for a week"</div>
                    <div class="notif-time">3 days ago</div>
                </div>
                <div class="notif-thumb t1"></div>
            </div>

            <div class="notif-item">
                <div class="notif-av"><div class="av b"></div><div class="notif-badge badge-heart">♥</div></div>
                <div class="notif-content">
                    <div class="notif-text"><strong>sunseeker</strong> and 88 others liked your photo.</div>
                    <div class="notif-time">4 days ago</div>
                </div>
                <div class="notif-thumb t2"></div>
            </div>
        </div>
    </div>
@endsection

