<?php
// auth.php
session_start();

// Adjust path if db.php in different folder
require_once __DIR__ . '/db.php';

/**
 * Log user in by setting session variables.
 * Use after successful password verification.
 */
function login_user($user)
{
    // Regenerate session id on login to prevent fixation
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
}

/**
 * Check if user is logged in.
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Require login and redirect to login.php if not logged in.
 */
function require_login()
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Require a specific role (e.g., 'admin') to access a page.
 * Redirects to index.php if role insufficient.
 */
function require_role($role)
{
    require_login();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        // Optionally show message or log attempt
        header('Location: index.php');
        exit;
    }
}

/**
 * Log out the current user.
 */
function logout_user()
{
    // Unset all session variables
    $_SESSION = [];
    // Destroy session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
}
