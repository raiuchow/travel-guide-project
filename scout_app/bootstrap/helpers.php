<?php
/**
 * Helper Functions
 * Global utility functions
 */

use Config\Database;

/**
 * Get database connection
 */
function db(): PDO {
    return Database::getInstance()->getConnection();
}

/**
 * Escape HTML output
 */
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to URL
 */
function redirect(string $url, int $statusCode = 302): void {
    header("Location: {$url}", true, $statusCode);
    exit;
}

/**
 * Get current user
 */
function currentUser(): array {
    return \App\Services\AuthService::currentUser();
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return \App\Services\AuthService::isLoggedIn();
}

/**
 * Require scout authentication
 */
function requireScout(): void {
    \App\Services\AuthService::requireScout();
}

/**
 * Generate CSRF token
 */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get old input value (for form repopulation)
 */
function old(string $key, mixed $default = ''): mixed {
    return $_SESSION['old'][$key] ?? $default;
}

/**
 * Flash message to session
 */
function flash(string $key, mixed $value): void {
    $_SESSION['flash'][$key] = $value;
}

/**
 * Get and clear flash message
 */
function getFlash(string $key, mixed $default = null): mixed {
    $value = $_SESSION['flash'][$key] ?? $default;
    unset($_SESSION['flash'][$key]);
    return $value;
}

/**
 * Check if flash message exists
 */
function hasFlash(string $key): bool {
    return isset($_SESSION['flash'][$key]);
}

/**
 * Sanitize input
 */
function sanitize(mixed $value): mixed {
    if (is_array($value)) {
        return array_map('sanitize', $value);
    }
    return is_string($value) ? trim(strip_tags($value)) : $value;
}

/**
 * Get base URL
 */
function baseUrl(string $path = ''): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $protocol . '://' . $host . '/' . ltrim($path, '/');
}

/**
 * Asset URL helper
 */
function asset(string $path): string {
    return baseUrl($path);
}
