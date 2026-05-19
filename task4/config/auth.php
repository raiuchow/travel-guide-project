<?php
// config/auth.php

function requireAuth(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
}

function requireVerifiedUser(): void {
    requireAuth();
    if ($_SESSION['role'] !== 'user' || empty($_SESSION['is_verified'])) {
        header('Location: /index.php?error=access_denied');
        exit;
    }
}

function isLoggedIn(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return !empty($_SESSION['user_id']);
}

function isVerifiedUser(): bool {
    return isLoggedIn()
        && $_SESSION['role'] === 'user'
        && !empty($_SESSION['is_verified']);
}

function isVerifiedAny(): bool {
    return isLoggedIn() && !empty($_SESSION['is_verified']);
}

function currentUser(): array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return [
        'id'          => $_SESSION['user_id']   ?? null,
        'name'        => $_SESSION['name']       ?? '',
        'role'        => $_SESSION['role']       ?? '',
        'is_verified' => $_SESSION['is_verified'] ?? 0,
    ];
}

/**
 * Basic CSRF token generation & verification.
 */
function csrfToken(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
