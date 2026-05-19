<?php
// api/comments/delete.php
// DELETE /api/comments/{id}  (routed via .htaccess or called as ?id=N)
// Allows a verified general user to delete their own comment.

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/CommentModel.php';

header('Content-Type: application/json');

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorised.']);
    exit;
}

$user = currentUser();

if ($user['role'] !== 'user' || empty($user['is_verified'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Only verified general users can delete comments.']);
    exit;
}

// ── Method guard ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

// ── CSRF: read from X-CSRF-Token header (sent by JS fetch) ───────────────────
$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verifyCsrf($csrfToken)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token.']);
    exit;
}

// ── Input ─────────────────────────────────────────────────────────────────────
// Support both /api/comments/{id} (via PATH_INFO) and ?id={id}
$pathParts = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$commentId = (int) (end($pathParts) ?: ($_GET['id'] ?? 0));

if ($commentId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid comment ID.']);
    exit;
}

// ── Delete ────────────────────────────────────────────────────────────────────
$model   = new CommentModel();
$deleted = $model->deleteComment($commentId, (int) $user['id']);

if (!$deleted) {
    http_response_code(404);
    echo json_encode(['error' => 'Comment not found or you do not own it.']);
    exit;
}

echo json_encode(['success' => true, 'deleted_id' => $commentId]);
