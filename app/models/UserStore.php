<?php

namespace Models;

/**
 * UserStore - Handles user registration, login, and active user tracking
 * Uses APCu if available, falls back to JSON file storage
 */
class UserStore
{
    private const USERS_KEY = 'chat_users';
    private const ACTIVE_KEY = 'chat_active_users';
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
     * Register or login a user
     * Returns: true on success, false if password doesn't match existing user
     */
    public function authenticate(string $username, string $password): bool
    {
        $users = $this->getUsers();

        if (isset($users[$username])) {
            // User exists, verify password
            if (!password_verify($password, $users[$username])) {
                return false;
            }
        } else {
            // New user, register with hashed password
            $users[$username] = password_hash($password, PASSWORD_DEFAULT);
            $this->saveUsers($users);
        }

        // Add to active users
        $this->addActiveUser($username);

        return true;
    }

    /**
     * Logout a user and return remaining active user count
     */
    public function logout(string $username): int
    {
        $this->removeActiveUser($username);
        return $this->getActiveUserCount();
    }

    /**
     * Get count of active users
     */
    public function getActiveUserCount(): int
    {
        return count($this->getActiveUsers());
    }

    /**
     * Get all registered users
     */
    private function getUsers(): array
    {
        if ($this->useApcu) {
            return apcu_fetch(self::USERS_KEY) ?: [];
        }

        $data = $this->readJsonFile();
        return $data['users'] ?? [];
    }

    /**
     * Save users data
     */
    private function saveUsers(array $users): void
    {
        if ($this->useApcu) {
            apcu_store(self::USERS_KEY, $users);
            return;
        }

        $data = $this->readJsonFile();
        $data['users'] = $users;
        $this->writeJsonFile($data);
    }

    /**
     * Get active users list
     */
    private function getActiveUsers(): array
    {
        if ($this->useApcu) {
            return apcu_fetch(self::ACTIVE_KEY) ?: [];
        }

        $data = $this->readJsonFile();
        return $data['active_users'] ?? [];
    }

    /**
     * Add user to active list
     */
    private function addActiveUser(string $username): void
    {
        $active = $this->getActiveUsers();
        if (!in_array($username, $active)) {
            $active[] = $username;
        }

        if ($this->useApcu) {
            apcu_store(self::ACTIVE_KEY, $active);
            return;
        }

        $data = $this->readJsonFile();
        $data['active_users'] = $active;
        $this->writeJsonFile($data);
    }

    /**
     * Remove user from active list
     */
    private function removeActiveUser(string $username): void
    {
        $active = $this->getActiveUsers();
        $active = array_values(array_filter($active, fn($u) => $u !== $username));

        if ($this->useApcu) {
            apcu_store(self::ACTIVE_KEY, $active);
            return;
        }

        $data = $this->readJsonFile();
        $data['active_users'] = $active;
        $this->writeJsonFile($data);
    }

    /**
     * Clear all users (used when resetting)
     */
    public function clearAll(): void
    {
        if ($this->useApcu) {
            apcu_delete(self::USERS_KEY);
            apcu_delete(self::ACTIVE_KEY);
            return;
        }

        $this->writeJsonFile(['users' => [], 'active_users' => [], 'messages' => []]);
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

    /**
     * Check if using APCu
     */
    public function isUsingApcu(): bool
    {
        return $this->useApcu;
    }
}
