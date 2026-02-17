<?php

namespace Models;

/**
 * ChatStore - Handles message storage and retrieval
 * Uses APCu if available, falls back to JSON file storage
 */
class ChatStore
{
    private const MESSAGES_KEY = 'chat_messages';
    private const MAX_MESSAGES = 200;
    private const STORAGE_FILE = __DIR__ . '/../../storage/chat.json';

    private bool $useApcu;

    public function __construct()
    {
        $this->useApcu = function_exists('apcu_enabled') && apcu_enabled();

        // Ensure storage directory exists for file fallback
        if (!$this->useApcu) {
            $storageDir = dirname(self::STORAGE_FILE);
            if (!is_dir($storageDir)) {
                mkdir($storageDir, 0755, true);
            }
        }
    }

    /**
     * Add a new message
     */
    public function addMessage(string $user, string $text): array
    {
        $messages = $this->getMessages();

        // Create message with monotonic ID (microseconds)
        $message = [
            'id' => (int) (microtime(true) * 1000),
            'user' => $user,
            'text' => $text,
            'ts' => time()
        ];

        $messages[] = $message;

        // Keep only last MAX_MESSAGES
        if (count($messages) > self::MAX_MESSAGES) {
            $messages = array_slice($messages, -self::MAX_MESSAGES);
        }

        $this->saveMessages($messages);

        return $message;
    }

    /**
     * Get messages since a given ID
     */
    public function getMessagesSince(int $sinceId): array
    {
        $messages = $this->getMessages();

        if ($sinceId === 0) {
            return $messages;
        }

        return array_values(array_filter($messages, fn($m) => $m['id'] > $sinceId));
    }

    /**
     * Get all messages
     */
    public function getMessages(): array
    {
        if ($this->useApcu) {
            return apcu_fetch(self::MESSAGES_KEY) ?: [];
        }

        $data = $this->readJsonFile();
        return $data['messages'] ?? [];
    }

    /**
     * Save messages
     */
    private function saveMessages(array $messages): void
    {
        if ($this->useApcu) {
            apcu_store(self::MESSAGES_KEY, $messages);
            return;
        }

        $data = $this->readJsonFile();
        $data['messages'] = $messages;
        $this->writeJsonFile($data);
    }

    /**
     * Clear all messages
     */
    public function clearAll(): void
    {
        if ($this->useApcu) {
            apcu_delete(self::MESSAGES_KEY);
            return;
        }

        $data = $this->readJsonFile();
        $data['messages'] = [];
        $this->writeJsonFile($data);
    }

    /**
     * Read JSON storage file with locking
     */
    private function readJsonFile(): array
    {
        if (!file_exists(self::STORAGE_FILE)) {
            return ['users' => [], 'active_users' => [], 'messages' => []];
        }

        $fp = fopen(self::STORAGE_FILE, 'r');
        if (!$fp) {
            return ['users' => [], 'active_users' => [], 'messages' => []];
        }

        flock($fp, LOCK_SH);
        $content = stream_get_contents($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        return json_decode($content, true) ?: ['users' => [], 'active_users' => [], 'messages' => []];
    }

    /**
     * Write JSON storage file with locking
     */
    private function writeJsonFile(array $data): void
    {
        $fp = fopen(self::STORAGE_FILE, 'c+');
        if (!$fp) {
            return;
        }

        flock($fp, LOCK_EX);
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
