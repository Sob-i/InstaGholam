document.addEventListener('DOMContentLoaded', function () {
    console.log(this.dataset);
    document.querySelectorAll('.delete-comment').forEach(button => {

        button.addEventListener('click', function (e) {

            e.preventDefault();

            const commentId = this.dataset.id;
            const commentUid = this.dataset.uid;
            const postId = this.dataset.postId;
            const postUid = this.dataset.postUid;


            if (!commentId || !commentUid || !postId || !postUid) {
                console.error('Required comment/post data is missing.');
                return;
            }

            if (!confirm('Are you sure you want to delete this comment?')) {
                return;
            }

            const url = `/post/${postId}/comment/${commentId}/delete`;

            fetch(url, {
                method: 'POST',

                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),

                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },

                body: JSON.stringify({
                    comment_uid: commentUid,
                    post_uid: postUid
                })
            })

                .then(response => response.text())

                .then(responseText => {

                    let data;

                    try {
                        data = JSON.parse(responseText);
                    } catch (error) {
                        console.error('Invalid JSON response.');
                        throw error;
                    }

                    if (data.success) {

                        // Remove comment
                        const commentItem =
                            this.closest('.comment-item');

                        if (commentItem) {
                            commentItem.remove();
                        }

                        // Update comment count
                        const commentButtons =
                            document.querySelectorAll(
                                `.comments-count[data-post-id="${postId}"]`
                            );

                        commentButtons.forEach(commentButton => {

                            const countElement =
                                commentButton.querySelector(
                                    '.comment-count-number'
                                );

                            if (!countElement) {
                                return;
                            }

                            let currentCount =
                                parseInt(
                                    countElement.textContent.trim(),
                                    10
                                ) || 0;

                            const change =
                                Number(data.commentCount);

                            if (!Number.isNaN(change)) {
                                currentCount += change;
                            } else {
                                currentCount -= 1;
                            }

                            currentCount =
                                Math.max(0, currentCount);

                            countElement.textContent =
                                currentCount;
                        });

                        // Show "No comments" if this was the last comment
                        const commentsList =
                            document.querySelector(
                                `#comments-list-${postId}`
                            );

                        if (commentsList) {

                            const remainingComments =
                                commentsList.querySelectorAll(
                                    '.comment-item'
                                );

                            if (
                                remainingComments.length === 0 &&
                                !commentsList.querySelector('.no-comments')
                            ) {

                                commentsList.insertAdjacentHTML(
                                    'beforeend',
                                    `
                                    <div
                                        class="no-comments"
                                        style="color: #8e8e8e; font-size: 14px; padding: 10px 0;"
                                    >
                                        No comments yet. Be the first to comment!
                                    </div>
                                    `
                                );
                            }
                        }

                    } else {

                        alert(
                            data.message ||
                            'Failed to delete comment.'
                        );
                    }

                })

                .catch(error => {

                    console.error(
                        'Delete comment error:',
                        error
                    );

                    alert(
                        'Something went wrong while deleting the comment.'
                    );

                });

        });

    });

});
