document.addEventListener('DOMContentLoaded', function() {
    const postsGrid = document.getElementById('postsGrid');
    const baseUrl = window.adminRoutes.baseUrl;

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const tab = this.getAttribute('data-tab');

            if (tab === 'recent') {
                window.location.reload();
                return;
            }

            // USE window.adminRoutes HERE - NOT Blade syntax
            const url = tab === 'flagged' ? window.adminRoutes.flagged : window.adminRoutes.hidden;

            postsGrid.innerHTML = '<div style="text-align: center; padding: 40px;">Loading...</div>';

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(posts => {
                    postsGrid.innerHTML = '';

                    if (!Array.isArray(posts)) {
                        console.error('Expected array but got:', posts);
                        postsGrid.innerHTML = '<div style="text-align: center; padding: 40px; color: red;">Invalid data format</div>';
                        return;
                    }

                    posts.forEach(post => {
                        const firstFile = post.post_files.split(',')[0];
                        const fileExtension = firstFile.split('.').pop().toLowerCase();
                        const isVideo = ['mp4', 'mov', 'avi', 'webm'].includes(fileExtension);
                        const emailPrefix = post.user.email.split('@')[0];
                        const postDate = post.created_at.split('T')[0];
                        const postPath = baseUrl + 'users/posts/' + emailPrefix + '-posts/' + postDate + '/' + firstFile;
                        const likesFormatted = post.likes_formatted || '0';
                        const commentsFormatted = post.comments_formatted || '0';

                        let cardHTML = '';

                        if (tab === 'flagged') {
                            cardHTML = `
                                <div class="pg-card flagged-card">
                                    <div class="pg-thumb">
                                        <span class="badge-pill pill-red">⚑ Flagged</span>
                                        ${isVideo ?
                                `<video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;" muted preload="metadata" disablepictureinpicture disableremoteplayback>
                                                <source src="${postPath}" type="video/${fileExtension}">
                                            </video>` :
                                `<img src="${postPath}" alt="Post image" loading="lazy" style="object-fit: cover; object-position: center; width: 100%; height: 100%;">`
                            }
                                    </div>
                                    <div class="pg-info">
                                        <div class="pg-user">${post.user.username}</div>
                                        <div class="pg-caption">${post.post_caption || 'NoCaption'}</div>
                                        <div class="pg-caption">${post.post_tags || 'NoTags'}</div>
                                        <div class="pg-stats">
                                            <span class="pg-stat">♥ ${likesFormatted}</span>
                                            <span class="pg-stat">💬 ${commentsFormatted}</span>
                                            <span class="pg-stat" style="color:var(--red)">⚑ 8 reports</span>
                                        </div>
                                    </div>
                                    <div class="pg-actions">
                                        <button class="pg-btn">Review</button>
                                        <button class="pg-btn approve">Approve</button>
                                       <button class="pg-btn danger">Delete</button>
                                    </div>
                                </div>
                            `;
                        } else {
                            cardHTML = `
                                <div class="pg-card hidden-card">
                                    <div class="pg-thumb dimmed">
                                        <span class="badge-pill pill-amber">Hidden</span>
                                        ${isVideo ?
                                `<video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;" muted preload="metadata" disablepictureinpicture disableremoteplayback>
                                                <source src="${postPath}" type="video/${fileExtension}">
                                            </video>` :
                                `<img src="${postPath}" alt="Post image" loading="lazy" style="object-fit: cover; object-position: center; width: 100%; height: 100%;">`
                            }
                                    </div>
                                    <div class="pg-info">
                                        <div class="pg-user">${post.user.username}</div>
                                        <div class="pg-caption">${post.post_caption || 'NoCaption'}</div>
                                        <div class="pg-caption">${post.post_tags || 'NoTags'}</div>
                                        <div class="pg-stats">
                                            <span class="pg-stat">♥ ${likesFormatted}</span>
                                            <span class="pg-stat">💬 ${commentsFormatted}</span>
                                            <span class="pg-stat" style="color:var(--amber)">⚑ 5 reports</span>
                                        </div>
                                    </div>
                                    <div class="pg-actions">
                                        <button class="pg-btn">Review</button>
                                        <button class="pg-btn approve">Restore</button>
                                        <button class="pg-btn danger">Delete</button>
                                    </div>
                                </div>
                            `;
                        }

                        postsGrid.insertAdjacentHTML('beforeend', cardHTML);
                    });

                    if (posts.length === 0) {
                        postsGrid.innerHTML = '<div style="text-align: center; padding: 40px; color: #999;">No posts found</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    postsGrid.innerHTML = '<div style="text-align: center; padding: 40px; color: red;">Error loading posts</div>';
                });
        });
    });
});
