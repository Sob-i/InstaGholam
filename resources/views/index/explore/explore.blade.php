@extends('layouts.front.explore.main')
@section('content')
    <div class="main">
        <div class="search-bar">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.35-4.35"/>
            </svg>
            <input type="text" placeholder="Search people, places, tags…"/>
        </div>

        <div class="grid">
            @php
                // Define different height classes for variety
                $heightClasses = ['g1', 'g2', 'g3', 'g4', 'g5', 'g6', 'g7', 'g8', 'g9'];
                $minHeights = [
                    'g1' => '403px',
                    'g2' => '200px',
                    'g3' => '300px',
                    'g4' => '350px',
                    'g5' => '250px',
                    'g6' => '400px',
                    'g7' => '280px',
                    'g8' => '320px',
                    'g9' => '380px'
                ];
                $posts = $data['posts']
            @endphp

            @forelse($posts as $index => $post)
                @php
                    $firstFile = strtok($post->post_files, ',');
                    $fileExtension = pathinfo($firstFile, PATHINFO_EXTENSION);
                    $isVideo = in_array(strtolower($fileExtension), ['mp4', 'mov', 'avi', 'webm']);
                    $heightClass = $heightClasses[$index % count($heightClasses)];
                    $minHeight = $minHeights[$heightClass];
                    $folderName = strstr($post->user->email, '@', true) . '-posts';
                    $datePath = $post->created_at->format('Y-m-d');
                @endphp
                <div class="grid-item">
                    <a href="{{route('post.show', $post->id)}}">
                        <div class="grid-img {{ $heightClass }}" style="min-height:{{ $minHeight }};">
                            @if($isVideo)
                                <video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;"
                                       muted
                                       preload="metadata"
                                       disablepictureinpicture
                                       disableremoteplayback>
                                    <source src="{{ asset('users/posts/' . $folderName . '/' . $datePath . '/' . $firstFile) }}"
                                            type="video/{{ $fileExtension }}">
                                </video>
                            @else
                                <img src="{{ asset('users/posts/' . $folderName . '/' . $datePath . '/' . $firstFile) }}"
                                     style="object-fit: cover; object-position: center; width: 100%; height: 100%;">
                            @endif
                        </div>
                        <div class="overlay" style="color: white">
                                @if($post->like_count == 'visible')
                                    <span>♥ {{$post->post_likes}}</span>
                                @elseif($post->like_count == 'notVisible' && $post->user_id != auth()->id())
                                    <span>♥ Hidden</span>
                                @else
                                    <span>♥ {{$post->post_likes}}</span>
                                @endif
                                @if($post->comment_status == 'open')
                                    <span>💬 {{$post->post_comments}}</span>
                                @else
                                    <span>💬 Closed</span>
                                @endif
                            </div>
                    </a>
                </div>
            @empty
                <p>No posts found</p>
            @endforelse
        </div>
    </div>
@endsection
