<script>
    const data = {
        chat_id: '{{ $chat->id ?? '' }}',
        sender_id: '{{auth()->id() ?? ''}}',
        receiver_id: document.getElementById('receiver_id')?.value || '',
        message: document.getElementById('message_txt')?.value || '',
        attachments: document.getElementById('attachments')?.value || null,
        type: 'message',
    };
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const messages = document.querySelector('.thread-messages');

        if (messages) {
            messages.scrollTop = messages.scrollHeight;
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const messages = document.querySelector('.thread-messages');
        const scrollBtn = document.getElementById('scrollToBottom');

        if (!messages || !scrollBtn) return;

        // Start at the latest message
        messages.scrollTop = messages.scrollHeight;

        // Show/hide button when scrolling
        messages.addEventListener('scroll', function () {

            const distanceFromBottom =
                messages.scrollHeight -
                messages.scrollTop -
                messages.clientHeight;

            if (distanceFromBottom > 200) {
                scrollBtn.style.display = 'flex';
            } else {
                scrollBtn.style.display = 'none';
            }
        });

        // Scroll to bottom
        scrollBtn.addEventListener('click', function () {

            messages.scrollTo({
                top: messages.scrollHeight,
                behavior: 'smooth'
            });

        });

    });
</script>
<script src="{{asset('frontJs/messages/sendMessage.js')}}"></script>
<script src="{{asset('frontJs/messages/searchMessage.js')}}"></script>
</body>
</html>
