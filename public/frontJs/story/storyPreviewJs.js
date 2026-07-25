// Toast notification function
function showToast(message, type = 'success') {
    // Remove existing toast if any
    const existingToast = document.querySelector('.toast-message');
    if (existingToast) {
        existingToast.remove();
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-message toast-${type}`;

    // Create toast content with icon
    const icons = {
        success: `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`,
        error: `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`,
        warning: `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
        info: `<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`
    };

    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="display: flex; align-items: center;">${icons[type] || icons.success}</span>
            <span>${message}</span>
        </div>
    `;

    // Style the toast
    const toastStyles = {
        'position': 'fixed',
        'top': '24px',
        'right': '24px',
        'padding': '14px 20px',
        'border-radius': '12px',
        'color': 'white',
        'font-family': '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
        'font-size': '14px',
        'font-weight': '500',
        'z-index': '10000',
        'opacity': '0',
        'transform': 'translateX(100px)',
        'transition': 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
        'max-width': '420px',
        'box-shadow': '0 8px 32px rgba(0, 0, 0, 0.2)',
        'backdrop-filter': 'blur(20px)',
        'border': '1px solid rgba(255, 255, 255, 0.2)',
        'pointer-events': 'auto'
    };

    // Set background color based on type
    switch(type) {
        case 'success':
            toast.style.background = 'linear-gradient(135deg, rgba(72, 187, 120, 0.95), rgba(56, 161, 105, 0.95))';
            break;
        case 'error':
            toast.style.background = 'linear-gradient(135deg, rgba(245, 101, 101, 0.95), rgba(229, 62, 62, 0.95))';
            break;
        case 'warning':
            toast.style.background = 'linear-gradient(135deg, rgba(237, 137, 54, 0.95), rgba(221, 107, 32, 0.95))';
            break;
        case 'info':
            toast.style.background = 'linear-gradient(135deg, rgba(66, 153, 225, 0.95), rgba(49, 130, 206, 0.95))';
            break;
        default:
            toast.style.background = 'linear-gradient(135deg, rgba(72, 187, 120, 0.95), rgba(56, 161, 105, 0.95))';
    }

    // Apply styles
    Object.assign(toast.style, toastStyles);

    // Add to body
    document.body.appendChild(toast);

    // Trigger animation
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    });

    // Add close button functionality
    toast.addEventListener('click', () => {
        removeToast(toast);
    });

    // Auto remove after 4 seconds
    const timeout = setTimeout(() => {
        removeToast(toast);
    }, 4000);

    // Store timeout on element for cleanup
    toast.dataset.timeout = timeout;
}

function removeToast(toast) {
    clearTimeout(Number(toast.dataset.timeout));
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100px)';
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 300);
}

// Minimal version - just the core selection logic
document.querySelectorAll('.privacy-opt').forEach(option => {
    option.addEventListener('click', function() {
        // Remove selected state from all options
        document.querySelectorAll('.privacy-opt').forEach(opt => {
            opt.classList.remove('selected');
        });

        // Add selected state to clicked option
        this.classList.add('selected');

        // Check the radio button inside this option
        const radio = this.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
        }

        // Get the selected value from the radio button
        const selectedPrivacy = radio ? radio.value : this.querySelector('.privacy-name').textContent;
        console.log('Selected:', selectedPrivacy);
    });
});

// Story Canvas - Photo/Video Selector with Zoom, Audio & Color Toggle
document.addEventListener('DOMContentLoaded', function() {
    const storyCanvas = document.querySelector('.story-canvas-content');
    const canvasContainer = storyCanvas.parentElement;
    const progressFill = document.querySelector('.story-progress-fill');

    // Create file input element - EXCLUDE GIFs
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/png,image/jpeg,image/webp,image/bmp,video/*'; // No image/gif
    fileInput.style.display = 'none';
    fileInput.id = 'story-media-input';
    document.body.appendChild(fileInput);

    // Add CSS for controls and loading
    const style = document.createElement('style');
    style.textContent = `
            .zoom-controls {
                display: flex;
                gap: 12px;
                align-items: center;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                padding: 8px 16px;
                border-radius: 20px;
            }

            .zoom-control, .filter-control, .audio-control {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                border: none;
                background: transparent;
                color: white;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
                transition: all 0.2s;
                font-size: 18px;
                line-height: 1;
            }

            .zoom-out {
                font-size: 20px;
            }

            .zoom-in {
                font-size: 20px;
            }

            .zoom-reset {
                font-size: 16px;
                margin-left: 8px;
            }

            .zoom-level {
                color: white;
                font-size: 14px;
                min-width: 45px;
                text-align: center;
                font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            }

            .filter-control {
                background: rgba(255, 255, 255, 0.3);
                backdrop-filter: blur(10px);
            }

            .audio-control {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
            }

            .loading-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                z-index: 20;
                gap: 16px;
            }

            .loading-spinner {
                width: 40px;
                height: 40px;
                border: 3px solid rgba(255, 255, 255, 0.3);
                border-top: 3px solid #fff;
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            .loading-text {
                color: white;
                font-size: 14px;
                font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            }

            .drag-over {
                border: 2px dashed #4a9eff !important;
                background: rgba(74, 158, 255, 0.1) !important;
            }
        `;
    document.head.appendChild(style);

    // Zoom state
    let currentScale = 1;
    let isDragging = false;
    let startX, startY, translateX = 0, translateY = 0;
    let lastTranslateX = 0, lastTranslateY = 0;
    let initialDistance = 0;
    let initialScale = 1;
    let isBlackAndWhite = false;
    let progressInterval;

    // Handle click on the canvas placeholder
    storyCanvas.addEventListener('click', function(e) {
        if (e.target.classList.contains('zoom-control') ||
            e.target.classList.contains('filter-control') ||
            e.target.classList.contains('audio-control')) return;
        e.stopPropagation();
        fileInput.click();
    });

    // Handle file selection from input
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        processFile(file);
    });

    // Process the selected file
    function processFile(file) {
        // Check if file is a GIF and reject it
        if (file.type === 'image/gif') {
            showToast('GIF files are not supported. Please select a photo or video.', 'warning');
            fileInput.value = ''; // Reset the file input
            return;
        }

        // Show loading overlay
        showLoading();

        if (file.type.startsWith('image/')) {
            handleImageSelection(file);
        } else if (file.type.startsWith('video/')) {
            handleVideoSelection(file);
        } else {
            hideLoading();
            showToast('Please select an image or video file.', 'warning');
        }
    }

    // Show loading overlay
    function showLoading() {
        // Remove existing loading if any
        hideLoading();

        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = `
                <div class="loading-spinner"></div>
                <div class="loading-text">Processing media...</div>
            `;
        storyCanvas.appendChild(loadingOverlay);
    }

    // Hide loading overlay
    function hideLoading() {
        const existingLoader = storyCanvas.querySelector('.loading-overlay');
        if (existingLoader) {
            existingLoader.remove();
        }
    }

    // Handle image selection
    function handleImageSelection(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            hideLoading();
            displayMedia('image', e.target.result, file.name);
        };
        reader.onerror = function() {
            hideLoading();
            showToast('Error loading image. Please try again.', 'error');
        };
        reader.readAsDataURL(file);
    }

    // Handle video selection
    function handleVideoSelection(file) {
        const maxSize = 100 * 1024 * 1024; // 100MB
        if (file.size > maxSize) {
            hideLoading();
            showToast('Video file is too large. Please select a video under 100MB.', 'warning');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            hideLoading();
            displayMedia('video', e.target.result, file.name);
        };
        reader.onerror = function() {
            hideLoading();
            showToast('Error loading video. Please try again.', 'error');
        };
        reader.readAsDataURL(file);
    }

    // Display media in canvas
    function displayMedia(type, src, fileName) {
        // Clear any existing progress interval
        if (progressInterval) {
            clearInterval(progressInterval);
        }

        // Reset progress bar
        progressFill.style.width = '0%';

        storyCanvas.innerHTML = '';

        // Create wrapper for zoom functionality
        const mediaWrapper = document.createElement('div');
        mediaWrapper.className = 'media-wrapper';
        mediaWrapper.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #000;
            `;

        let mediaElement;
        let isMuted = false;

        if (type === 'image') {
            mediaElement = document.createElement('img');
            mediaElement.src = src;
            mediaElement.alt = 'Selected story image';
            mediaElement.draggable = false;
            // Set progress to full for images
            progressFill.style.width = '100%';
        } else if (type === 'video') {
            mediaElement = document.createElement('video');
            mediaElement.src = src;
            mediaElement.muted = false; // Audio is ON by default
            mediaElement.playsInline = true;
            mediaElement.autoplay = true;
            mediaElement.loop = true;
            mediaElement.setAttribute('playsinline', '');
            mediaElement.setAttribute('webkit-playsinline', '');

            // Update progress bar based on video time
            mediaElement.addEventListener('loadedmetadata', function() {
                updateVideoProgress();
            });

            mediaElement.addEventListener('timeupdate', function() {
                updateVideoProgress();
            });

            function updateVideoProgress() {
                if (mediaElement.duration) {
                    const progress = (mediaElement.currentTime / mediaElement.duration) * 100;
                    progressFill.style.width = progress + '%';
                }
            }
        }

        mediaElement.className = 'zoomable-media';
        mediaElement.style.cssText = `
                width: 100%;
                height: 100%;
                object-fit: contain;
                transition: transform 0.1s ease, filter 0.3s ease;
                transform-origin: center center;
                cursor: grab;
                user-select: none;
                -webkit-user-select: none;
                pointer-events: auto;
                filter: none;
            `;

        mediaWrapper.appendChild(mediaElement);

        // Create controls container
        const controlsContainer = document.createElement('div');
        controlsContainer.className = 'media-controls';
        controlsContainer.style.cssText = `
                position: absolute;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                gap: 16px;
                align-items: center;
                z-index: 10;
            `;

        // Create zoom controls
        const zoomControls = document.createElement('div');
        zoomControls.className = 'zoom-controls';

        // Zoom out button
        const zoomOutBtn = document.createElement('button');
        zoomOutBtn.innerHTML = '−';
        zoomOutBtn.className = 'zoom-control zoom-out';

        // Zoom level indicator
        const zoomLevel = document.createElement('span');
        zoomLevel.className = 'zoom-level';
        zoomLevel.textContent = '100%';

        // Zoom in button
        const zoomInBtn = document.createElement('button');
        zoomInBtn.innerHTML = '+';
        zoomInBtn.className = 'zoom-control zoom-in';

        // Reset zoom button
        const resetBtn = document.createElement('button');
        resetBtn.innerHTML = '↺';
        resetBtn.className = 'zoom-control zoom-reset';

        zoomControls.appendChild(zoomOutBtn);
        zoomControls.appendChild(zoomLevel);
        zoomControls.appendChild(zoomInBtn);
        zoomControls.appendChild(resetBtn);

        // Create filter toggle button
        const filterToggle = document.createElement('button');
        filterToggle.className = 'filter-control';
        filterToggle.innerHTML = '◑';
        filterToggle.title = 'Toggle black & white';

        // Filter toggle functionality
        filterToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            isBlackAndWhite = !isBlackAndWhite;

            if (isBlackAndWhite) {
                mediaElement.style.filter = 'grayscale(100%) contrast(110%)';
                filterToggle.innerHTML = '◐';
                filterToggle.style.background = 'rgba(0, 0, 0, 0.5)';
            } else {
                mediaElement.style.filter = 'none';
                filterToggle.innerHTML = '◑';
                filterToggle.style.background = 'rgba(255, 255, 255, 0.3)';
            }
        });

        filterToggle.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });

        filterToggle.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });

        controlsContainer.appendChild(zoomControls);
        controlsContainer.appendChild(filterToggle);

        // Add audio control for videos only
        if (type === 'video') {
            // Create audio toggle button
            const audioToggle = document.createElement('button');
            audioToggle.className = 'audio-control';
            audioToggle.innerHTML = '🔊'; // Sound on icon
            audioToggle.title = 'Toggle audio';

            // Audio toggle functionality
            audioToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                isMuted = !isMuted;

                if (isMuted) {
                    mediaElement.muted = true;
                    audioToggle.innerHTML = '🔇';
                    audioToggle.style.background = 'rgba(255, 0, 0, 0.3)';
                } else {
                    mediaElement.muted = false;
                    audioToggle.innerHTML = '🔊';
                    audioToggle.style.background = 'rgba(255, 255, 255, 0.1)';
                }
            });

            audioToggle.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
            });

            audioToggle.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });

            controlsContainer.appendChild(audioToggle);
        }

        storyCanvas.appendChild(mediaWrapper);
        storyCanvas.appendChild(controlsContainer);

        // Reset zoom state
        resetZoom();

        // Zoom functionality
        function updateZoom(newScale, animate = true) {
            currentScale = Math.min(Math.max(newScale, 0.5), 3);

            if (!animate) {
                mediaElement.style.transition = 'none';
            } else {
                mediaElement.style.transition = 'transform 0.1s ease';
            }

            mediaElement.style.transform = `scale(${currentScale}) translate(${translateX}px, ${translateY}px)`;
            zoomLevel.textContent = Math.round(currentScale * 100) + '%';

            zoomOutBtn.style.opacity = currentScale <= 0.5 ? '0.3' : '1';
            zoomInBtn.style.opacity = currentScale >= 3 ? '0.3' : '1';

            if (!animate) {
                setTimeout(() => {
                    mediaElement.style.transition = 'transform 0.1s ease';
                }, 50);
            }
        }

        function resetZoom() {
            currentScale = 1;
            translateX = 0;
            translateY = 0;
            lastTranslateX = 0;
            lastTranslateY = 0;
            mediaElement.style.transform = 'scale(1) translate(0px, 0px)';
            mediaElement.style.cursor = 'grab';
            zoomLevel.textContent = '100%';
            zoomOutBtn.style.opacity = '1';
            zoomInBtn.style.opacity = '1';
        }

        // Zoom button handlers
        zoomInBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            updateZoom(currentScale + 0.25);
        });

        zoomOutBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            updateZoom(currentScale - 0.25);
        });

        resetBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            resetZoom();
        });

        // Mouse wheel zoom
        mediaWrapper.addEventListener('wheel', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            updateZoom(currentScale + delta);
        }, { passive: false });

        // Mouse drag to pan when zoomed
        mediaElement.addEventListener('mousedown', function(e) {
            if (currentScale > 1) {
                isDragging = true;
                startX = e.clientX - translateX;
                startY = e.clientY - translateY;
                mediaElement.style.cursor = 'grabbing';
                e.preventDefault();
            }
        });

        window.addEventListener('mousemove', function(e) {
            if (isDragging) {
                translateX = e.clientX - startX;
                translateY = e.clientY - startY;
                updateZoom(currentScale, false);
            }
        });

        window.addEventListener('mouseup', function() {
            if (isDragging) {
                isDragging = false;
                mediaElement.style.cursor = currentScale > 1 ? 'grab' : 'default';
                lastTranslateX = translateX;
                lastTranslateY = translateY;
            }
        });

        // Touch events for pinch zoom
        mediaWrapper.addEventListener('touchstart', function(e) {
            if (e.touches.length === 2) {
                initialDistance = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
                initialScale = currentScale;
                e.preventDefault();
            } else if (e.touches.length === 1 && currentScale > 1) {
                isDragging = true;
                startX = e.touches[0].clientX - translateX;
                startY = e.touches[0].clientY - translateY;
            }
        });

        mediaWrapper.addEventListener('touchmove', function(e) {
            if (e.touches.length === 2) {
                const currentDistance = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
                const scale = initialScale * (currentDistance / initialDistance);
                updateZoom(scale, false);
                e.preventDefault();
            } else if (e.touches.length === 1 && isDragging) {
                translateX = e.touches[0].clientX - startX;
                translateY = e.touches[0].clientY - startY;
                updateZoom(currentScale, false);
                e.preventDefault();
            }
        }, { passive: false });

        mediaWrapper.addEventListener('touchend', function() {
            isDragging = false;
            initialDistance = 0;
            lastTranslateX = translateX;
            lastTranslateY = translateY;
        });

        // Double click to reset zoom
        mediaElement.addEventListener('dblclick', function(e) {
            e.preventDefault();
            e.stopPropagation();
            resetZoom();
        });

        storyCanvas.dataset.mediaType = type;
        storyCanvas.dataset.fileName = fileName;

        showToast('Media loaded successfully', 'success');
    }

    // Remove media and restore placeholder
    function removeMedia() {
        // Clear progress interval
        if (progressInterval) {
            clearInterval(progressInterval);
        }

        // Reset progress bar
        progressFill.style.width = '0%';

        hideLoading();

        storyCanvas.innerHTML = `
                <div class="canvas-placeholder-icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                </div>
                <div class="canvas-placeholder-text">Tap to add a photo or video</div>
                <div class="canvas-placeholder-sub">Disappears after 24 hours</div>
            `;

        delete storyCanvas.dataset.mediaType;
        delete storyCanvas.dataset.fileName;
        fileInput.value = '';
    }

    // Drag and drop handlers
    document.addEventListener('dragover', function(e) {
        e.preventDefault();
    });

    document.addEventListener('drop', function(e) {
        e.preventDefault();
    });

    if (canvasContainer) {
        canvasContainer.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('drag-over');
        });

        canvasContainer.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('drag-over');
        });

        canvasContainer.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('drag-over');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                // Check if dropped file is a GIF and reject it
                if (file.type === 'image/gif') {
                    showToast('GIF files are not supported. Please use a photo or video.', 'warning');
                    return;
                }
                if (file.type.startsWith('image/') || file.type.startsWith('video/')) {
                    processFile(file);
                } else {
                    showToast('Please drop an image or video file.', 'warning');
                }
            }
        });
    }

    // GET EDITED IMAGE - Captures canvas with current zoom, pan, and filter
    function getEditedImage() {
        const mediaData = window.storyCanvas.getMediaData();
        if (!mediaData || mediaData.type !== 'image') {
            throw new Error('No image loaded');
        }

        const img = mediaData.element;
        const wrapper = img.parentElement;

        // Create canvas at the exact visible size
        const canvas = document.createElement('canvas');
        canvas.width = wrapper.clientWidth;
        canvas.height = wrapper.clientHeight;
        const ctx = canvas.getContext('2d');

        // Black background (matching .media-wrapper)
        ctx.fillStyle = '#000';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        ctx.save();

        // Move origin to center of canvas
        ctx.translate(canvas.width / 2, canvas.height / 2);

        // Apply the current zoom and pan
        ctx.scale(currentScale, currentScale);
        ctx.translate(translateX, translateY);

        // Apply filter if active
        if (isBlackAndWhite) {
            ctx.filter = 'grayscale(100%) contrast(110%)';
        }

        // Calculate fit dimensions (matching object-fit: contain)
        const imgW = img.naturalWidth;
        const imgH = img.naturalHeight;
        const scaleX = canvas.width / imgW;
        const scaleY = canvas.height / imgH;
        const fitScale = Math.min(scaleX, scaleY);
        const drawW = imgW * fitScale;
        const drawH = imgH * fitScale;

        // Draw image centered
        ctx.drawImage(img, -drawW/2, -drawH/2, drawW, drawH);

        ctx.restore();

        // Return as data URL (PNG)
        return canvas.toDataURL('image/png');
    }

    // GET EDITED VIDEO - Records video with current zoom, pan, and filter
    async function getEditedVideo() {
        const mediaData = window.storyCanvas.getMediaData();
        if (!mediaData || mediaData.type !== 'video') {
            throw new Error('No video loaded');
        }

        const video = mediaData.element;
        const wrapper = video.parentElement;

        // Create canvas at the exact visible size
        const canvas = document.createElement('canvas');
        canvas.width = wrapper.clientWidth;
        canvas.height = wrapper.clientHeight;
        const ctx = canvas.getContext('2d');

        // Capture audio from original video if unmuted
        let audioTrack = null;
        if (!video.muted && video.captureStream) {
            const originalStream = video.captureStream();
            const audioTracks = originalStream.getAudioTracks();
            if (audioTracks.length > 0) audioTrack = audioTracks[0];
        }

        // Create stream from canvas
        const canvasStream = canvas.captureStream(30); // 30 fps

        // Add audio if available
        if (audioTrack) {
            canvasStream.addTrack(audioTrack);
        }

        // Record the stream
        const chunks = [];
        const recorder = new MediaRecorder(canvasStream, { mimeType: 'video/webm' });

        recorder.ondataavailable = e => chunks.push(e.data);

        // Draw function for each frame
        const draw = () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Black background
            ctx.fillStyle = '#000';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.save();

            // Move origin to center
            ctx.translate(canvas.width / 2, canvas.height / 2);

            // Apply zoom and pan
            ctx.scale(currentScale, currentScale);
            ctx.translate(translateX, translateY);

            // Apply filter if active
            if (isBlackAndWhite) {
                ctx.filter = 'grayscale(100%) contrast(110%)';
            }

            // Calculate fit dimensions
            const vidW = video.videoWidth;
            const vidH = video.videoHeight;
            if (vidW && vidH) {
                const scaleX = canvas.width / vidW;
                const scaleY = canvas.height / vidH;
                const fitScale = Math.min(scaleX, scaleY);
                const drawW = vidW * fitScale;
                const drawH = vidH * fitScale;

                // Draw video centered
                ctx.drawImage(video, -drawW/2, -drawH/2, drawW, drawH);
            }

            ctx.restore();
        };

        // RESET VIDEO TO BEGINNING before recording
        video.currentTime = 0;
        video.pause();

        // Wait for video to seek to beginning
        await new Promise(resolve => {
            const onSeeked = () => {
                video.removeEventListener('seeked', onSeeked);
                resolve();
            };
            video.addEventListener('seeked', onSeeked);

            // Timeout fallback if seeked doesn't fire
            setTimeout(() => {
                video.removeEventListener('seeked', onSeeked);
                resolve();
            }, 500);
        });

        // Start recording
        recorder.start();

        // Play video from beginning
        video.play();

        // Animation loop
        const drawLoop = () => {
            draw();
            if (recorder.state === 'recording') {
                requestAnimationFrame(drawLoop);
            }
        };
        drawLoop();

        // Stop when video ends
        const duration = video.duration * 1000 || 5000;
        setTimeout(() => {
            if (recorder.state === 'recording') recorder.stop();
        }, duration);

        // Return blob when done
        return new Promise(resolve => {
            recorder.onstop = () => {
                resolve(new Blob(chunks, { type: 'video/webm' }));
            };
        });
    }

    // Upload to backend function
    async function uploadToServer(blob, mimeType, fileName) {
        const formData = new FormData();
        formData.append('media', blob, fileName);
        formData.append('type', mimeType.startsWith('image') ? 'image' : 'video');
        formData.append('mime_type', mimeType);

        // Add privacy setting - now using radio button value
        const selectedRadio = document.querySelector('input[name="privacy"]:checked');
        const selectedPrivacy = selectedRadio ? selectedRadio.value : 'followers';
        formData.append('privacy', selectedPrivacy);

        // Add caption if any
        const captionInput = document.querySelector('.bottombar-input');
        const caption = captionInput ? captionInput.value : '';
        if (caption) {
            formData.append('caption', caption);
        }

        // Get CSRF token if using Laravel
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const token = csrfToken ? csrfToken.getAttribute('content') : '';

        // Log what we're sending for debugging
        console.log('Uploading:', {
            fileName: fileName,
            type: mimeType.startsWith('image') ? 'image' : 'video',
            mimeType: mimeType,
            privacy: selectedPrivacy,
            caption: caption,
            blobSize: blob.size
        });

        const response = await fetch('/newStory/add', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        });

        // Get response data
        const responseData = await response.json();
        console.log('Server response:', responseData);

        // Show toast with message from backend
        if (response.ok && responseData.success) {
            showToast(responseData.message || 'Story uploaded successfully!', 'success');
        } else {
            showToast(responseData.message || 'Upload failed. Please try again.', 'error');
        }

        if (!response.ok) {
            throw new Error(responseData.message || 'Upload failed');
        }

        return responseData;
    }

    // Expose methods
    window.storyCanvas = {
        getMediaData: function() {
            const hasMedia = storyCanvas.dataset.mediaType;
            if (!hasMedia) return null;
            return {
                type: storyCanvas.dataset.mediaType,
                fileName: storyCanvas.dataset.fileName,
                element: storyCanvas.querySelector('.zoomable-media')
            };
        },
        clearMedia: removeMedia,
        getFileInput: function() {
            return fileInput;
        },
        getEditedImage: getEditedImage,
        getEditedVideo: getEditedVideo,
        uploadToServer: uploadToServer,
        showToast: showToast
    };

    // Share button functionality
    const shareButton = document.querySelector('.btn-primary');
    if (shareButton) {
        shareButton.addEventListener('click', async function() {
            try {
                const mediaData = window.storyCanvas.getMediaData();

                if (!mediaData) {
                    showToast('Please add a photo or video first', 'warning');
                    return;
                }

                // Disable button and show loading state
                this.disabled = true;
                const originalText = this.textContent;
                this.textContent = 'Sharing...';

                // Show loading while processing
                showLoading();

                let result;
                if (mediaData.type === 'image') {
                    // Get the edited image
                    const dataUrl = window.storyCanvas.getEditedImage();

                    // Convert data URL to blob for upload
                    const response = await fetch(dataUrl);
                    const blob = await response.blob();

                    // Upload to backend (toast is shown inside uploadToServer)
                    result = await uploadToServer(blob, 'image/png', mediaData.fileName);

                } else if (mediaData.type === 'video') {
                    // Get the edited video blob
                    const blob = await window.storyCanvas.getEditedVideo();

                    // Upload to backend (toast is shown inside uploadToServer)
                    result = await uploadToServer(blob, 'video/webm', mediaData.fileName);
                }

                hideLoading();

                // If successful, clear the canvas
                if (result && result.success) {
                    window.storyCanvas.clearMedia();
                    // Reset button
                    this.textContent = originalText;
                    this.disabled = false;
                } else {
                    // Reset button on failure
                    this.textContent = originalText;
                    this.disabled = false;
                }

            } catch (err) {
                hideLoading();
                showToast(err.message || 'An error occurred while sharing', 'error');
                console.error('Share error:', err);

                // Reset button
                if (shareButton) {
                    shareButton.disabled = false;
                    shareButton.textContent = 'Share';
                }
            }
        });
    }
});
