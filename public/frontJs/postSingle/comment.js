
    $(document).ready(function() {

    $(document).on('click', '.submit-comment', function(e) {
        e.preventDefault();

        let $button = $(this);
        let postId = $button.data('post-id');
        let $inputField = $('#comment-input-' + postId);
        let commentBody = $inputField.val().trim();

        if (commentBody === '') {
            $inputField.focus();
            return;
        }

        $button.prop('disabled', true).text('Posting...');

        $.ajax({
            url: '/post/' + postId + '/comments',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                comment: commentBody
            },
            success: function(response) {
                if (response.success) {
                    let $commentsList = $('#comments-list-' + postId);
                    $('#no-comments-' + postId).remove();

                    let newCommentHtml = `
    <div class="comment-item" style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; font-size: 14px; animation: fadeIn 0.3s ease;">
        <img src="/users/avatar/${response.comment.user.avatar || 'default-avatar.png'}"
             style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0;"
             alt="${response.comment.user.name}">
        <div style="flex: 1; line-height: 1.4;">
            <strong style="margin-right: 5px;">${response.comment.user.name}</strong>
            <span>${response.comment.content}</span>
      <span class="post-time" style="color: #8e8e8e; font-size: 12px; margin-right: 8px; direction: ltr; display: inline-block;">${response.comment.created_at || 'Just now'}</span>
        </div>
    </div>
`;

                    $commentsList.prepend(newCommentHtml);
                    $inputField.val('');
                    $inputField.focus();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    for (let field in errors) {
                        errorMessage += errors[field].join('\n') + '\n';
                    }
                    alert(errorMessage);
                } else if (xhr.status === 401) {
                    alert('please login');
                } else if (xhr.status === 500) {
                    alert('an error occurred');
                    console.error('Server Error:', xhr.responseText);
                }
            },
            complete: function() {
                $button.prop('disabled', false).text('Post');
            }
        });
    });

    $(document).on('keypress', '.comment-input', function(e) {
    if (e.which === 13) {
    e.preventDefault();
    let postId = $(this).attr('id').replace('comment-input-', '');
    $('.submit-comment[data-post-id="' + postId + '"]').click();
}
});

});
