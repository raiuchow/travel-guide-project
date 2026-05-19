<?php
// api/comments/add.php
// POST /api/comments/add
// Requires verified general user. Returns the new comment as JSON.

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/CommentModel.php';

header('Content-Type: application/json');

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'You must be logged in to comment.']);
    exit;
}

$user = currentUser();

if ($user['role'] !== 'user' || empty($user['is_verified'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Only verified general users can post comments.']);
    exit;
}

// ── Method guard ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

// ── CSRF check ────────────────────────────────────────────────────────────────
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCsrf($csrfToken)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token.']);
    exit;
}

// ── Input validation ──────────────────────────────────────────────────────────
$postId  = (int) ($_POST['post_id'] ?? 0);
$content = trim($_POST['content']  ?? '');

$errors = [];

if ($postId <= 0) {
    $errors[] = 'Invalid post.';
}

if ($content === '') {
    $errors[] = 'Comment cannot be empty.';
} elseif (mb_strlen($content) > 1000) {
    $errors[] = 'Comment must be 1000 characters or fewer.';
}

// Basic XSS protection — strip tags, we'll htmlspecialchars on output
$content = strip_tags($content);

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}

// ── Persist ───────────────────────────────────────────────────────────────────
$model   = new CommentModel();
$comment = $model->addComment($postId, (int) $user['id'], $content);

if (!$comment) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save comment. Please try again.']);
    exit;
}

echo json_encode([
    'success'       => true,
    'id'            => $comment['id'],
    'content'       => htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8'),
    'reviewer_name' => htmlspecialchars($comment['reviewer_name'], ENT_QUOTES, 'UTF-8'),
    'created_at'    => $comment['created_at'],
    'user_id'       => $comment['user_id'],
]);
