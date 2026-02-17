<?php

namespace Controllers;

use Core\Controller;
use Models\UserStore;
use Models\ChatStore;

/**
 * AuthController - Handles login/logout
 */
class AuthController extends Controller
{
    private UserStore $userStore;
    private ChatStore $chatStore;

    public function __construct()
    {
        $this->userStore = new UserStore();
        $this->chatStore = new ChatStore();
    }

    /**
     * Show login page
     */
    public function showLogin(): void
    {
        // If already logged in, redirect to chat
        if ($this->isLoggedIn()) {
            $this->redirect('/chat');
            return;
        }

        $this->view('auth/login', [
            'error' => $_GET['error'] ?? null
        ]);
    }

    /**
     * Handle login/register
     */
    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate input
        if (empty($username) || empty($password)) {
            $this->redirect('/?error=' . urlencode('Username and password are required'));
            return;
        }

        if (strlen($username) > 50) {
            $this->redirect('/?error=' . urlencode('Username too long'));
            return;
        }

        // Attempt authentication
        if (!$this->userStore->authenticate($username, $password)) {
            $this->redirect('/?error=' . urlencode('Invalid password for existing user'));
            return;
        }

        // Set session
        $_SESSION['user'] = $username;

        $this->redirect('/chat');
    }

    /**
     * Handle logout
     */
    public function logout(): void
    {
        $username = $this->currentUser();

        if ($username) {
            // Remove from active users and get remaining count
            $remainingUsers = $this->userStore->logout($username);

            // If no users left, clear all messages
            if ($remainingUsers === 0) {
                $this->chatStore->clearAll();
            }
        }

        // Destroy session
        session_destroy();

        $this->redirect('/');
    }
}
