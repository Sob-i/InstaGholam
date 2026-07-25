
    $(document).ready(function() {
    // Save/Unsave functionality
    $(document).on('click', '.save-btn', function(e) {
        e.preventDefault();

        const $btn = $(this);
        const postId = $btn.data('post-id');
        const $icon = $btn.find('.save-icon');

        // Optimistic UI update
        const isCurrentlySaved = $btn.hasClass('saved');

        // Toggle visual state immediately
        if (isCurrentlySaved) {
            $btn.removeClass('saved');
            $icon.attr('fill', 'none').attr('stroke', 'currentColor');
        } else {
            $btn.addClass('saved');
            $icon.attr('fill', 'var(--accent)').attr('stroke', 'var(--accent)');

            // Trigger save animation
            triggerSaveAnimation($btn);
        }

        // Disable button temporarily to prevent double clicks
        $btn.prop('disabled', true);

        // Send AJAX request
        $.ajax({
            url: `/post/${postId}/save`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update save state based on server response
                    if (response.is_saved) {
                        $btn.addClass('saved');
                        $icon.attr('fill', 'var(--accent)').attr('stroke', 'var(--accent)');
                    } else {
                        $btn.removeClass('saved');
                        $icon.attr('fill', 'none').attr('stroke', 'currentColor');
                    }
                }
            },
            error: function(xhr) {
                // Revert optimistic update on error
                if (isCurrentlySaved) {
                    $btn.addClass('saved');
                    $icon.attr('fill', 'var(--accent)').attr('stroke', 'var(--accent)');
                } else {
                    $btn.removeClass('saved');
                    $icon.attr('fill', 'none').attr('stroke', 'currentColor');
                }

                console.error('Save error:', xhr.responseText);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    // Save animation (bookmark floating effect)
    function triggerSaveAnimation($btn) {
    // Create floating bookmark animation
    const $bookmark = $('<div class="save-animation">📑</div>');
    $btn.append($bookmark);

    $bookmark.css({
    position: 'absolute',
    top: '-20px',
    left: '50%',
    transform: 'translateX(-50%)',
    fontSize: '24px',
    animation: 'saveFloat 0.8s ease-out forwards',
    pointerEvents: 'none',
    zIndex: 100
});

    setTimeout(() => $bookmark.remove(), 800);
}
});
