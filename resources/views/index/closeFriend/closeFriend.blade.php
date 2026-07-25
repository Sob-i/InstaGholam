@extends('layouts.front.closeFriend.main')
@section('content')
    <div class="main">
        <!-- LEFT CONTENT -->
        <div class="content-col">

            <div class="page-header">
                <div class="page-icon">★</div>
                <div class="page-title">Close Friends</div>
            </div>
            <div class="page-sub">Only people on this list can see your Close Friends stories.</div>

            <!-- Search -->
            <div class="search-wrap">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="searchInput" placeholder="Search followers…"/>
            </div>

            <!-- Current Close Friends -->
            <div class="cf-list" id="cfList">
                @forelse($friends as $friend)
                    @php
                        $f = $friendsCount - 1;
                     @endphp
                    <div class="cf-item">
                        <div class="cf-av-wrap">
                            <img class="cf-av b" src="{{asset('users/avatar/'. $friend->userInfo[$f]->avatar )}}">
                            <div class="cf-ring"></div>
                            <div class="cf-badge">★</div>
                        </div>
                        <div class="cf-info">
                            <div class="cf-name">{{$friend->userInfo[$f]->username}}</div>
                            <div class="cf-handle">@ {{$friend->userInfo[$f]->username}}</div>
                        </div>
                        <button class="cf-toggle in-list" data-toggle="{{$friend->userInfo[$f]->id}}" title="Remove from Close Friends">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </button>
                    </div>
                @empty
                @endforelse

                @forelse($followings as $following)
                    @php
                    $friend = $friend->friend_id ?? 0;
                     @endphp
                    @if($following->followed_id == $friend)

                        @else
                            <div class="cf-item">
                                <div class="cf-av-wrap">
                                    <img class="cf-av b" src="{{asset('users/avatar/'. $following->userInfo->avatar )}}">
                                </div>
                                <div class="cf-info">
                                    <div class="cf-name">{{$following->userInfo->username}}</div>
                                    <div class="cf-handle">{{'@' . $following->userInfo->username}}</div>
                                </div>
                                <button class="cf-toggle follow" data-toggle="{{$following->userInfo->id}}" title="Add to Close Friends">
                                    <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                    </svg>
                                </button>
                            </div>
                    @endif
                @empty
                @endforelse

                <!-- Empty state (shown when all removed) -->
                <div class="empty-state" id="cfEmpty" style="display:none;">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <p>No close friends yet.<br/>Add people from your followers below.</p>
                </div>
            </div>
        </div>

        <!-- RIGHT INFO PANEL -->
        <div class="info-panel">

            <!-- Avatar stack of current CF list -->
            <div class="count-bubble">
                <div class="count-num" id="bigCount">{{$friendsCount}}</div>
                <div class="count-label">close friends</div>
            </div>

            <div class="info-card">
                <div class="info-card-head">
                    <div class="info-card-icon">★</div>
                    <div class="info-card-title">How it works</div>
                </div>
                <div class="info-card-body">
                    <div class="info-row">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Your Close Friends stories disappear after <strong>24 hours</strong> just like regular stories.</span>
                    </div>
                    <div class="info-row">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                        <span>Only people on <strong>this list</strong> can see your Close Friends stories.</span>
                    </div>
                    <div class="info-row">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                        <span>People are <strong>not notified</strong> when you add or remove them.</span>
                    </div>
                    <div class="info-row">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span>A <strong>green ring</strong> appears on your story for people on this list.</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
