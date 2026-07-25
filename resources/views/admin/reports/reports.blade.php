@extends('layouts.admin.reports.main')
@section('content')

    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Reports</span>
            <span class="urgent-badge">14 open · 3 urgent</span>
            <div class="spacer"></div>
        </div>

        <div class="content">
            <!-- REPORT LIST -->
            <div class="report-list">
                <div class="list-head">
                    <span class="list-title">Open Reports</span>
                    <div class="filter-tabs">
                        <button class="ftab active">All</button>
                        <button class="ftab">Posts</button>
                        <button class="ftab">Users</button>
                        <button class="ftab">Comments</button>
                    </div>
                </div>
                <div class="report-items">
                    <div class="report-item active-item">
                        <div class="ri-thumb t1"></div>
                        <div class="ri-info">
                            <div class="ri-reason">Inappropriate content</div>
                            <div class="ri-target">Post by @nxt.studio</div>
                            <div class="ri-meta">
                                <span class="ri-count">⚑ 8 reports</span>
                                <span class="ri-time">2h ago</span>
                                <span class="ri-priority pri-high">HIGH</span>
                            </div>
                        </div>
                    </div>
                    <div class="report-item">
                        <div class="ri-thumb t2"></div>
                        <div class="ri-info">
                            <div class="ri-reason">Spam / misleading</div>
                            <div class="ri-target">Reel by @user_4421</div>
                            <div class="ri-meta">
                                <span class="ri-count">⚑ 5 reports</span>
                                <span class="ri-time">4h ago</span>
                                <span class="ri-priority pri-high">HIGH</span>
                            </div>
                        </div>
                    </div>
                    <div class="report-item">
                        <div class="ri-thumb text-type">💬</div>
                        <div class="ri-info">
                            <div class="ri-reason">Hate speech</div>
                            <div class="ri-target">Comment by @anon_88x</div>
                            <div class="ri-meta">
                                <span class="ri-count">⚑ 4 reports</span>
                                <span class="ri-time">5h ago</span>
                                <span class="ri-priority pri-high">HIGH</span>
                            </div>
                        </div>
                    </div>
                    <div class="report-item">
                        <div class="ri-thumb t3"></div>
                        <div class="ri-info">
                            <div class="ri-reason">Impersonation</div>
                            <div class="ri-target">Profile @alex_rivera2</div>
                            <div class="ri-meta">
                                <span class="ri-count">⚑ 3 reports</span>
                                <span class="ri-time">8h ago</span>
                                <span class="ri-priority pri-med">MED</span>
                            </div>
                        </div>
                    </div>
                    <div class="report-item">
                        <div class="ri-thumb t4"></div>
                        <div class="ri-info">
                            <div class="ri-reason">Copyright infringement</div>
                            <div class="ri-target">Photo by @drone.life</div>
                            <div class="ri-meta">
                                <span class="ri-count">⚑ 2 reports</span>
                                <span class="ri-time">12h ago</span>
                                <span class="ri-priority pri-med">MED</span>
                            </div>
                        </div>
                    </div>
                    <div class="report-item">
                        <div class="ri-thumb text-type">💬</div>
                        <div class="ri-info">
                            <div class="ri-reason">Harassment / bullying</div>
                            <div class="ri-target">Comment by @user_9912</div>
                            <div class="ri-meta">
                                <span class="ri-count">⚑ 1 report</span>
                                <span class="ri-time">1d ago</span>
                                <span class="ri-priority pri-low">LOW</span>
                            </div>
                        </div>
                    </div>
                    <div class="report-item">
                        <div class="ri-thumb t5"></div>
                        <div class="ri-info">
                            <div class="ri-reason">Self-harm content</div>
                            <div class="ri-target">Story by @user_3318</div>
                            <div class="ri-meta">
                                <span class="ri-count">⚑ 1 report</span>
                                <span class="ri-time">1d ago</span>
                                <span class="ri-priority pri-low">LOW</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DETAIL + ACTIONS -->
            <div class="detail-panel">
                <!-- Report Detail -->
                <div class="detail-card">
                    <div class="detail-head">
                        <span class="detail-title">Report #1094 — Inappropriate content</span>
                        <span class="detail-badge">8 reports · HIGH</span>
                    </div>
                    <div class="detail-body">
                        <div class="content-preview"></div>
                        <div class="detail-row">
                            <span class="detail-label">Content type</span>
                            <span class="detail-val">Photo post</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Posted by</span>
                            <span class="detail-val"><strong>@nxt.studio</strong> · <a href="admin-users.html">View profile</a></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Posted at</span>
                            <span class="detail-val">Jun 16, 2025 at 09:41 AM</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Caption</span>
                            <span class="detail-val">"🔥🔥🔥 #viral #trending"</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Report reasons</span>
                            <span class="detail-val">Nudity or sexual content (5), Violence or graphic content (3)</span>
                        </div>
                        <div style="margin-top:16px;">
                            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Reporters</div>
                            <div class="reporters-list">
                                <div class="reporter-row"><div class="rep-av ra"></div><span class="rep-name">maya_k</span><span class="rep-reason">Nudity or sexual content</span><span class="rep-time">2h ago</span></div>
                                <div class="reporter-row"><div class="rep-av rb"></div><span class="rep-name">sunseeker</span><span class="rep-reason">Nudity or sexual content</span><span class="rep-time">2h ago</span></div>
                                <div class="reporter-row"><div class="rep-av rc"></div><span class="rep-name">lena_arts</span><span class="rep-reason">Violence or graphic content</span><span class="rep-time">3h ago</span></div>
                                <div style="font-size:12px;color:var(--muted);margin-top:4px;">+ 5 more reporters</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="action-card">
                    <div class="action-title">Moderator Action</div>
                    <div class="action-btns">
                        <button class="action-btn approve">
                            ✓ Dismiss report — content is acceptable
                            <span>No action taken. Reporters will be notified.</span>
                        </button>
                        <button class="action-btn hide">
                            ◑ Hide post from public feed
                            <span>Post remains but is not shown publicly.</span>
                        </button>
                        <button class="action-btn remove">
                            ✕ Remove post permanently
                            <span>Post deleted. User receives a strike.</span>
                        </button>
                        <button class="action-btn suspend">
                            ⊘ Remove post + suspend account
                            <span>Post deleted and @nxt.studio suspended.</span>
                        </button>
                    </div>
                    <textarea rows="3" placeholder="Add a moderator note (optional)…"></textarea>
                </div>
            </div>
        </div>
    </div>

@endsection
