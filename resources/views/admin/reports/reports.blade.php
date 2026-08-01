
@extends('layouts.admin.reports.main')
@section('content')

    <div class="main">
        <div class="topbar">
            <span class="topbar-title">Reports</span>
            <span class="urgent-badge">{{$data['reportsCount']->count()}} open</span>
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
                <div class="reports-container">
                    <!-- Reports will be rendered here by JavaScript -->
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
                            <div class="reporters-list" id="reportersList"></div>

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
        const data = @json([
        'reports' => $data['reports'],
        'reportsCount' => $data['reportsCount']
    ]);
    </script>
    <script>
        function renderReports(data) {
            const container = document.querySelector('.reports-container');
            if (!container) return;

            container.innerHTML = '';

            // Convert reportsCount to array if it's an object
            let reportsCountArray = [];
            if (data.reportsCount) {
                if (typeof data.reportsCount === 'object' && !Array.isArray(data.reportsCount)) {
                    reportsCountArray = Object.values(data.reportsCount);
                } else if (Array.isArray(data.reportsCount)) {
                    reportsCountArray = data.reportsCount;
                }
            }

            // Track rendered reportable IDs to prevent duplicates
            const renderedReportableIds = new Set();

            data.reports.forEach((report) => {
                if (!report.reportable || renderedReportableIds.has(report.reportable.id)) {
                    return;
                }
                renderedReportableIds.add(report.reportable.id);

                let postPath = '';
                let fileExtension = 'jpg';
                let isVideo = false;

                if (report.reportable.post_files) {
                    const firstFile = report.reportable.post_files.split(',')[0].trim();
                    fileExtension = firstFile.split('.').pop().toLowerCase();
                    isVideo = ['mp4', 'mov', 'avi', 'webm', 'mkv', 'ogg'].includes(fileExtension);

                    const email = report.reportedUser ? report.reportedUser.email :
                        (report.reported_user ? report.reported_user.email : '');
                    const username = email.split('@')[0];

                    // Get timestamp from filename
                    const timestamp = firstFile.replace(`${username}-`, '').replace(`.${fileExtension}`, '');

                    let postDate;
                    if (!isNaN(timestamp)) {
                        const ts = parseInt(timestamp);
                        let seconds;

                        if (timestamp.length === 13) {
                            seconds = Math.floor(ts / 1000);
                        } else if (timestamp.length === 12) {
                            seconds = Math.floor(ts / 1000);
                        } else if (timestamp.length === 10) {
                            seconds = ts;
                        } else {
                            seconds = Math.floor(ts / 1000);
                        }

                        postDate = new Date(seconds * 1000);

                        if (isNaN(postDate.getTime()) || postDate.getFullYear() < 2020) {
                            postDate = new Date(report.reportable.created_at);
                        }
                    } else {
                        postDate = new Date(report.reportable.created_at);
                    }

                    if (!postDate || isNaN(postDate.getTime())) {
                        postDate = new Date();
                    }

                    // Use UTC to avoid timezone issues
                    const year = postDate.getUTCFullYear();
                    const month = String(postDate.getUTCMonth() + 1).padStart(2, '0');
                    const day = String(postDate.getUTCDate()).padStart(2, '0');
                    const formattedDate = `${year}-${month}-${day}`;

                    postPath = `/users/posts/${username}-posts/${formattedDate}/${firstFile}`;
                }

                // Get count
                let count = 1;
                reportsCountArray.forEach((countItem) => {
                    if (countItem.item && countItem.item.id === report.reportable.id) {
                        count = countItem.count;
                    }
                });

                // Format posted date
                let postedDate = '';
                if (report.reportable.created_at) {
                    const date = new Date(report.reportable.created_at);
                    if (!isNaN(date.getTime())) {
                        postedDate = date.toLocaleString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                }

                const username = report.reportedUser ? report.reportedUser.username :
                    (report.reported_user ? report.reported_user.username : '');
                const subjectLabel = report.subject_label || report.report_subject || 'No reason provided';
                const caption = report.reportable.post_caption || '';
                const reportableId = report.reportable.id;
                const reportableType = report.reportable_type;

                let type = "user";

                if (report.reportable_type.includes("postModel")) {
                    type = "post";
                } else if (report.reportable_type.includes("commentModel")) {
                    type = "comment";
                } else if (report.reportable_type.includes("storyModel")) {
                    type = "story";
                } else if (report.reportable_type.includes("User")) {
                    type = "user";
                }

                const reportHtml = `
            <div class="report-item"
                data-type="${type}"
                data-report-id="${report.id}"
                 data-count="${count}"
                 data-username="${username}"
                 data-posted="${postedDate}"
                 data-caption="${caption}"
                 data-reason="${subjectLabel}"
                 data-image="${postPath}"
                 data-is-video="${isVideo ? 1 : 0}"
                data-reportable-id="${reportableId}"
                data-reportable-type="${reportableType}">
                ${report.reportable ? `
                    <a href="/post/${report.reportable.id}" class="ri-thumb t1">
                        ${isVideo ? `
                            <video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;"
                                   muted
                                   preload="metadata"
                                   playsinline
                                   disablepictureinpicture
                                   disableremoteplayback
                                   onerror="this.parentElement.innerHTML='<div style=\\'display:flex;align-items:center;justify-content:center;height:100%;background:#f0f0f0;color:#999;font-size:14px;\\'>Video unavailable</div>'">
                                <source src="${postPath}" type="video/${fileExtension}">
                            </video>
                        ` : `
                            <img src="${postPath}"
                                 alt="Post image"
                                 loading="lazy"
                                 style="object-fit: cover; object-position: center; width: 100%; height: 100%;"
                                 onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\\'display:flex;align-items:center;justify-content:center;height:100%;background:#f0f0f0;color:#999;font-size:14px;\\'>Image unavailable</div>'">
                        `}
                    </a>
                ` : ''}
                <div class="ri-info">
                    <div class="ri-reason">${subjectLabel}</div>
                    <div class="ri-target">Post by ${username ? '@' + username : ''}</div>
                    <div>
                        <span class="ri-count">⚑ ${count} report${count > 1 ? 's' : ''}</span>
                        <span class="ri-time">${report.created_at ? timeAgo(new Date(report.created_at)) : ''}</span>
                    </div>
                </div>
            </div>
        `;

                container.innerHTML += reportHtml;
            });
        }

        function timeAgo(date) {
            const diff = Math.floor((new Date() - date) / 1000);
            if (diff < 60) return diff + ' seconds ago';
            if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
            if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
            if (diff < 2592000) return Math.floor(diff / 86400) + ' days ago';
            if (diff < 31536000) return Math.floor(diff / 2592000) + ' months ago';
            return Math.floor(diff / 31536000) + ' years ago';
        }

        renderReports(data);

        document.addEventListener("click", function(e) {

            const tab = e.target.closest(".ftab");

            if (!tab) return;

            document.querySelectorAll(".ftab").forEach(btn => {
                btn.classList.remove("active");
            });

            tab.classList.add("active");


            const filter = tab.textContent.trim().toLowerCase();

            document.querySelectorAll(".report-item").forEach(item => {

                const type = item.dataset.type;

                if (filter === "all") {
                    item.style.display = "";
                }
                else if (type === filter.slice(0, -1)) {
                    // posts -> post
                    // users -> user
                    // comments -> comment
                    item.style.display = "";
                }
                else {
                    item.style.display = "none";
                }

            });

        });
    </script>
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
                `<a href="/profile/${item.dataset.username}">@${item.dataset.username}</a>`;

            document.getElementById("detailPosted").textContent =
                item.dataset.posted;

            document.getElementById("detailCaption").textContent =
                item.dataset.caption;

            const reports = data.reports.filter(report =>
                report.reportable_id == item.dataset.reportableId &&
                report.reportable_type == item.dataset.reportableType
            );

            const labels = {
                spam: "Spam",
                harassment: "Harassment",
                hate_speech: "Hate Speech",
                violence: "Violence",
                nudity: "Nudity",
                false_information: "False Information",
                other: "Other"
            };

            const reasons = [...new Set(
                reports.map(r => labels[r.report_subject] || r.report_subject)
            )].join(", ");

            document.getElementById("detailReasons").textContent = reasons;
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
        function loadReporters(item) {

            const reportersList = document.getElementById("reportersList");

            reportersList.innerHTML = "";

            const reportId = Number(item.dataset.reportId);

            const reports = data.reports.filter(report =>
                report.reportable_id == item.dataset.reportableId &&
                report.reportable_type == item.dataset.reportableType
            );


            reports.forEach(report => {
                const time = new Date(report.created_at).toLocaleString();
                reportersList.innerHTML += `
            <div class="reporter-row">
                <div class="rep-av"></div>
                <img src="/users/avatar/${report.reporter.avatar}" class="sidebar-avatar" style="margin-left: -4%;">
                <span class="rep-name">${report.reporter.username}</span>
                <span class="rep-reason">${report.report_subject}</span>
                <span class="rep-time">${time}</span>
            </div>
        `;

            });

        }
        document.addEventListener("click", function (e) {

            const item = e.target.closest(".report-item");

            if (!item) return;

            loadReporters(item);

        });document.addEventListener("DOMContentLoaded", function () {

            const first = document.querySelector(".report-item");

            if (first) {
                loadReporters(first);
            }

        });
    </script>
@endsection
