
    $(document).ready(function() {
    // Like/Unlike functionality
    $(document).on('click', '.like-btn', function(e) {
        e.preventDefault();

        const $btn = $(this);
        const postId = $btn.data('post-id');
        const $countSpan = $btn.find('.likes-count');
        const $icon = $btn.find('.like-icon');
        const showCount = $btn.data('show-count'); // Get show_count from button

        // Optimistic UI update
        const isCurrentlyLiked = $btn.hasClass('liked');

        // Toggle visual state immediately
        if (isCurrentlyLiked) {
            $btn.removeClass('liked');
            $icon.attr('fill', 'none').attr('stroke', 'currentColor');
            // Only update count if it's visible
            if (showCount && $countSpan.is(':visible')) {
                let currentCount = parseInt($countSpan.text()) || 0;
                $countSpan.text(Math.max(0, currentCount - 1));
            }
        } else {
            $btn.addClass('liked');
            $icon.attr('fill', '#ed4956').attr('stroke', '#ed4956');
            // Only update count if it's visible
            if (showCount && $countSpan.is(':visible')) {
                let currentCount = parseInt($countSpan.text()) || 0;
                $countSpan.text(currentCount + 1);
            }

            // Trigger like animation
            triggerLikeAnimation($btn);
        }

        // Disable button temporarily to prevent double clicks
        $btn.prop('disabled', true);

        // Send AJAX request
        $.ajax({
            url: `/post/${postId}/like`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update like state based on server response
                    if (response.is_liked) {
                        $btn.addClass('liked');
                        $icon.attr('fill', '#ed4956').attr('stroke', '#ed4956');
                    } else {
                        $btn.removeClass('liked');
                        $icon.attr('fill', 'none').attr('stroke', 'currentColor');
                    }

                    // Handle count display based on server response
                    if (response.show_count && response.formatted_count !== null) {
                        $countSpan.text(response.formatted_count).show();
                        $btn.data('show-count', true);
                    } else {
                        // Completely hide and empty the count
                        $countSpan.text('').hide();
                        $btn.data('show-count', false);
                    }
                }
            },
            error: function(xhr) {
                // Revert optimistic update on error
                if (isCurrentlyLiked) {
                    $btn.addClass('liked');
                    $icon.attr('fill', '#ed4956').attr('stroke', '#ed4956');
                } else {
                    $btn.removeClass('liked');
                    $icon.attr('fill', 'none').attr('stroke', 'currentColor');
                }

                // Restore original state
                const originalCount = $countSpan.data('original-count');
                const originalShowCount = $countSpan.data('original-show-count');

                if (originalShowCount) {
                    $countSpan.text(originalCount).show();
                    $btn.data('show-count', true);
                } else {
                    $countSpan.text('').hide();
                    $btn.data('show-count', false);
                }

                console.error('Like error:', xhr.responseText);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    // Like animation (heart burst effect)
    function triggerLikeAnimation($btn) {
    // Create floating heart animation
    const $heart = $('<div class="like-animation">❤️</div>');
    $btn.append($heart);

    $heart.css({
    position: 'absolute',
    top: '-20px',
    left: '50%',
    transform: 'translateX(-50%)',
    fontSize: '24px',
    animation: 'likeFloat 0.8s ease-out forwards',
    pointerEvents: 'none',
    zIndex: 100
});

    setTimeout(() => $heart.remove(), 800);
}

    // Initialize like buttons
    $('.like-btn').each(function() {
    const $btn = $(this);
    const $countSpan = $btn.find('.likes-count');
    const showCount = $btn.data('show-count');

    // Store original state for error recovery
    if (showCount) {
    $countSpan.data('original-count', $countSpan.text());
    $countSpan.data('original-show-count', true);
}

});
});
