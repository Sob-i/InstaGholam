class PostsSearch {
    constructor() {
        this.searchInput = document.querySelector('.filter-search input');
        this.postsGrid = document.getElementById('postsGrid');
        this.tabButtons = document.querySelectorAll('.tab-btn');
        this.searchTimeout = null;
        this.activeTab = 'recent';
        this.originalHTML = null;

        this.init();
    }

    init() {
        if (!this.searchInput || !this.postsGrid) return;

        this.originalHTML = this.postsGrid.innerHTML;

        // Search handler
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            const term = e.target.value.trim();

            if (!term) {
                this.loadDefaultTab();
                return;
            }

            this.searchTimeout = setTimeout(() => this.search(term), 300);
        });

        // Tab switching
        this.tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                this.tabButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.activeTab = btn.dataset.tab;

                const term = this.searchInput.value.trim();
                if (term) {
                    this.search(term);
                } else {
                    this.loadDefaultTab();
                }
            });
        });
    }

    getEndpoint() {
        const endpoints = {
            recent: '/admin/posts/search',
            flagged: '/admin/posts/searchFlagged',
            hidden: '/admin/posts/searchHidden'
        };
        return endpoints[this.activeTab] || endpoints.recent;
    }

    async search(term) {
        try {
            this.postsGrid.style.opacity = '0.5';

            const response = await fetch(`${this.getEndpoint()}?search=${encodeURIComponent(term)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.renderPosts(data.posts);
            } else {
                this.showEmpty(data.message);
            }
        } catch (error) {
            console.error('Search failed:', error);
            this.showEmpty('Search failed. Please try again.');
        } finally {
            this.postsGrid.style.opacity = '1';
        }
    }

    async loadDefaultTab() {
        try {
            const response = await fetch(`${this.getEndpoint()}?search=`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.renderPosts(data.posts);
                this.originalHTML = this.postsGrid.innerHTML;
            }
        } catch (error) {
            console.error('Failed to load tab:', error);
            this.postsGrid.innerHTML = this.originalHTML;
        }
    }

    renderPosts(posts) {
        if (!posts.length) {
            this.showEmpty('No posts found');
            return;
        }

        this.postsGrid.innerHTML = posts.map(post =>
            this.createPostCard(post)
        ).join('');
    }

    createPostCard(post) {
        const firstFile = post.post_files ? post.post_files.split(',')[0] : '';
        const ext = firstFile ? firstFile.split('.').pop().toLowerCase() : '';
        const isVideo = ['mp4', 'mov', 'avi', 'webm'].includes(ext);
        const emailPrefix = post.user ? post.user.email.split('@')[0] : 'unknown';
        const datePath = post.created_at ? new Date(post.created_at).toISOString().split('T')[0] : 'unknown-date';
        const postPath = `users/posts/${emailPrefix}-posts/${datePath}/${firstFile}`;
        const likesFormatted = post.likes_formatted || '0';
        const commentsFormatted = post.comments_formatted || '0';

        // Recent tab - Your original style
        if (this.activeTab === 'recent') {
            return `
                <div class="pg-card">
                    ${isVideo ? `
                        <div class="pg-thumb">
                            <video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;" muted preload="metadata" disablepictureinpicture disableremoteplayback>
                                <source src="/${postPath}" type="video/${ext}">
                            </video>
                        </div>
                    ` : `
                        <div class="pg-thumb">
                            <img src="/${postPath}" alt="Post image" loading="lazy" style="object-fit: cover; object-position: center; width: 100%; height: 100%;">
                        </div>
                    `}
                    <div class="pg-info">
                        <div class="pg-user">${post.user?.username || 'Unknown'}</div>
                        <div class="pg-caption">${post.post_caption || 'NoCaption'}</div>
                        <div class="pg-stats">
                            <span class="pg-stat">♥ ${likesFormatted}</span>
                            <span class="pg-stat">💬 ${commentsFormatted}</span>
                            <span style="margin-left:auto;"><span class="status-dot dot-green"></span></span>
                        </div>
                    </div>
                    <div class="pg-actions">
                        <a href="/admin/post/${post.id}" class="pg-btn" style="text-decoration: none">View</a>
                        <button class="pg-btn flag" data-action="flag" data-post-id="${post.id}">Flag</button>
                        <button class="pg-btn danger" data-action="delete" data-post-id="${post.id}">Delete</button>
                    </div>
                </div>
            `;
        }

        // Flagged tab - Your original style
        if (this.activeTab === 'flagged') {
            return `
                <div class="pg-card flagged-card">
                    <div class="pg-thumb">
                        <span class="badge-pill pill-red">⚑ Flagged</span>
                        ${isVideo ? `
                            <video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;" muted preload="metadata" disablepictureinpicture disableremoteplayback>
                                <source src="/${postPath}" type="video/${ext}">
                            </video>
                        ` : `
                            <img src="/${postPath}" alt="Post image" loading="lazy" style="object-fit: cover; object-position: center; width: 100%; height: 100%;">
                        `}
                    </div>
                    <div class="pg-info">
                        <div class="pg-user">${post.user?.username || 'Unknown'}</div>
                        <div class="pg-caption">${post.post_caption || 'NoCaption'}</div>
                        <div class="pg-caption">${post.post_tags || 'NoTags'}</div>
                        <div class="pg-stats">
                            <span class="pg-stat">♥ ${likesFormatted}</span>
                            <span class="pg-stat">💬 ${commentsFormatted}</span>
                            <span class="pg-stat" style="color:var(--red)">⚑ 8 reports</span>
                        </div>
                    </div>
                    <div class="pg-actions">
                        <button class="pg-btn" data-action="review" data-post-id="${post.id}">Review</button>
                        <button class="pg-btn approve" data-action="approve" data-post-id="${post.id}">Approve</button>
                         <button class="pg-btn danger" data-action="delete" data-post-id="${post.id}">Delete</button>
                    </div>
                </div>
            `;
        }

        // Hidden tab - Your original style
        return `
            <div class="pg-card hidden-card">
                <div class="pg-thumb dimmed">
                    <span class="badge-pill pill-amber">👁 Hidden</span>
                    ${isVideo ? `
                        <video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;" muted preload="metadata" disablepictureinpicture disableremoteplayback>
                            <source src="/${postPath}" type="video/${ext}">
                        </video>
                    ` : `
                        <img src="/${postPath}" alt="Post image" loading="lazy" style="object-fit: cover; object-position: center; width: 100%; height: 100%;">
                    `}
                </div>
                <div class="pg-info">
                    <div class="pg-user">${post.user?.username || 'Unknown'}</div>
                    <div class="pg-caption">${post.post_caption || 'NoCaption'}</div>
                    <div class="pg-caption">${post.post_tags || 'NoTags'}</div>
                    <div class="pg-stats">
                        <span class="pg-stat">♥ ${likesFormatted}</span>
                        <span class="pg-stat">💬 ${commentsFormatted}</span>
                        <span class="pg-stat" style="color:var(--amber)">⚑ 5 reports</span>
                    </div>
                </div>
                <div class="pg-actions">
                    <button class="pg-btn" data-action="review" data-post-id="${post.id}">Review</button>
                    <button class="pg-btn approve" data-action="restore" data-post-id="${post.id}">Restore</button>
                    <button class="pg-btn danger" data-action="delete" data-post-id="${post.id}">Delete</button>
                </div>
            </div>
        `;
    }

    showEmpty(message = 'No posts found') {
        const tabIcons = {
            recent: '📭',
            flagged: '⚠️',
            hidden: '👁'
        };

        this.postsGrid.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 4rem 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">${tabIcons[this.activeTab] || '📭'}</div>
                <p style="color: var(--text-secondary); font-size: 1rem;">${message}</p>
            </div>
        `;
    }
}

// Add styles
const style = document.createElement('style');
style.textContent = `
    .pg-card.flagged-card {
        border: 1px solid rgba(255, 68, 68, 0.3);
    }

    .pg-card.hidden-card {
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .pg-thumb.dimmed {
        opacity: 0.7;
        filter: brightness(0.8);
    }

    .badge-pill {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 10;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        color: white;
        backdrop-filter: blur(8px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .pill-red {
        background: rgba(239, 68, 68, 0.95);
    }

    .pill-amber {
        background: rgba(245, 158, 11, 0.95);
    }

    .pg-btn.approve {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .pg-btn.approve:hover {
        background: rgba(34, 197, 94, 0.2);
    }
`;
document.head.appendChild(style);

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    new PostsSearch();
});
