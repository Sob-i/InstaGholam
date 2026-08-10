@extends('layouts.admin.dashboard.main')
@section('content')
    <div class="main">
        <!-- TOPBAR -->
        <div class="topbar">
            <div>
                <span class="topbar-title">Dashboard</span>
                <span class="topbar-sub">— Welcome {{$user->username}}</span>
            </div>
            <div class="spacer"></div>
            <div class="topbar-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                <div class="dot-notif"></div>
            </div>
        </div>

        <div class="content">
            <!-- STAT CARDS -->
            <div class="stat-grid">
                <div class="stat-card lime">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value">{{$TotalUsers}}</div>
                    @if($user->newUsersCount > 0)
                        <div class="stat-delta delta-up">↑ {{$user->newUsersCount}} this month</div>
                    @endif
                    <div class="stat-delta">{{$user->newUsersCount}} this month</div>
                    <div class="stat-icon"><svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div>
                </div>
                <div class="stat-card violet">
                    <div class="stat-label">Posts Today</div>
                    <div class="stat-value">{{$todayPosts}}</div>
                    <div class="stat-delta delta-up">↑ 8.1% vs yesterday</div>
                    <div class="stat-icon"><svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
                </div>
                <div class="stat-card red">
                    <div class="stat-label">Open Reports</div>
                    <div class="stat-value">{{$openReportsCount}}</div>
                    <div class="stat-delta delta-down">↑ 3 new since yesterday</div>
                    <div class="stat-icon"><svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
                </div>
                <div class="stat-card amber">
                    <div class="stat-label">Suspended Accounts</div>
                    <div class="stat-value">{{$suspendedUserCount}}</div>
                    <div class="stat-delta delta-down">↑ 22 this week</div>
                    <div class="stat-icon"><svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg></div>
                </div>
            </div>

            <!-- CHARTS + FLAGGED -->
            <div class="two-col">
                <!-- Activity Chart -->
                <div class="card">
                    <div class="card-head">
                        <span class="card-title">Daily Active Users</span>
                        <span class="card-action">View full report →</span>
                    </div>
                    <div class="chart-wrap">
                        <svg width="100%" height="160" viewBox="0 0 560 160" preserveAspectRatio="none">
                            <!-- Grid lines -->
                            <line x1="0" y1="40" x2="560" y2="40" stroke="#1e2024" stroke-width="1"/>
                            <line x1="0" y1="80" x2="560" y2="80" stroke="#1e2024" stroke-width="1"/>
                            <line x1="0" y1="120" x2="560" y2="120" stroke="#1e2024" stroke-width="1"/>
                            <!-- Area fill -->
                            <defs>
                                <linearGradient id="areaG" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#c8f04d" stop-opacity=".25"/>
                                    <stop offset="100%" stop-color="#c8f04d" stop-opacity="0"/>
                                </linearGradient>
                            </defs>
                            <path d="M0,120 C40,110 80,90 120,80 C160,70 200,100 240,75 C280,50 320,60 360,45 C400,30 440,55 480,40 C510,30 540,35 560,30 L560,160 L0,160 Z" fill="url(#areaG)"/>
                            <!-- Line -->
                            <path d="M0,120 C40,110 80,90 120,80 C160,70 200,100 240,75 C280,50 320,60 360,45 C400,30 440,55 480,40 C510,30 540,35 560,30" fill="none" stroke="#c8f04d" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <!-- Dot -->
                            <circle cx="560" cy="30" r="5" fill="#c8f04d"/>
                        </svg>
                        <div class="chart-labels">
                            <span>Jun 9</span><span>Jun 10</span><span>Jun 11</span><span>Jun 12</span>
                            <span>Jun 13</span><span>Jun 14</span><span>Jun 15</span><span>Jun 16</span>
                        </div>
                    </div>
                </div>

                <!-- Content Breakdown -->
                <div class="card">
                    <div class="card-head">
                        <span class="card-title">Content Breakdown</span>
                        <span class="card-action">Details →</span>
                    </div>
                    <div class="donut-wrap">
                        <svg class="donut-svg" width="110" height="110" viewBox="0 0 110 110">
                            <!-- Donut segments via stroke-dasharray trick -->
                            <circle cx="55" cy="55" r="40" fill="none" stroke="#1e2024" stroke-width="18"/>
                            <!-- Photos 52% = 251 of 502 -->
                            <circle cx="55" cy="55" r="40" fill="none" stroke="#c8f04d" stroke-width="18"
                                    stroke-dasharray="130 251" stroke-dashoffset="63" transform="rotate(-90 55 55)"/>
                            <!-- Videos 28% -->
                            <circle cx="55" cy="55" r="40" fill="none" stroke="#7c5cfc" stroke-width="18"
                                    stroke-dasharray="70 251" stroke-dashoffset="-67" transform="rotate(-90 55 55)"/>
                            <text x="55" y="52" text-anchor="middle" fill="#f0f0f0" font-size="13" font-weight="800" font-family="Inter,sans-serif">18.3k</text>
                            <text x="55" y="66" text-anchor="middle" fill="#5a5e6b" font-size="9" font-family="Inter,sans-serif">posts today</text>
                        </svg>
                        <div class="donut-legend">
                            <div class="legend-row"><div class="legend-dot" style="background:var(--accent)"></div><span class="legend-label">Photos</span><span class="legend-val">52%</span></div>
                            <div class="legend-row"><div class="legend-dot" style="background:var(--accent2)"></div><span class="legend-label">Videos</span><span class="legend-val">28%</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RECENT USERS + FLAGGED -->
            <div class="two-col">
                <!-- Recent Signups -->
                <div class="card">
                    <div class="card-head">
                        <span class="card-title">Recent Signups</span>
                        <a href="{{route('admin.users')}}" class="card-action" style="text-decoration: none">View all →</a>
                    </div>
                    <table>
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Joined</th>
                            <th>Posts</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($recentUsers as $recentUser)
                            <tr>
                                <td><div class="cell-user"> <img src="{{ asset('users/avatar/' . $recentUser->avatar) }}" class="sidebar-avatar" alt="{{$recentUser->username }} avatar"><div><div class="cell-name">
                                    {{$recentUser->username}}</div><div class="cell-handle">@ {{$recentUser->username}}</div></div></div></td>
                                <td>{{ $recentUser->created_at->format('M d, Y') }}</td><td>{{$recentUser->posts}}</td>
                                <td><span class="badge-pill pill-green">{{$recentUser->status}}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td></td>
                                <td></td>
                                <td>No Recent Users.</td>
                                <td></td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Flagged Content -->
                <div class="card">
                    <div class="card-head">
                        <span class="card-title">Flagged Content</span>
                        <a href="admin-reports.html" class="card-action">Review all →</a>
                    </div>
                    <div class="flagged-item">
                        <div class="flag-thumb ft1"></div>
                        <div class="flag-info">
                            <div class="flag-title">Inappropriate content</div>
                            <div class="flag-sub">@nxt.studio · Photo</div>
                        </div>
                        <div class="flag-count">8 reports</div>
                    </div>
                    <div class="flagged-item">
                        <div class="flag-thumb ft2"></div>
                        <div class="flag-info">
                            <div class="flag-title">Spam / misleading</div>
                            <div class="flag-sub">@user_4421 · Reel</div>
                        </div>
                        <div class="flag-count">5 reports</div>
                    </div>
                    <div class="flagged-item">
                        <div class="flag-thumb ft3"></div>
                        <div class="flag-info">
                            <div class="flag-title">Hate speech</div>
                            <div class="flag-sub">@anon_88x · Comment</div>
                        </div>
                        <div class="flag-count">4 reports</div>
                    </div>
                    <div class="flagged-item">
                        <div class="flag-thumb ft1"></div>
                        <div class="flag-info">
                            <div class="flag-title">Impersonation</div>
                            <div class="flag-sub">@alex_rivera2 · Profile</div>
                        </div>
                        <div class="flag-count">3 reports</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
