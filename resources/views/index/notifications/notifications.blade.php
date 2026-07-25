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

            <div class="notif-item unread">
                <div class="notif-av"><div class="av a"></div><div class="notif-badge badge-heart">♥</div></div>
                <div class="notif-content">
                    <div class="notif-text"><strong>maya_k</strong> and 341 others liked your photo.</div>
                    <div class="notif-time">2 minutes ago</div>
                </div>
                <div class="notif-thumb t1"></div>
                <div class="unread-dot"></div>
            </div>

            <div class="notif-item unread">
                <div class="notif-av"><div class="av b"></div><div class="notif-badge badge-comment">💬</div></div>
                <div class="notif-content">
                    <div class="notif-text"><strong>sunseeker</strong> commented: "this is absolutely insane 🔥 the colors!"</div>
                    <div class="notif-time">8 minutes ago</div>
                </div>
                <div class="notif-thumb t2"></div>
                <div class="unread-dot"></div>
            </div>

            <div class="notif-item unread">
                <div class="notif-av"><div class="av c"></div><div class="notif-badge badge-follow">+</div></div>
                <div class="notif-content">
                    <div class="notif-text"><strong>drone.life</strong> started following you.</div>
                    <div class="notif-time">22 minutes ago</div>
                </div>
                <button class="follow-btn">Follow back</button>
                <div class="unread-dot"></div>
            </div>

            <div class="notif-item unread">
                <div class="notif-av"><div class="av d"></div><div class="notif-badge badge-tag">🏷</div></div>
                <div class="notif-content">
                    <div class="notif-text"><strong>lena_arts</strong> tagged you in a photo.</div>
                    <div class="notif-time">1 hour ago</div>
                </div>
                <div class="notif-thumb t1"></div>
                <div class="unread-dot"></div>
            </div>
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

