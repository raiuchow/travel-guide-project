<?php
/**
 * Authentication Service
 * SOLID: Single Responsibility - শুধুমাত্র authentication/authorization logic
 */

namespace App\Services;

class AuthService {

    /**
     * Start session if not already started
     */
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    /**
     * Check if user is a verified scout
     */
    public static function isVerifiedScout(): bool {
        self::startSession();
        return self::isLoggedIn() 
            && ($_SESSION['role'] ?? '') === 'scout' 
            && !empty($_SESSION['is_verified']);
    }

    /**
     * Require scout access (gate)
     */
    public static function requireScout(): void {
        if (!self::isLoggedIn()) {
            header('Location: /login.php');
            exit;
        }

        if (!self::isVerifiedScout()) {
            http_response_code(403);
            self::renderError('Access Denied', 'Only verified scouts can access this page.');
            exit;
        }
    }

    /**
     * Get current user data
     */
    public static function currentUser(): array {
        self::startSession();
        return [
            'id'          => $_SESSION['user_id']    ?? null,
            'name'        => $_SESSION['name']        ?? '',
            'email'       => $_SESSION['email']       ?? '',
            'role'        => $_SESSION['role']        ?? '',
            'is_verified' => $_SESSION['is_verified'] ?? 0,
        ];
    }

    /**
     * Login user
     */
    public static function login(array $user): void {
        self::startSession();
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['name']        = $user['name'];
        $_SESSION['email']       = $user['email'];
        $_SESSION['role']        = $user['role'];
        $_SESSION['is_verified'] = $user['is_verified'];
    }

    /**
     * Logout user
     */
    public static function logout(): void {
        self::startSession();
        
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }

    /**
     * Render error page
     */
    private static function renderError(string $title, string $message): void {
        echo "<!DOCTYPE html><html><head><title>{$title}</title>
        <style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;
        height:100vh;background:#0a0a0a;color:#fff;flex-direction:column;}
        h1{color:#e63946;}p{color:#aaa;}</style></head>
        <body><h1>{$title}</h1><p>{$message}</p>
        <a href='/login.php' style='color:#4ecdc4;margin-top:1rem;'>Go to Login</a></body></html>";
    }
}
