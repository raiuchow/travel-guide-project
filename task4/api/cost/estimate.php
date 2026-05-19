<?php
// api/cost/estimate.php
// GET /api/cost/estimate?post_id=N&travelers=N&days=N
// Returns JSON with estimated total cost.

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/CostEstimateModel.php';
require_once __DIR__ . '/../../models/PostModel.php';

header('Content-Type: application/json');

if (!isVerifiedAny()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorised.']);
    exit;
}

// ── Input validation ──────────────────────────────────────────────────────────
$postId   = (int) ($_GET['post_id']   ?? 0);
$travelers = (int) ($_GET['travelers'] ?? 1);
$days      = (int) ($_GET['days']      ?? 7);

$errors = [];

if ($postId <= 0)                       $errors[] = 'Invalid post.';
if ($travelers < 1 || $travelers > 50)  $errors[] = 'Travelers must be between 1 and 50.';
if ($days < 1 || $days > 365)           $errors[] = 'Days must be between 1 and 365.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}

// ── Fetch data ────────────────────────────────────────────────────────────────
$postModel = new PostModel();
$post = $postModel->getPostById($postId);

if (!$post) {
    http_response_code(404);
    echo json_encode(['error' => 'Post not found.']);
    exit;
}

$costModel = new CostEstimateModel();
$cost      = $costModel->getByPost($postId, $post['cost_level']);

// Formula: total = base_cost * travelers * (days / 7)
$baseCost   = $cost['base_cost'];
$total      = round($baseCost * $travelers * ($days / 7), 2);

echo json_encode([
    'base_cost'    => $baseCost,
    'currency'     => $cost['currency'],
    'travelers'    => $travelers,
    'days'         => $days,
    'total'        => $total,
    'source'       => $cost['source'],
    'last_updated' => $cost['last_updated'],
]);
