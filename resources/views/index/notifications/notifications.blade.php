
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
            @forelse($notifications['newNotifications'] as $notification)
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
                        <div class="notif-text"><strong><a href="{{route('profile',$notification->user->username)}}" style="text-decoration: none; color: white">{{$notification->user->username}}</a></strong> {{$notification->message}}</div>
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
                    @elseif($notification->type == 'request')
                        @if(!$notification->isFollowed)
                            <form method="POST" action="{{route('profile.user.follow.accept',$notification->user->id)}}">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn-edit accept-follow">
                                    {{ $notification->isFollowed ? 'following' : 'Accept' }}
                                </button>
                            </form>
                        @else
                            <span class="btn-edit accept-follow">Following</span>
                        @endif
                    @endif
                    <div class="unread-dot"></div>
                </div>
            @empty
                <div class="notif-item">
                    <div class="notif-content">
                        <div class="notif-text">No New Notifications for today.</div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- THIS WEEK -->
        <div class="notif-section">
            <div class="notif-section-label">This Week</div>

            @forelse($notifications['oldNotifications'] as $notification)
                @php
                    if (!empty($notification->post->post_files)){
                        $firstFile = strtok($notification->post->post_files, ',');
                        $fileExtension = strtolower(pathinfo($firstFile, PATHINFO_EXTENSION));
                        $isVideo = in_array($fileExtension, ['mp4', 'mov', 'avi', 'webm']);
                        $postPath = 'users/posts/' . strstr($notification->targetUser->email, '@', true) . '-posts/' . $notification->post->created_at->format('Y-m-d') . '/' . $firstFile;
                    }
                @endphp
                <div class="notif-item">
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
                        <div class="notif-text"><strong><a href="{{route('profile',$notification->user->username)}}" style="text-decoration: none; color: white">{{$notification->user->username}}</a></strong> {{$notification->message}}</div>
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
                    @elseif($notification->type == 'request')
                        @if(!$notification->isFollowed)
                            <form method="POST" action="{{route('profile.user.follow.accept',$notification->user->id)}}">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn-edit accept-follow">
                                    {{ $notification->isFollowed ? 'following' : 'Accept' }}
                                </button>
                            </form>
                        @else
                            <span class="btn-edit accept-follow">Following</span>
                        @endif
                    @endif
                </div>
            @empty
            @endforelse
    </div>
@endsection

