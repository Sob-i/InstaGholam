@extends('layouts.front.messages.main')
@section('content')
    <div class="main">
        <div class="messages-col">
            <!-- header -->
            <div class="page-header">
                <span class="page-icon">✉️</span>
                <span class="page-title">Messages</span>
            </div>
            <div class="page-sub">Your conversations — tap any thread to open.</div>

            <!-- thread list -->
            <div class="thread-list">
                <!-- thread 1 -->
                @forelse($user->Chats as $chat)
                    <div class="thread-item">
                        @forelse($chat->members as $member)
                            @if($member->id != $user->id)
                                <div class="thread-avatar"><img src="{{asset('/users/avatar/'.$member->avatar)}}" alt="user"></div>
                                <div class="thread-info">
                                    <a href="{{route('message.page.show', $member->id)}}" style="text-decoration: none; color: white;">
                                    <div class="thread-name">{{$member->username}}</div>
                                    <div class="thread-preview">{{$chat->LastMessage->message}}</div>
                                    </a>
                                </div>
                                <div class="thread-meta">
                                    @if($chat->lastReadMessageCount)
                                        <span class="thread-unread">{{$chat->lastReadMessageCount}}</span>
                                    @endif
                                    <span class="thread-time" style="color: white">{{$chat->LastMessage->created_at->diffForHumans()}}</span>
                                </div>
                            @endif
                        @empty
                        @endforelse

                    </div>

                @empty

                @endforelse
            </div>

            <!-- optional "request" section like notifications style -->
            <div style="margin-top: 12px; border-top: 1px solid var(--border); padding-top: 20px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <span style="font-size: 16px;">📨</span>
                    <span style="font-weight: 600;">Message requests</span>
                    <span style="background: var(--accent); color: #000; font-size: 11px; padding: 0 8px; border-radius: 12px; line-height: 20px;">2</span>
                </div>
                <div class="thread-item" style="background: transparent; border: 1px dashed var(--border);">
                    <div class="thread-avatar"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Crect width='48' height='48' fill='%23f7971e'/%3E%3Ctext x='14' y='32' font-size='22' fill='black' font-family='Inter'%3E👤%3C/text%3E%3C/svg%3E" alt="user"></div>
                    <div class="thread-info">
                        <div class="thread-name">nova_explorer</div>
                        <div class="thread-preview" style="color: var(--accent);">Request from nova_explorer</div>
                    </div>
                    <div style="display: flex; gap: 6px;">
                        <button class="follow-btn" style="background: var(--accent); color: #000;">Accept</button>
                        <button class="follow-btn" style="background: transparent; border-color: var(--border); color: var(--muted);">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
