
    document.addEventListener('DOMContentLoaded', function () {

    const savedContent = document.getElementById('saved-content');
    const savedScrollArea = document.getElementById('saved-scroll-area');
    const savedGrid = document.getElementById('saved-posts-grid');
    const loader = document.getElementById('saved-posts-loader');
    const endMessage = document.getElementById('saved-posts-end');

    if (!savedContent || !savedScrollArea || !savedGrid) {
    return;
}

    let currentPage = 1;
    let hasMore = true;
    let loading = false;


    /*
    |--------------------------------------------------------------------------
    | Load Saved Posts
    |--------------------------------------------------------------------------
    */

    async function loadSavedPosts(page = 1) {

    if (loading || !hasMore) {
    return;
}

    loading = true;

    if (page > 1) {
    loader.style.display = 'block';
}

    try {

        const url =
            window.savedPostsUrl + '?page=' + page;

    const response = await fetch(
    url,
{
    method: 'GET',
    headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
}
}
    );

    if (!response.ok) {
    throw new Error('Failed to load saved posts');
}

    const data = await response.json();


    /*
    |--------------------------------------------------------------------------
    | No Saved Posts
    |--------------------------------------------------------------------------
    */

    if (
    page === 1 &&
    (
    !data.saved_posts ||
    data.saved_posts.length === 0
    )
    ) {

    hasMore = false;

    savedGrid.innerHTML = `
                    <div class="empty-state">
                        <svg
                            width="62"
                            height="62"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            viewBox="0 0 24 24"
                        >
                            <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/>
                        </svg>

                        <p>No saved posts yet</p>
                    </div>
                `;

    return;
}



    /*
    |--------------------------------------------------------------------------
    | Render Posts
    |--------------------------------------------------------------------------
    */

    if (data.saved_posts) {

    data.saved_posts.forEach(function (savedPost) {

    if (!savedPost.post) {
    return;
}

    savedGrid.insertAdjacentHTML(
    'beforeend',
    createSavedPost(savedPost.post)
    );

});

}


    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    currentPage = data.current_page;
    hasMore = data.has_more;


    if (!hasMore) {
    endMessage.style.display = 'block';
}


    /*
    |--------------------------------------------------------------------------
    | Check if the container is tall enough to scroll
    |
    | If it isn't, automatically load the next page.
    |--------------------------------------------------------------------------
    */

    requestAnimationFrame(function () {

    const canScroll =
    savedScrollArea.scrollHeight >
    savedScrollArea.clientHeight;


    if (
    !canScroll &&
    hasMore &&
    !loading
    ) {

    loadSavedPosts(
    currentPage + 1
    );

}

});


} catch (error) {

    console.error(
    'Failed to load saved posts:',
    error
    );

} finally {

    loading = false;

    loader.style.display = 'none';

}

}


    /*
    |--------------------------------------------------------------------------
    | Create Saved Post HTML
    |--------------------------------------------------------------------------
    */

    function createSavedPost(post) {

    if (!post.post_files) {
    return '';
}


    const firstFile =
    post.post_files
    .split(',')[0]
    .trim();


    /*
    |--------------------------------------------------------------------------
    | Folder
    |--------------------------------------------------------------------------
    */

    const folderName =
    firstFile.split('-')[0] + '-posts';


    /*
    |--------------------------------------------------------------------------
    | Extension
    |--------------------------------------------------------------------------
    */

    const fileExtension =
    firstFile
    .split('.')
    .pop()
    .toLowerCase();


    const isVideo = [
    'mp4',
    'mov',
    'avi',
    'webm'
    ].includes(fileExtension);


    /*
    |--------------------------------------------------------------------------
    | Date
    |--------------------------------------------------------------------------
    */

    const date =
    formatDate(post.created_at);


    /*
    |--------------------------------------------------------------------------
    | File URL
    |--------------------------------------------------------------------------
    */

        const fileUrl =
            window.savedPostsAssetUrl +
            "/" +
            folderName +
            "/" +
            date +
            "/" +
            firstFile;


    /*
    |--------------------------------------------------------------------------
    | Post URL
    |--------------------------------------------------------------------------
    */

        const postUrl =
            window.savedPostUrl + "/" + post.id;


    let mediaHTML;


    /*
    |--------------------------------------------------------------------------
    | Video
    |--------------------------------------------------------------------------
    */

    if (isVideo) {

    mediaHTML = `
                <video
                    class="saved-video"
                    muted
                    playsinline
                    preload="metadata"
                    disablepictureinpicture
                    disableremoteplayback
                    style="
                        object-fit: cover;
                        object-position: center;
                        width: 100%;
                        height: 100%;
                        pointer-events: none;
                    "
                >
                    <source
                        src="${fileUrl}"
                        type="video/${fileExtension}"
                    >
                </video>
            `;

}


    /*
    |--------------------------------------------------------------------------
    | Image
    |--------------------------------------------------------------------------
    */

    else {

    mediaHTML = `
                <img
                    src="${fileUrl}"
                    loading="lazy"
                    alt="Post"
                    style="
                        object-fit: cover;
                        object-position: center;
                        width: 100%;
                        height: 100%;
                    "
                >
            `;

}


    return `
            <div class="pg-item">

                <a href="${postUrl}">

                    ${mediaHTML}

                    <div class="pg-overlay">

                        <span>
                            ♥ ${post.post_likes ?? 0}
                        </span>

                        <span>
                            💬 ${post.post_comments ?? 0}
                        </span>

                    </div>

                </a>

            </div>
        `;

}


    /*
    |--------------------------------------------------------------------------
    | Date Formatter
    |--------------------------------------------------------------------------
    */

    function formatDate(dateString) {

    const date =
    new Date(dateString);


    const year =
    date.getFullYear();


    const month =
    String(
    date.getMonth() + 1
    ).padStart(2, '0');


    const day =
    String(
    date.getDate()
    ).padStart(2, '0');


    return `${year}-${month}-${day}`;

}

    /*
    |--------------------------------------------------------------------------
    | Saved Tab
    |--------------------------------------------------------------------------
    */

    const savedTab =
    document.querySelector('[data-tab="saved"]');


    if (savedTab) {

    savedTab.addEventListener('click', function () {

    // Reset everything
    currentPage = 1;
    hasMore = true;
    loading = false;

    // Clear previous posts
    savedGrid.innerHTML = '';

    // Hide end message
    endMessage.style.display = 'none';

    // Hide loader before starting
    loader.style.display = 'none';

    // Load from page 1 again
    loadSavedPosts(1);

});

}

    /*
    |--------------------------------------------------------------------------
    | Scroll ONLY inside Saved Posts
    |--------------------------------------------------------------------------
    */

    savedScrollArea.addEventListener(
    'scroll',
    function () {

    if (
    loading ||
    !hasMore
    ) {
    return;
}


    const distanceFromBottom =
    savedScrollArea.scrollHeight -
    (
    savedScrollArea.scrollTop +
    savedScrollArea.clientHeight
    );


    if (
    distanceFromBottom <= 300
    ) {

    loadSavedPosts(
    currentPage + 1
    );

}

}
    );

});
