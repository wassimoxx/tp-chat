<?php

namespace Controllers;

use Core\Controller;
use Core\View;
use Models\ChatStore;

/**
 * ChatController - Handles chat room and messaging
 */
class ChatController extends Controller
{
    private ChatStore $chatStore;

    public function __construct()
    {
        $this->chatStore = new ChatStore();
    }

    /**
     * Show chat room
     */
    public function showRoom(): void
    {
        $this->requireAuth();

        $this->view('chat/room', [
            'username' => $this->currentUser(),
            'messages' => $this->chatStore->getMessages()
        ]);
    }

    /**
     * Send a message
     */
    public function send(): void
    {
        $this->requireAuth();

        $text = trim($_POST['message'] ?? '');

        if (empty($text)) {
            $this->json(['error' => 'Message cannot be empty'], 400);
            return;
        }

        // Limit message length
        if (strlen($text) > 1000) {
            $text = substr($text, 0, 1000);
        }

        $message = $this->chatStore->addMessage($this->currentUser(), $text);

        $this->json([
            'success' => true,
            'message' => [
                'id' => $message['id'],
                'user' => View::escape($message['user']),
                'text' => View::escape($message['text']),
                'ts' => $message['ts']
            ]
        ]);
    }

    /**
     * Poll for new messages
     */
    public function poll(): void
    {
        $this->requireAuth();

        $sinceId = (int) ($_GET['since'] ?? 0);

        $messages = $this->chatStore->getMessagesSince($sinceId);

        // Escape output for each message
        $escaped = array_map(function ($m) {
            return [
                'id' => $m['id'],
                'user' => View::escape($m['user']),
                'text' => View::escape($m['text']),
                'ts' => $m['ts']
            ];
        }, $messages);

        $this->json($escaped);
    }
}
