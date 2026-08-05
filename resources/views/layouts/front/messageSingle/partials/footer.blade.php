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
<script src="{{asset('frontJs/messages/sendMessage.js')}}"></script>
</body>
</html>
