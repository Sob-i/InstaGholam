
    $(document).ready(function () {

    let currentPostId = null;
    let currentPostUserId = null;

    let commentsPage = 1;
    let commentsLoading = false;
    let commentsHasMore = true;

    let replyingToId = null;
    let replyingToUsername = null;

    let repliesLoading = {};
    let repliesLoaded = {};

    function escapeHtml(text) {
    if (text === null || text === undefined) {
    return '';
}

    return $('<div>').text(text).html();
}

    function formatMention(text) {
    return text.replace(
    /@([a-zA-Z0-9_.]+)/g,
    '<span style="color: var(--accent);">@$1</span>'
    );
}

    function getAvatar(avatar) {
    if (!avatar) {
    return '/users/avatar/default-avatar.png';
}

    return '/users/avatar/' + avatar;
}

    function buildCommentMenu(comment) {

    let items = '';

    if (comment.can_report) {

    items += `
        <button
            type="button"
            class="modal-dropdown-item report-comment-btn"
            data-id="${comment.id}"
            data-type="comment"
            data-uid="${comment.user_id}"
        >
            Report
        </button>
    `;
}

    if (comment.can_delete) {
    items += `
                <button
                    type="button"
                    class="modal-dropdown-item delete-comment"
                    data-id="${comment.id}"
                    data-uid="${comment.user_id}"
                    data-post-id="${currentPostId}"
                    data-post-uid="${currentPostUserId}"
                >
                    Delete
                </button>
            `;
}

    if (!items) {
    return '';
}

    return `
            <div class="modal-comment-menu">

                <button
                    type="button"
                    class="modal-three-dot"
                >
                    <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <circle cx="12" cy="5" r="2"/>
                        <circle cx="12" cy="12" r="2"/>
                        <circle cx="12" cy="19" r="2"/>
                    </svg>
                </button>

                <div class="modal-comment-dropdown">
                    ${items}
                </div>

            </div>
        `;
}

    function buildVerifiedIcon(user) {

    if (
    user &&
    (
    user.role === 'admin' ||
    user.role === 'verifiedUser'
    )
    ) {
    return `
                <svg
                    class="verified-icon"
                    width="14"
                    height="14"
                    fill="var(--accent)"
                    viewBox="0 0 24 24"
                >
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            `;
}

    return '';
}

    function buildRepliesButton(comment) {

    const count = parseInt(comment.replies_count) || 0;

    if (count <= 0) {
    return '';
}

    return `
            <button
                type="button"
                class="show-replies-btn"
                data-post-id="${currentPostId}"
                data-comment-id="${comment.id}"
            >
                View ${count}
                ${count === 1 ? 'reply' : 'replies'}
            </button>
        `;
}

    function appendComment(comment, prepend = false) {

    const username =
    comment.user?.username || 'Unknown';

    const avatar =
    getAvatar(comment.user?.avatar);

    const verifiedIcon =
    buildVerifiedIcon(comment.user);

    const repliesButton =
    buildRepliesButton(comment);

    const menu =
    buildCommentMenu(comment);

    const html = `
            <div
                class="modal-comment-item"
                data-comment-id="${comment.id}"
            >

                <div class="modal-comment-row">

                    <a href="/profile/${encodeURIComponent(username)}">

                        <img
                            src="${avatar}"
                            class="modal-comment-avatar"
                            alt="${escapeHtml(username)}"
                        >

                    </a>

                    <div class="modal-comment-content">

                        <div class="modal-comment-top">

                            <a
                                href="/profile/${encodeURIComponent(username)}"
                                class="modal-comment-username"
                            >
                                ${escapeHtml(username)}
                            </a>

                           <span class="verifiedJ-icon">
                             ${verifiedIcon}
                        </span>

                        </div>

                        <div class="modal-comment-text">
                            ${formatMention(
    escapeHtml(comment.content)
    )}
                        </div>

                        <div class="modal-comment-meta">

                            <span class="modal-comment-time">
                                ${escapeHtml(
    comment.created_at || 'Just now'
    )}
                            </span>

                            <button
                                type="button"
                                class="modal-reply-btn"
                                data-comment-id="${comment.id}"
                                data-username="${escapeHtml(username)}"
                            >
                                Reply
                            </button>

                        </div>

                        ${repliesButton}

                    </div>

                    ${menu}

                </div>

            </div>
        `;

    if (prepend) {
    $('#modalCommentsList').prepend(html);
} else {
    $('#modalCommentsList').append(html);
}
}

    function appendReply($container, reply) {

    const username =
    reply.user?.username || 'Unknown';

    const avatar =
    getAvatar(reply.user?.avatar);

    const verifiedIcon =
    buildVerifiedIcon(reply.user);

    const menu =
    buildCommentMenu(reply);

    let mention = '';

    const repliesButton =
    buildRepliesButton(reply);

    const html = `
        <div
            class="modal-reply-item"
            data-comment-id="${reply.id}"
            data-parent-id="${reply.reply_comment_id || ''}"
        >

            <div class="modal-reply-row">

                <a href="/profile/${encodeURIComponent(username)}">

                    <img
                        src="${avatar}"
                        class="modal-comment-avatar"
                        alt="${escapeHtml(username)}"
                    >

                </a>

                <div class="modal-comment-content">

                    <div class="modal-comment-top">

                        <a
                            href="/profile/${encodeURIComponent(username)}"
                            class="modal-comment-username"
                        >
                            ${escapeHtml(username)}
                        </a>
                        <span class="verifiedJ-icon">
                             ${verifiedIcon}
                        </span>

                    </div>

                    <div class="modal-comment-text">

                        ${formatMention(
    escapeHtml(reply.content)
    )}
                    </div>

                    <div class="modal-comment-meta">

                        <span class="modal-comment-time">
                            ${escapeHtml(
    reply.created_at || 'Just now'
    )}
                        </span>

                        <button
                            type="button"
                            class="modal-reply-btn"
                            data-comment-id="${reply.id}"
                            data-username="${escapeHtml(username)}"
                        >
                            Reply
                        </button>

                    </div>

                    ${repliesButton}

                </div>

                ${menu}

            </div>

        </div>
    `;

    $container.append(html);
}

    function loadComments(reset = false) {

    if (commentsLoading) {
    return;
}

    if (!commentsHasMore && !reset) {
    return;
}

    if (reset) {
    commentsPage = 1;
    commentsHasMore = true;
    $('#modalCommentsList').empty();
}

    commentsLoading = true;

    if (commentsPage === 1) {
    $('#modalCommentsList').html(`
                <div class="comments-loading">
                    Loading comments...
                </div>
            `);
} else {
    $('#commentsMoreLoading').addClass('active');
}

    $.ajax({
    url: '/post/' + currentPostId + '/comments',
    method: 'GET',
    data: {
    page: commentsPage
},

    success: function (response) {

    if (commentsPage === 1) {
    $('#modalCommentsList').empty();
}

    if (
    response.status &&
    response.comments &&
    response.comments.length
    ) {

    response.comments.forEach(function (comment) {
    appendComment(comment);
});

    commentsHasMore =
    response.has_more;

    if (commentsHasMore) {
    commentsPage++;
}

} else if (commentsPage === 1) {

    $('#modalCommentsList').html(`
                        <div
                            class="no-modal-comments"
                            id="no-modal-comments"
                        >
                            No comments yet.
                        </div>
                    `);

    commentsHasMore = false;
}
},

    error: function (xhr) {
    console.error(
    'Comments error:',
    xhr.responseText
    );
},

    complete: function () {

    commentsLoading = false;

    $('#commentsMoreLoading')
    .removeClass('active');
}
});
}

    function loadReplies(parentId, $parent) {

    if (repliesLoading[parentId]) {
    return;
}

    repliesLoading[parentId] = true;

    let $replies =
    $parent.children('.comment-replies');

    if (!$replies.length) {

    $replies = $(`
                <div
                    class="comment-replies"
                    data-parent-id="${parentId}"
                ></div>
            `);

    $parent.append($replies);
}

    if (!repliesLoaded[parentId]) {

    $replies.html(`
                <div class="comments-loading">
                    Loading replies...
                </div>
            `);
}

    let page =
    repliesLoaded[parentId]?.page || 1;

    $.ajax({
    url:
    '/post/' +
    currentPostId +
    '/comment/' +
    parentId +
    '/replies',

    method: 'GET',

    data: {
    page: page
},

    success: function (response) {

    if (!repliesLoaded[parentId]) {
    $replies.empty();
}

    if (
    response.status &&
    response.replies &&
    response.replies.length
    ) {

    response.replies.forEach(function (reply) {
    appendReply($replies, reply);
});

    repliesLoaded[parentId] = {
    page: response.current_page + 1,
    hasMore: response.has_more
};

    const $button =
    $parent.find(
    '.show-replies-btn'
    ).first();

    if (response.has_more) {

    $button.text(
    'View more replies'
    );

} else {

    $button.remove();
}

} else {

    repliesLoaded[parentId] = {
    page: page,
    hasMore: false
};
}
},

    error: function (xhr) {

    console.error(
    'Replies error:',
    xhr.responseText
    );
},

    complete: function () {
    repliesLoading[parentId] = false;
}
});
}

    function startReply(commentId, username) {

    replyingToId = commentId;
    replyingToUsername = username;

    $('#replyingText').html(
    'Replying to <strong>@' +
    escapeHtml(username) +
    '</strong>'
    );

    $('#replyingIndicator').addClass('active');

    $('#modalCommentInput')
    .attr(
    'placeholder',
    'Reply to @' + username + '…'
    )
    .val('')
    .focus();
}

    function cancelReply() {

    replyingToId = null;
    replyingToUsername = null;

    $('#replyingIndicator')
    .removeClass('active');

    $('#modalCommentInput')
    .attr(
    'placeholder',
    'Add a comment…'
    )
    .val('');
}

    function submitComment() {

    const $input =
    $('#modalCommentInput');

    const $button =
    $('#modalSubmitComment');

    const text =
    $input.val().trim();

    if (!text) {
    $input.focus();
    return;
}

    $button
    .prop('disabled', true)
    .text('Posting...');

    $.ajax({
    url:
    '/post/' +
    currentPostId +
    '/sendComment',

    method: 'POST',

    data: {
    _token:
    $('meta[name="csrf-token"]').attr('content'),

    comment: text
},

    success: function (response) {

    if (!response.success) {
    return;
}

    $('#no-modal-comments').remove();

    appendComment(
    response.comment,
    true
    );

    $input.val('');

    $input.focus();

    $('.comments-modal-body')
    .scrollTop(0);
},

    error: function (xhr) {

    if (xhr.status === 422) {

    const errors =
    xhr.responseJSON.errors;

    let message = '';

    for (let field in errors) {
    message +=
    errors[field].join('\n') +
    '\n';
}

    alert(message);

} else if (xhr.status === 401) {

    alert('Please login');

} else {

    console.error(
    xhr.responseText
    );

    alert('An error occurred');
}
},

    complete: function () {

    $button
    .prop('disabled', false)
    .text('Post');
}
});
}

    function submitReply() {

    const $input = $('#modalCommentInput');
    const $button = $('#modalSubmitComment');

    const text = $input.val().trim();

    if (!text || !replyingToId) {
    return;
}

    $button
    .prop('disabled', true)
    .text('Posting...');

    $.ajax({
    url:
    '/post/' +
    currentPostId +
    '/' +
    replyingToId +
    '/sendCommentReply',

    method: 'POST',

    data: {
    _token:
    $('meta[name="csrf-token"]').attr('content'),

    reply: text
},

    success: function (response) {

    if (!response.success) {
    return;
}

    let parentId =
    response.reply.reply_comment_id ||
    replyingToId;

    let $parent =
    $('[data-comment-id="' + parentId + '"]').first();

    let $replies =
    $parent.children('.comment-replies');

    if (!$replies.length) {

    $replies = $(`
                    <div
                        class="comment-replies"
                        data-parent-id="${parentId}"
                    ></div>
                `);

    $parent.append($replies);
}

    response.reply.replying_to_username =
    replyingToUsername;

    appendReply(
    $replies,
    response.reply
    );

    $input.val('');

    cancelReply();
},

    error: function (xhr) {

    if (xhr.status === 422) {

    const errors =
    xhr.responseJSON.errors;

    let message = '';

    for (let field in errors) {
    message +=
    errors[field].join('\n') +
    '\n';
}

    alert(message);

} else {

    console.error(
    xhr.responseText
    );

    alert('An error occurred');
}
},

    complete: function () {

    $button
    .prop('disabled', false)
    .text('Post');
}
});
}

    $(document).on(
    'click',
    '.comments-count',
    function () {

    currentPostId =
    $(this).data('post-id');

    currentPostUserId =
    $(this).data('post-user-id');

    commentsPage = 1;
    commentsHasMore = true;

    repliesLoading = {};
    repliesLoaded = {};

    cancelReply();

    $('#commentsModal')
    .addClass('active');

    loadComments(true);
}
    );

    $(document).on(
    'click',
    '#closeCommentsModal, .comments-modal',
    function (e) {

    if (
    e.target === this ||
    $(e.target).is('#closeCommentsModal')
    ) {

    $('#commentsModal')
    .removeClass('active');

    cancelReply();
}
}
    );

    $(document).on(
    'click',
    '.modal-three-dot',
    function (e) {

    e.preventDefault();
    e.stopPropagation();

    const $dropdown =
    $(this)
    .closest('.modal-comment-menu')
    .find('.modal-comment-dropdown');

    $('.modal-comment-dropdown')
    .not($dropdown)
    .removeClass('active');

    $dropdown.toggleClass('active');
}
    );

    $(document).on(
    'click',
    '.modal-comment-dropdown',
    function (e) {
    e.stopPropagation();
}
    );

    $(document).on(
    'click',
    function () {

    $('.modal-comment-dropdown')
    .removeClass('active');
}
    );

    $(document).on(
    'click',
    '.modal-reply-btn',
    function (e) {

    e.preventDefault();

    const commentId =
    $(this).data('comment-id');

    const username =
    $(this).data('username');

    startReply(
    commentId,
    username
    );
}
    );

    $(document).on(
    'click',
    '.show-replies-btn',
    function (e) {

    e.preventDefault();

    const $button =
    $(this);

    const parentId =
    $button.data('comment-id');

    const $parent =
    $button.closest(
    '.modal-comment-item, .modal-reply-item'
    );

    if (
    repliesLoaded[parentId] &&
    !repliesLoaded[parentId].hasMore
    ) {
    return;
}

    loadReplies(
    parentId,
    $parent
    );
}
    );

    $(document).on(
    'click',
    '#cancelReply',
    function () {
    cancelReply();
}
    );

    $(document).on(
    'click',
    '#modalSubmitComment',
    function (e) {

    e.preventDefault();

    if (replyingToId) {
    submitReply();
} else {
    submitComment();
}
}
    );

    $(document).on(
    'keypress',
    '#modalCommentInput',
    function (e) {

    if (e.which === 13) {

    e.preventDefault();

    $('#modalSubmitComment')
    .click();
}
}
    );

    $('.comments-modal-body').on(
    'scroll',
    function () {

    const element = this;

    if (
    element.scrollTop +
    element.clientHeight >=
    element.scrollHeight - 100
    ) {

    if (
    !commentsLoading &&
    commentsHasMore
    ) {
    loadComments();
}
}
}
    );

});
    $(document).on('click', '.delete-comment', function (e) {

    e.preventDefault();
    e.stopPropagation();

    const $button = $(this);

    const commentId = $button.data('id');
    const postId = $button.data('post-id');

    if (!commentId || !postId) {
    console.error('Missing comment ID or post ID');
    return;
}

    if (!confirm('Delete this comment?')) {
    return;
}

    $button.prop('disabled', true).text('Deleting...');

    $.ajax({
    url: '/post/' + postId + '/comment/' + commentId + '/delete',
    method: 'POST',

    data: {
    _token: $('meta[name="csrf-token"]').attr('content')
},

    success: function (response) {

    if (!response.success) {
    alert(response.message || 'Could not delete comment.');
    return;
}

    const $comment =
    $('[data-comment-id="' + commentId + '"]').first();

    $comment.fadeOut(250, function () {
    $(this).remove();
});

    $('.modal-comment-dropdown')
    .removeClass('active');

    const $count =
    $('.comments-count[data-post-id="' + postId + '"]')
    .find('.comment-count-number');

    if ($count.length) {

    let current =
    parseInt($count.text()) || 0;

    if (current > 0) {
    $count.text(current - 1);
}
}
},

    error: function (xhr) {

    console.error(
    'Delete error:',
    xhr.responseText
    );

    if (xhr.status === 403) {
    alert('You are not allowed to delete this comment.');
} else if (xhr.status === 404) {
    alert('Comment not found.');
} else if (xhr.status === 401) {
    alert('Please login.');
} else {
    alert('An error occurred while deleting the comment.');
}
},

    complete: function () {
    $button
    .prop('disabled', false)
    .text('Delete');
}
});
});
    document.addEventListener("click", function (e) {

    const btn = e.target.closest(".report-btn, .report-comment-btn");

    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    document.getElementById("reported-user-id").value =
    btn.dataset.id;

    document.getElementById("report-type").value =
    btn.dataset.type;

    document.getElementById("reporter-uid").value =
    btn.dataset.uid;

    document.querySelectorAll(".modal-comment-dropdown")
    .forEach(function (dropdown) {
    dropdown.classList.remove("active");
});

    reportModal.style.display = "flex";
});
