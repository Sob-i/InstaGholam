@extends('layouts.front.newPost.main')
@section('content')
    <div class="main">
        <div class="upload-container">
            <!-- LEFT: dropzone + thumbnail strip -->
            <div>
                <div class="section-title">New Post</div>
                <div class="section-sub">Share your moment with the world.</div>
                @if(session('success'))
                    <div class="alert alert-success">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <circle cx="8" cy="8" r="7" stroke="#c8f04d" stroke-width="1.5"/>
                            <path d="M5 8l2 2 4-4" stroke="#c8f04d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('fail'))
                    <div class="alert alert-error">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <circle cx="8" cy="8" r="7" stroke="#fc5c7d" stroke-width="1.5"/>
                            <path d="M8 5v4M8 11h.01" stroke="#fc5c7d" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <span>{{ session('fail') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <circle cx="8" cy="8" r="7" stroke="#fc5c7d" stroke-width="1.5"/>
                            <path d="M8 5v4M8 11h.01" stroke="#fc5c7d" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <ul style="margin:0; padding-left:20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" id="uploadForm" action="{{route('newPost.create')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="dropzone" id="dz">
                        <!-- Main preview (image) -->
                        <img class="main-preview" id="mainImg" src="" alt=""/>
                        <!-- Main preview (video) -->
                        <video class="main-preview" id="mainVid" autoplay muted loop playsinline></video>
                        <!-- Gradient overlay -->
                        <div class="main-preview-overlay" id="mainOverlay"></div>
                        <!-- File counter label -->
                        <div class="main-preview-label" id="mainLabel"></div>
                        <!-- Change button -->
                        <div class="change-hint" id="changeHint" onclick="document.getElementById('uploadFile').click()">Change</div>
                        <!-- Empty state -->
                        <div class="empty-state" id="emptyState" onclick="document.getElementById('uploadFile').click()">
                            <div class="dropzone-icon">
                                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                            </div>
                            <div class="dropzone-text">
                                <input type="file" name="uploadFile[]" id="uploadFile" style="display:none;" multiple accept="image/*,video/*">
                                Drop your photo or video here
                            </div>
                            <div class="dropzone-sub">or click to browse — JPG, PNG, WebP, MP4, MOV</div>
                        </div>
                    </div>

                    <!-- Thumbnail strip (hidden until files are selected) -->
                    <div class="thumb-strip" id="thumbStrip" style="display:none;"></div>
                    <!-- Size error toast -->
                    <div class="size-toast" id="sizeToast"></div>
            </div>

            <!-- RIGHT: form -->
            <div class="form-side">
                <div>
                    <label>Caption</label>
                    <textarea rows="4" name="caption" placeholder="Write a caption…" maxlength="2200" oninput="document.getElementById('cc').textContent=this.value.length+'/2200'"></textarea>
                    <div class="char-count" id="cc">0/2200</div>
                </div>
                <div>
                    <label>Tags</label>
                    <div class="tag-input-wrap">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        <input type="text" name="tags" placeholder="Add a tag…"/>
                    </div>
                    <div class="tag-pills">
                        <div class="tag-pill">Tags Will Help Your Post wives : #Travel , #Photo_Graphy</div>
                    </div>
                </div>
                <div>
                    <label>Location</label>
                    <div class="location-input">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <input type="text" name="location" placeholder="Add location…"/>
                    </div>
                </div>
                <div>
                    <label>Audience</label>
                    <select name="audience">
                        <option value="everyone">Everyone</option>
                        <option value="followers_only">Followers only</option>
                        <option value="close_friends">Close friends</option>
                    </select>
                </div>
                <div>
                    <div class="toggle-row">
                        <div><div class="toggle-label">Disable comments</div><div class="toggle-sub">No comments for this post</div></div>
                        <input type="hidden" name="disable_comments" value="open">
                        <div class="toggle" onclick="this.classList.toggle('on'); this.previousElementSibling.value = this.classList.contains('on') ? 'closed' : 'open'"></div>
                    </div>
                    <div class="toggle-row">
                        <div><div class="toggle-label">Hide like count</div><div class="toggle-sub">Others won't see how many likes</div></div>
                        <input type="hidden" name="hide_likes" value="visible">
                        <div class="toggle" onclick="this.classList.toggle('on'); this.previousElementSibling.value = this.classList.contains('on') ? 'notVisible' : 'visible'"></div>
                    </div>
                </div>
                <div class="btn-row">
                    <a href="{{route('newPost')}}"><button type="button" class="btn-secondary">Discard</button></a>
                    <div id="uploadContainer" style="display:none;">
                        <div class="progress-wrapper">
                            <div class="progress-bar" id="progressBar"></div>
                        </div>

                        <div class="progress-info">
                            <span id="progressPercent">0%</span>
                            <span id="progressStatus">Preparing upload...</span>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">Share Post</button>
                </div>
            </div>
            </form>
        </div>

    </div>


    <script>
        const MAX_MB = 100;
        const MAX_BYTES = MAX_MB * 1024 * 1024;

        const files = [];
        let activeIdx = 0;
        let toastTimer = null;

        const dt = new DataTransfer();

        const dz = document.getElementById('dz');
        const strip = document.getElementById('thumbStrip');
        const mainImg = document.getElementById('mainImg');
        const mainVid = document.getElementById('mainVid');
        const mainOverlay = document.getElementById('mainOverlay');
        const mainLabel = document.getElementById('mainLabel');
        const changeHint = document.getElementById('changeHint');
        const emptyState = document.getElementById('emptyState');
        const uploadFile = document.getElementById('uploadFile');
        const sizeToast = document.getElementById('sizeToast');

        function syncInputFiles() {
            const newDt = new DataTransfer();

            files.forEach(item => {
                newDt.items.add(item.file);
            });

            uploadFile.files = newDt.files;
        }

        function showToast(msg) {
            sizeToast.textContent = msg;
            sizeToast.classList.add('visible');

            clearTimeout(toastTimer);

            toastTimer = setTimeout(() => {
                sizeToast.classList.remove('visible');
            }, 3500);
        }

        function addFiles(rawFiles) {
            let skipped = 0;

            rawFiles.forEach(f => {
                if (f.size > MAX_BYTES) {
                    skipped++;
                    return;
                }

                files.push({
                    file: f,
                    url: URL.createObjectURL(f)
                });
            });

            syncInputFiles();

            if (skipped === 1) {
                showToast(`1 file exceeded ${MAX_MB} MB and was skipped.`);
            }

            if (skipped > 1) {
                showToast(`${skipped} files exceeded ${MAX_MB} MB and were skipped.`);
            }
        }

        uploadFile.addEventListener('change', e => {
            addFiles(Array.from(e.target.files));

            if (files.length) {
                renderStrip();
                setActive(activeIdx);
            }
        });

        dz.addEventListener('dragover', e => {
            e.preventDefault();
            dz.classList.add('drag');
        });

        dz.addEventListener('dragleave', () => {
            dz.classList.remove('drag');
        });

        dz.addEventListener('drop', e => {
            e.preventDefault();
            dz.classList.remove('drag');

            addFiles(
                Array.from(e.dataTransfer.files).filter(file =>
                    file.type.startsWith('image/') ||
                    file.type.startsWith('video/')
                )
            );

            if (files.length) {
                renderStrip();
                setActive(activeIdx);
            }
        });

        function isVideo(item) {
            return item.file.type.startsWith('video/');
        }

        function setActive(idx) {
            if (!files.length) return;

            activeIdx = idx;

            const item = files[idx];

            emptyState.style.display = 'none';

            mainOverlay.classList.add('visible');
            mainLabel.classList.add('visible');
            changeHint.classList.add('visible');

            if (isVideo(item)) {
                mainImg.classList.remove('visible');

                mainVid.src = item.url;
                mainVid.classList.add('visible');

                mainLabel.textContent =
                    item.file.name.split('.').pop().toUpperCase();
            } else {
                mainVid.classList.remove('visible');
                mainVid.src = '';

                mainImg.src = item.url;
                mainImg.classList.add('visible');

                mainLabel.textContent = `${idx + 1} / ${files.length}`;
            }

            document.querySelectorAll('.strip-thumb').forEach((el, i) => {
                el.classList.toggle('active', i === idx);
            });
        }

        function renderStrip() {
            strip.innerHTML = '';

            files.forEach((item, i) => {
                const thumb = document.createElement('div');

                thumb.className =
                    'strip-thumb' + (i === activeIdx ? ' active' : '');

                thumb.onclick = () => setActive(i);

                if (isVideo(item)) {
                    const video = document.createElement('video');

                    video.src = item.url;
                    video.muted = true;

                    video.style.cssText =
                        'width:100%;height:100%;object-fit:cover;border-radius:8px;';

                    thumb.appendChild(video);
                } else {
                    const img = document.createElement('img');

                    img.src = item.url;
                    img.alt = '';

                    thumb.appendChild(img);
                }

                const del = document.createElement('div');

                del.className = 'strip-del';
                del.innerHTML = '&times;';

                del.onclick = e => {
                    e.stopPropagation();
                    removeFile(i);
                };

                thumb.appendChild(del);

                strip.appendChild(thumb);
            });

            const addBtn = document.createElement('div');

            addBtn.className = 'strip-add';
            addBtn.innerHTML = '+';

            addBtn.onclick = () => {
                uploadFile.click();
            };

            strip.appendChild(addBtn);

            strip.style.display = files.length ? 'flex' : 'none';
        }

        function removeFile(idx) {
            URL.revokeObjectURL(files[idx].url);

            files.splice(idx, 1);

            syncInputFiles();

            if (!files.length) {
                strip.style.display = 'none';

                mainImg.classList.remove('visible');
                mainImg.src = '';

                mainVid.classList.remove('visible');
                mainVid.src = '';

                mainOverlay.classList.remove('visible');
                mainLabel.classList.remove('visible');
                changeHint.classList.remove('visible');

                emptyState.style.display = '';

                activeIdx = 0;

                return;
            }

            activeIdx = Math.min(activeIdx, files.length - 1);

            renderStrip();
            setActive(activeIdx);

        }
    </script>


@endsection
