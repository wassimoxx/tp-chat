<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Chat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header h1 {
            color: #fff;
            font-size: 20px;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .username {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }

        .username span {
            color: #3498db;
            font-weight: 600;
        }

        .logout-btn {
            padding: 8px 16px;
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(231, 76, 60, 0.3);
        }

        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .messages-area {
            flex: 1;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 20px;
            overflow-y: auto;
            margin-bottom: 20px;
            min-height: 400px;
            max-height: calc(100vh - 220px);
        }

        .message {
            margin-bottom: 15px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.own {
            text-align: right;
        }

        .message-bubble {
            display: inline-block;
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.08);
            text-align: left;
        }

        .message.own .message-bubble {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }

        .message-header {
            display: flex;
            gap: 10px;
            align-items: baseline;
            margin-bottom: 5px;
        }

        .message-user {
            color: #3498db;
            font-weight: 600;
            font-size: 13px;
        }

        .message.own .message-user {
            color: rgba(255, 255, 255, 0.9);
        }

        .message-time {
            color: rgba(255, 255, 255, 0.4);
            font-size: 11px;
        }

        .message-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .input-area {
            display: flex;
            gap: 12px;
        }

        .message-input {
            flex: 1;
            padding: 14px 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .message-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .message-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .send-btn {
            padding: 14px 28px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: #fff;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .send-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(52, 152, 219, 0.4);
        }

        .send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.4);
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <header class="header">
        <h1>💬 Group Chat</h1>
        <div class="user-info">
            <span class="username">Logged in as <span>
                    <?= \Core\View::escape($username) ?>
                </span></span>
            <form method="POST" action="<?= BASE_URL ?>/logout" style="display: inline;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>

    <div class="chat-container">
        <div class="messages-area" id="messages">
            <?php if (empty($messages)): ?>
                <div class="empty-state" id="empty-state">
                    <div class="empty-state-icon">💭</div>
                    <p>No messages yet. Start the conversation!</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message <?= $msg['user'] === $username ? 'own' : '' ?>">
                        <div class="message-bubble">
                            <div class="message-header">
                                <span class="message-user">
                                    <?= \Core\View::escape($msg['user']) ?>
                                </span>
                                <span class="message-time">
                                    <?= date('H:i', $msg['ts']) ?>
                                </span>
                            </div>
                            <div class="message-text">
                                <?= \Core\View::escape($msg['text']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="input-area">
            <input type="text" id="messageInput" class="message-input" placeholder="Type your message..."
                maxlength="1000" autocomplete="off">
            <button id="sendBtn" class="send-btn">Send</button>
        </div>
    </div>

    <script>
        const currentUser = <?= json_encode($username) ?>;
        const baseUrl = <?= json_encode(BASE_URL) ?>;
        const messagesContainer = document.getElementById('messages');
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');

        // Track last message ID for polling
        let lastMessageId = <?= !empty($messages) ? end($messages)['id'] : 0 ?>;

        // Format time from timestamp
        function formatTime(ts) {
            const date = new Date(ts * 1000);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        // Create message HTML
        function createMessageHtml(msg) {
            const isOwn = msg.user === currentUser;
            return `
                <div class="message ${isOwn ? 'own' : ''}">
                    <div class="message-bubble">
                        <div class="message-header">
                            <span class="message-user">${msg.user}</span>
                            <span class="message-time">${formatTime(msg.ts)}</span>
                        </div>
                        <div class="message-text">${msg.text}</div>
                    </div>
                </div>
            `;
        }

        // Append messages to container
        function appendMessages(messages) {
            if (messages.length === 0) return;

            // Remove empty state if present
            const emptyState = document.getElementById('empty-state');
            if (emptyState) {
                emptyState.remove();
            }

            messages.forEach(msg => {
                messagesContainer.insertAdjacentHTML('beforeend', createMessageHtml(msg));
                if (msg.id > lastMessageId) {
                    lastMessageId = msg.id;
                }
            });

            // Auto-scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Send message
        async function sendMessage() {
            const text = messageInput.value.trim();
            if (!text) return;

            sendBtn.disabled = true;
            messageInput.disabled = true;

            try {
                const response = await fetch(`${baseUrl}/chat/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `message=${encodeURIComponent(text)}`
                });

                const data = await response.json();

                if (data.success) {
                    messageInput.value = '';
                    // Message will appear via polling, but add it immediately for responsiveness
                    appendMessages([data.message]);
                } else {
                    console.error('Failed to send:', data.error);
                }
            } catch (error) {
                console.error('Send error:', error);
            } finally {
                sendBtn.disabled = false;
                messageInput.disabled = false;
                messageInput.focus();
            }
        }

        // Poll for new messages
        async function pollMessages() {
            try {
                const response = await fetch(`${baseUrl}/chat/poll?since=${lastMessageId}`);

                // Check if redirected to login (session expired)
                if (response.redirected) {
                    window.location.href = baseUrl + '/';
                    return;
                }

                const messages = await response.json();

                // Filter out messages we already have (from our own sends)
                const newMessages = messages.filter(msg => msg.id > lastMessageId);
                appendMessages(newMessages);
            } catch (error) {
                console.error('Poll error:', error);
            }
        }

        // Event listeners
        sendBtn.addEventListener('click', sendMessage);

        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Auto-scroll on initial load
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Start polling
        setInterval(pollMessages, 1000);

        // Focus input on load
        messageInput.focus();
    </script>
</body>

</html>