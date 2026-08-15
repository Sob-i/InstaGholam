
    document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const storyModal = document.getElementById('highlightStoryModal');
    const closeStoryModal = document.getElementById('closeHighlightStory');
    const storyContent = document.getElementById('highlightStoryContent');
    const storyProgressContainer = document.getElementById('highlightStoryProgressContainer');
    const storyUserAvatar = document.getElementById('highlightStoryAvatar');
    const storyTitle = document.getElementById('highlightStoryTitle');
    const storyCount = document.getElementById('highlightStoryCount');
    const storyPrev = document.getElementById('highlightStoryPrev');
    const storyNext = document.getElementById('highlightStoryNext');

    let currentStories = [];
    let currentStoryIndex = 0;
    let progressInterval;
    let isPaused = false;
    let videoEnded = false;
    let currentVideo = null;
    let highlightTitle = '';
    let highlightCover = '';

    // Click handler for highlight items
    document.querySelectorAll('.highlight:not(.add-highlight)').forEach((highlightItem) => {
    highlightItem.addEventListener('click', function() {
    const highlightCover = this.dataset.highlight;
    const username = this.dataset.username;

    if (highlightCover && username) {
    openHighlightStories(username, highlightCover);
}
});
});

    // Close handlers
    closeStoryModal.addEventListener('click', closeStoryViewer);
    storyModal.addEventListener('click', function(e) {
    if (e.target === storyModal) closeStoryViewer();
});

    // Navigation
    storyPrev.addEventListener('click', (e) => {
    e.stopPropagation();
    navigateStory('prev');
});

    storyNext.addEventListener('click', (e) => {
    e.stopPropagation();
    navigateStory('next');
});

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
    if (storyModal.style.display === 'none') return;
    if (e.key === 'ArrowLeft') navigateStory('prev');
    if (e.key === 'ArrowRight') navigateStory('next');
    if (e.key === 'Escape') closeStoryViewer();
});

    function openHighlightStories(username, highlightCover) {

    // Show loading state
    storyContent.innerHTML = '<div class="highlight-story-loading">Loading stories...</div>';
    storyModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Fetch highlight stories with GET request
    fetch(`/profile/${username}/${highlightCover}/show`, {
    method: 'GET',
    headers: {
    'Accept': 'application/json'
}
})
    .then(response => response.json())
    .then(data => {
    if (data.status && data.data && data.data.length > 0) {
    // Combine stories from all highlight records
    let allStories = [];
    let title = '';
    let cover = '';

    data.data.forEach((highlight, index) => {
    if (highlight.stories && highlight.stories.length > 0) {
    highlight.stories.forEach(story => {
    allStories.push(story);
});
}
    if (!title) title = highlight.title || 'Highlight';
    if (!cover) cover = highlight.cover || '';
});

    currentStories = allStories;
    currentStoryIndex = 0;
    highlightTitle = title;
    highlightCover = cover;
    videoEnded = false;
    currentVideo = null;


    if (currentStories.length === 0) {
    storyContent.innerHTML = '<div class="highlight-story-loading">No stories in this highlight</div>';
    return;
}

    // Set highlight cover as avatar
    const username = currentStories[0]?.user?.email?.split('@')[0] || '';
    storyUserAvatar.src = `/users/highlights/${username}/${highlightCover}`;
    storyTitle.textContent = highlightTitle;

    updateStoryUI();
    createProgressBars();
    setTimeout(() => startProgress(), 100);
} else {
    storyContent.innerHTML = '<div class="highlight-story-loading">No stories available</div>';
}
})
    .catch(error => {
    console.error('Error:', error);
    storyContent.innerHTML = '<div class="highlight-story-loading">Error loading stories</div>';
});
}

    function updateStoryUI() {
    // Clean up previous video
    if (currentVideo) {
    currentVideo.pause();
    currentVideo.removeAttribute('src');
    currentVideo.load();
    currentVideo = null;
}

    clearInterval(progressInterval);
    videoEnded = false;

    const story = currentStories[currentStoryIndex];
    if (!story) return;

    // Get email_prefix from the user relationship
    const emailPrefix = story.user?.email ? story.user.email.split('@')[0] : '';
    const mediaUrl = `/users/stories/${story.media_type}/${emailPrefix}/${story.media}`;

    const isVideo = story.media_type === 'video' || mediaUrl.match(/\.(mp4|mov|avi|webm)$/i);
    const isGif = mediaUrl.match(/\.gif$/i);

    // Clear content
    storyContent.innerHTML = '';

    if (isVideo) {
    const video = document.createElement('video');
    video.className = 'highlight-story-media';
    video.src = mediaUrl;
    video.autoplay = true;
    video.playsInline = true;
    video.preload = 'auto';
    video.id = 'highlightStoryVideo';

    storyContent.appendChild(video);
    currentVideo = video;

    video.muted = false;
    video.volume = 1.0;

    video.play().catch(() => {
    video.muted = true;
    showUnmuteButton(video);
});

    video.addEventListener('ended', function() {
    if (!videoEnded) {
    videoEnded = true;
    markCurrentComplete();
    setTimeout(() => navigateStory('next'), 500);
}
});

    video.addEventListener('timeupdate', function() {
    if (!isPaused && video.duration && !videoEnded) {
    const progress = (video.currentTime / video.duration) * 100;
    updateProgressBar(progress);
}
});

} else if (isGif) {
    const video = document.createElement('video');
    video.className = 'highlight-story-media';
    video.src = mediaUrl;
    video.autoplay = true;
    video.loop = true;
    video.muted = true;
    video.playsInline = true;
    video.preload = 'auto';

    storyContent.appendChild(video);
    currentVideo = video;
    startImageProgress(10000);
} else {
    const img = document.createElement('img');
    img.className = 'highlight-story-media';
    img.src = mediaUrl;
    img.alt = 'Story';

    storyContent.appendChild(img);
    startImageProgress(10000);
}

    // Add tap zones
    const leftZone = document.createElement('div');
    leftZone.className = 'highlight-story-tap-zone highlight-story-tap-zone-left';
    leftZone.onclick = (e) => { e.stopPropagation(); navigateStory('prev'); };

    const rightZone = document.createElement('div');
    rightZone.className = 'highlight-story-tap-zone highlight-story-tap-zone-right';
    rightZone.onclick = (e) => { e.stopPropagation(); navigateStory('next'); };

    storyContent.appendChild(leftZone);
    storyContent.appendChild(rightZone);
}

    function showUnmuteButton(video) {
    const existingBtn = document.querySelector('.highlight-unmute-btn');
    if (existingBtn) existingBtn.remove();

    const unmuteBtn = document.createElement('button');
    unmuteBtn.className = 'highlight-unmute-btn';
    unmuteBtn.innerHTML = '🔇 Tap to unmute';

    unmuteBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    video.muted = false;
    video.volume = 1.0;
    video.play().catch(() => {});
    unmuteBtn.remove();
});

    storyContent.appendChild(unmuteBtn);
}

    function updateProgressBar(progress) {
    const bars = storyProgressContainer.querySelectorAll('.highlight-story-progress-bar-fill');
    const currentBar = bars[currentStoryIndex];
    if (currentBar) {
    currentBar.style.width = Math.min(progress, 100) + '%';
}
}

    function markCurrentComplete() {
    const bars = storyProgressContainer.querySelectorAll('.highlight-story-progress-bar-fill');
    const currentBar = bars[currentStoryIndex];
    if (currentBar) {
    currentBar.style.width = '100%';
    currentBar.classList.add('completed');
}
}

    function createProgressBars() {
    storyProgressContainer.innerHTML = '';
    currentStories.forEach((story, index) => {
    const bg = document.createElement('div');
    bg.className = 'highlight-story-progress-bar-bg';

    const fill = document.createElement('div');
    fill.className = 'highlight-story-progress-bar-fill';

    if (index < currentStoryIndex) {
    fill.classList.add('completed');
    fill.style.width = '100%';
}
    if (index === currentStoryIndex) {
    fill.classList.add('active');
}

    bg.appendChild(fill);
    storyProgressContainer.appendChild(bg);
});
}

    function startImageProgress(duration) {
    clearInterval(progressInterval);
    const bars = storyProgressContainer.querySelectorAll('.highlight-story-progress-bar-fill');
    const currentBar = bars[currentStoryIndex];
    if (!currentBar) return;

    let progress = 0;
    const interval = 50;
    const increment = (interval / duration) * 100;

    progressInterval = setInterval(() => {
    if (isPaused) return;
    progress += increment;
    updateProgressBar(progress);
    if (progress >= 100) {
    clearInterval(progressInterval);
    markCurrentComplete();
    setTimeout(() => navigateStory('next'), 500);
}
}, interval);
}

    function startProgress() {
    clearInterval(progressInterval);
    const story = currentStories[currentStoryIndex];
    if (!story) return;

    const emailPrefix = story.user?.email ? story.user.email.split('@')[0] : '';
    const mediaUrl = `/users/stories/${story.media_type}/${emailPrefix}/${story.media}`;
    const isVideo = story.media_type === 'video' || mediaUrl.match(/\.(mp4|mov|avi|webm)$/i);
    const isGif = mediaUrl.match(/\.gif$/i);

    if (!isVideo && !isGif) {
    startImageProgress(10000);
} else if (isGif) {
    startImageProgress(10000);
}
    // Video progress is handled by timeupdate event
}

    function navigateStory(direction) {
    clearInterval(progressInterval);

    if (currentVideo) {
    currentVideo.pause();
    currentVideo.removeAttribute('src');
    currentVideo.load();
    currentVideo = null;
}

    videoEnded = false;

    if (direction === 'next') {
    if (currentStoryIndex < currentStories.length - 1) {
    currentStoryIndex++;
    updateStoryUI();
    createProgressBars();
    setTimeout(() => startProgress(), 100);
} else {
    closeStoryViewer();
    return;
}
} else if (direction === 'prev') {
    if (currentStoryIndex > 0) {
    currentStoryIndex--;
    updateStoryUI();
    createProgressBars();
    setTimeout(() => startProgress(), 100);
}
}
}

    function closeStoryViewer() {
    clearInterval(progressInterval);

    if (currentVideo) {
    currentVideo.pause();
    currentVideo.removeAttribute('src');
    currentVideo.load();
    currentVideo = null;
}

    storyModal.style.display = 'none';
    document.body.style.overflow = '';
    storyContent.innerHTML = '';
    storyProgressContainer.innerHTML = '';
    currentStories = [];
    currentStoryIndex = 0;
    videoEnded = false;
    isPaused = false;
    highlightTitle = '';
    highlightCover = '';
}

    // Pause on hover
    storyContent.addEventListener('mouseenter', () => {
    isPaused = true;
    if (currentVideo) currentVideo.pause();
});

    storyContent.addEventListener('mouseleave', () => {
    isPaused = false;
    if (currentVideo) {
    currentVideo.play().catch(() => {});
}
});

    // Touch events for mobile
    let touchStartX = 0;
    let touchStartY = 0;

    storyContent.addEventListener('touchstart', (e) => {
    touchStartX = e.touches[0].clientX;
    touchStartY = e.touches[0].clientY;
    isPaused = true;
    if (currentVideo) currentVideo.pause();
});

    storyContent.addEventListener('touchend', (e) => {
    const diffX = touchStartX - e.changedTouches[0].clientX;
    const diffY = touchStartY - e.changedTouches[0].clientY;

    if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
    diffX > 0 ? navigateStory('next') : navigateStory('prev');
}

    isPaused = false;
    if (currentVideo) {
    currentVideo.play().catch(() => {});
}
});
});
