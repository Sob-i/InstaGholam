document.addEventListener("DOMContentLoaded", () => {

    const sendBtn = document.querySelector(".send-btn");
    const messageInput = document.querySelector(".thread-input input");
    const messagesContainer = document.querySelector(".thread-messages");

    if (!sendBtn || !messageInput) return;

    sendBtn.addEventListener("click", sendMessage);

    messageInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            sendMessage();
        }
    });

    async function sendMessage() {
        const message = messageInput.value.trim();
        if (message === "") return;

        // ✅ Add message immediately (optimistic UI)
        const div = document.createElement("div");
        div.className = "message sent";
        div.innerHTML = `${message}<span class="time">Sending...</span>`;
        messagesContainer.appendChild(div);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Clear input
        messageInput.value = "";

        const formData = new FormData();
        formData.append("chat_id", data.chat_id);
        formData.append("sender_id", data.sender_id);
        formData.append("receiver_id", data.receiver_id);
        formData.append("message", message);
        formData.append("type", data.type);

        try {
            const response = await fetch(`/message/${data.sender_id}/send`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || '',
                    "Accept": "application/json",
                },
                body: formData,
            });

            const responseData = await response.json();

            if (responseData.success) {
                // ✅ Update the time to "Just now"
                const timeSpan = div.querySelector('.time');
                if (timeSpan) {
                    timeSpan.textContent = 'Just now';
                }
            } else {
                // ❌ Show error on the message
                div.style.border = '2px solid red';
                div.style.opacity = '0.7';
                const timeSpan = div.querySelector('.time');
                if (timeSpan) {
                    timeSpan.textContent = '❌ Failed';
                }
            }

        } catch (error) {
            // ❌ Show error on the message
            div.style.border = '2px solid red';
            div.style.opacity = '0.7';
            const timeSpan = div.querySelector('.time');
            if (timeSpan) {
                timeSpan.textContent = '❌ Error';
            }
            console.error(error);
        }
    }

});
