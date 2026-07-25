document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-bar input');
    const gridContainer = document.querySelector('.grid');
    let debounceTimer;
    let originalGridContent = gridContainer.innerHTML; // Store original grid content

    // Create results dropdown container (for users only)
    const resultsDropdown = document.createElement('div');
    resultsDropdown.className = 'search-results-dropdown';
    resultsDropdown.style.cssText = `
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #000000;
        border: 1px solid #333;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    `;

    // Make search-bar position relative for dropdown
    const searchBar = document.querySelector('.search-bar');
    searchBar.style.position = 'relative';
    searchBar.appendChild(resultsDropdown);

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchBar.contains(e.target)) {
            resultsDropdown.style.display = 'none';
        }
    });

    // Handle search input
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            resultsDropdown.style.display = 'none';
            // Restore original grid if search is cleared
            if (query.length === 0) {
                gridContainer.innerHTML = originalGridContent;
            }
            return;
        }

        // Debounce to avoid too many requests
        debounceTimer = setTimeout(() => {
            performSearch(query);
        }, 300);
    });

    // Check if there's a search query in the URL (for tag clicks)
    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('search');

    if (searchQuery && searchInput) {
        searchInput.value = searchQuery;
        // Trigger the search automatically
        setTimeout(() => {
            searchInput.dispatchEvent(new Event('input'));
        }, 100);
    }

    function performSearch(query) {
        // Show single loading state in grid only
        gridContainer.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;">Searching...</div>';
        resultsDropdown.style.display = 'none'; // Keep dropdown hidden while loading

        fetch(`/explore/search?search=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                const hasPosts = data.placesAndTags && data.placesAndTags.length > 0;
                const hasUsers = data.users && data.users.length > 0;

                // Display posts in the grid
                if (hasPosts) {
                    displayPostsInGrid(data.placesAndTags);
                } else {
                    gridContainer.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;">No posts or places found</div>';
                }

                // Display users in dropdown (only if there are users)
                if (hasUsers) {
                    displayUsersInDropdown(data.users);
                } else {
                    resultsDropdown.style.display = 'none';
                }

                // If nothing found at all
                if (!hasPosts && !hasUsers) {
                    gridContainer.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;">No results found</div>';
                    resultsDropdown.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                gridContainer.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #e53e3e;">Error searching. Please try again.</div>';
                resultsDropdown.style.display = 'none';
            });
    }

    function displayPostsInGrid(posts) {
        // Use the same height classes from your Blade template
        const heightClasses = ['g1', 'g2', 'g3', 'g4', 'g5', 'g6', 'g7', 'g8', 'g9'];
        const minHeights = {
            'g1': '403px',
            'g2': '200px',
            'g3': '300px',
            'g4': '350px',
            'g5': '250px',
            'g6': '400px',
            'g7': '280px',
            'g8': '320px',
            'g9': '380px'
        };

        let html = '';

        posts.forEach((post, index) => {
            const firstFile = post.post_files ? post.post_files.split(',')[0] : null;
            const fileExtension = firstFile ? firstFile.split('.').pop().toLowerCase() : '';
            const isVideo = ['mp4', 'mov', 'avi', 'webm'].includes(fileExtension);
            const folderName = post.user?.email?.split('@')[0] + '-posts';
            const datePath = new Date(post.created_at).toISOString().split('T')[0];
            const fileUrl = firstFile ? `/users/posts/${folderName}/${datePath}/${firstFile}` : '';
            const heightClass = heightClasses[index % heightClasses.length];
            const minHeight = minHeights[heightClass];

            html += `
                <div class="grid-item">
                    <a href="/post/${post.id}">
                        <div class="grid-img ${heightClass}" style="min-height: ${minHeight};">
                            ${firstFile ?
                (isVideo ?
                        `<video style="object-fit: cover; object-position: center; width: 100%; height: 100%; pointer-events: none;"
                                           muted
                                           preload="metadata"
                                           disablepictureinpicture
                                           disableremoteplayback>
                                        <source src="${fileUrl}" type="video/${fileExtension}">
                                    </video>` :
                        `<img src="${fileUrl}" style="object-fit: cover; object-position: center; width: 100%; height: 100%;" onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\\'width: 100%; height: 100%; background: #333; display: flex; align-items: center; justify-content: center; color: #C8F04DFF; font-size: 40px;\\'>📷</div>'">`
                ) :
                '<div style="width: 100%; height: 100%; background: #333; display: flex; align-items: center; justify-content: center; color: #C8F04DFF; font-size: 40px;">📷</div>'
            }
                        </div>
                        <div class="overlay" style="color: white;">
                            <span>♥ ${post.post_likes || 0}</span>
                            <span>💬 ${post.post_comments || 0}</span>
                        </div>
                    </a>
                </div>
            `;
        });

        gridContainer.innerHTML = html;
    }

    function displayUsersInDropdown(users) {
        let html = '';

        html += '<div style="padding: 12px 16px; background: #1a1a1a; font-size: 14px; font-weight: 600; color: #C8F04DFF;">People</div>';

        users.forEach(user => {
            const profilePic = user.avatar
                ? `/users/avatar/${user.avatar}`
                : null;

            html += `
                <a href="/profile/${user.username}" class="search-result-item" style="display: flex; align-items: center; padding: 12px 16px; text-decoration: none; color: #ffffff; border-bottom: 1px solid #333; transition: background-color 0.2s;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; margin-right: 12px; background: #333; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        ${profilePic
                ? `<img src="${profilePic}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" alt="${user.username}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">`
                : ''
            }
                        <span style="font-size: 16px; font-weight: 600; color: #C8F04DFF; display: ${profilePic ? 'none' : 'flex'}; align-items: center; justify-content: center; width: 100%; height: 100%;">${user.username.charAt(0).toUpperCase()}</span>
                    </div>
                    <div>
                        <div style="font-weight: 500;">${user.username}</div>
                        <div style="font-size: 12px; color: #999;">@${user.username}</div>
                    </div>
                </a>
            `;
        });

        resultsDropdown.innerHTML = html;
        resultsDropdown.style.display = 'block';

        // Add hover effect to all result items using CSS
        const styleSheet = document.createElement('style');
        styleSheet.textContent = `
            .search-result-item {
                transition: all 0.2s ease;
            }
            .search-result-item:hover {
                background-color: #C8F04DFF !important;
                color: #000000 !important;
            }
            .search-result-item:hover * {
                color: #000000 !important;
            }
            .search-result-item:hover div[style*="color: #999"] {
                color: #000000 !important;
            }
        `;

        // Remove any existing style to prevent duplicates
        const existingStyle = resultsDropdown.querySelector('style');
        if (existingStyle) {
            existingStyle.remove();
        }
        resultsDropdown.appendChild(styleSheet);
    }

    function timeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);

        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + "y ago";

        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + "mo ago";

        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + "d ago";

        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + "h ago";

        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + "m ago";

        return "just now";
    }
});
