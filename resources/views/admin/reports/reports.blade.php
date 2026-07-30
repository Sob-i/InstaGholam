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
                    @forelse($data['reports'] as $report)
                        @php
                            if (!empty($report->reportable->post_files)){
                                $firstFile = strtok($report->reportable->post_files, ',');
                                $fileExtension = strtolower(pathinfo($firstFile, PATHINFO_EXTENSION));
                                $isVideo = in_array($fileExtension, ['mp4', 'mov', 'avi', 'webm']);
                                $postPath = 'users/posts/' . strstr($report->reportedUser->email, '@', true) . '-posts/' . $report->reportable->created_at->format('Y-m-d') . '/' . $firstFile;
                            }
                        @endphp
                        <div class="report-item"  data-report-id="{{ $report->id }}"
                             data-count="{{ $count['count'] ?? 1 }}"
                             data-username="{{ $report->reportedUser->username }}"
                             data-posted="{{ $report->reportable->created_at->format('M d, Y h:i A') }}"
                             data-caption="{{ $report->reportable->post_caption }}"
                             data-reason="{{ $report->subject_label }}"
                             data-image="{{ asset($postPath) }}"
                             data-is-video="{{ $isVideo ? 1 : 0 }}">
                            @if($report->reportable)
                                <a href="{{route('post.show',$report->reportable->id)}}" class="ri-thumb t1">
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
                            @endif
                            <div class="ri-info">
                                <div class="ri-reason">{{$report->subject_label}}</div>
                                <div class="ri-target">Post by {{'@'.$report->reportedUser->username}}</div>
                                <div>
                                    @forelse($data['reportsCount'] as $count)
                                        @if($report->reportable->id == $count['item']['id'] )
                                            <span class="ri-count">⚑ {{$count['count']}} reports</span>
                                        @endif
                                    @empty
                                    @endforelse
                                    <span class="ri-time">{{$report->created_at->diffForHumans()}}</span>
                                </div>
                            </div>
                        </div>
                    @empty

                    @endforelse
                </div>
            </div>

            <!-- DETAIL + ACTIONS -->
            <div class="detail-panel">
                <!-- Report Detail -->
                <div class="detail-card">
                    <div class="detail-head">
                        <div class="detail-title" id="detailTitle"></div>
                        <span class="detail-badge" id="detailBadge"></span>
                    </div>
                    <div class="detail-body">
                        <div class="content-preview" id="detailPreview"></div>
                        <div class="detail-row">
                            <span class="detail-label">Posted by</span>
                            <span class="detail-val" id="detailUser"></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Posted at</span>
                            <span class="detail-val" id="detailPosted"></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Caption</span>
                            <span class="detail-val" id="detailCaption"></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Report reasons</span>
                            <span class="detail-val" id="detailReasons"></span>
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

    <script>
        function loadReport(item) {

            document.querySelectorAll(".report-item").forEach(report => {
                report.classList.remove("selected");
            });

            item.classList.add("selected");

            document.getElementById("detailTitle").textContent =
                `Report #${item.dataset.reportId}`;

            document.getElementById("detailBadge").textContent =
                `${item.dataset.count} reports`;

            document.getElementById("detailUser").innerHTML =
                `<strong>@${item.dataset.username}</strong>`;

            document.getElementById("detailPosted").textContent =
                item.dataset.posted;

            document.getElementById("detailCaption").textContent =
                item.dataset.caption;

            document.getElementById("detailReasons").textContent =
                item.dataset.reason;

            const preview = document.getElementById("detailPreview");

            if (item.dataset.isVideo === "1") {

                preview.innerHTML = `
            <div class="preview-video-wrapper">
                <video
                    id="detailVideo"
                    autoplay
                    muted
                    loop
                    playsinline
                    disablepictureinpicture
                    controlsList="nodownload nofullscreen noplaybackrate"
                >
                    <source src="${item.dataset.image}">
                </video>

                <button
                    id="muteToggle"
                    class="mute-btn">
                    🔇
                </button>
            </div>
        `;

                const video = document.getElementById("detailVideo");
                const muteBtn = document.getElementById("muteToggle");

                muteBtn.onclick = function () {

                    video.muted = !video.muted;

                    muteBtn.textContent = video.muted ? "🔇" : "🔊";

                };

            } else {

                preview.innerHTML = `
            <img
                src="${item.dataset.image}"
                style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
        `;

            }

        }
        document.addEventListener("click", function (e) {

            const item = e.target.closest(".report-item");

            if (!item) return;

            loadReport(item);

        });
        document.addEventListener("DOMContentLoaded", function () {

            const first = document.querySelector(".report-item");

            if (first) {
                loadReport(first);
            }

        });
        function renderReporters(reporters) {

            const list = document.getElementById("reportersList");

            list.innerHTML = "";

            reporters.forEach(report => {

                list.innerHTML += `
            <div class="reporter-row">
                <div class="rep-av"></div>

                <span class="rep-name">
                    @${report.username}
                </span>

                <span class="rep-reason">
                    ${report.reason}
                </span>

                <span class="rep-time">
                    ${report.time}
                </span>
            </div>
        `;

            });

        }
    </script>
@endsection
