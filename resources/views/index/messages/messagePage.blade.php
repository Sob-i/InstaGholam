@extends('layouts.front.messageSingle.main')
@section('content')
    <div class="main">
        <div class="messages-col">

            <div class="search-wrap">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" placeholder="Search for this conversation messages…" />
            </div>

            <!-- OPENED THREAD (the "opened one" you asked for) -->
            <div class="opened-thread">
                <!-- header with back button -->
                <div class="thread-header">
                    <a href="{{route('messages.show')}}" class="back-btn" style="text-decoration: none">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                    <div class="thread-user">
                        @forelse($chat->members as $member)
                            @if($member->id != auth()->id())
                                <input type="hidden" id="receiver_id" value="{{$member->id}}">
                                <div class="avatar"><img src="{{asset('users/avatar/'.$member->avatar)}}" alt="{{$member->username}}"></div>
                                <div>
                                    <div class="name">{{$member->username}}</div>
                                    <div class="status">Active now</div>
                                </div>
                            @endif
                        @empty
                        @endforelse

                    </div>
                    <button style="background:none;border:none;color:var(--muted);cursor:pointer;padding:4px;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                    </button>
                </div>

                <!-- messages -->
                <div class="thread-messages">
                    @forelse($chat->messages as $message)
                        @if($message->sender_id == auth()->id())
                            <div class="message sent">
                                {{$message->message}}
                                <span class="time">{{$message->created_at->diffForHumans()}}</span>
                            </div>
                        @else
                            <div class="message received">
                                {{$message->message}}
                                <span class="time">{{$message->created_at->diffForHumans()}}</span>
                            </div>
                        @endif
                    @empty
                        There is no message yet Send something and start the conversation!
                    @endforelse
                </div>

                <!-- input -->
                <div class="thread-input">
                    <input type="text" id="message_txt" placeholder="Type a message…" />
                    <button class="send-btn">Send</button>
                </div>
            </div>

        </div>
    </div>

@endsection
