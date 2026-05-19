<?php
// api/posts/search.php
// GET /api/posts/search?q=keyword
// Returns JSON array of matching approved posts.

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/PostModel.php';

header('Content-Type: application/json');

if (!isVerifiedAny()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorised']);
    exit;
}

$q = trim($_GET['q'] ?? '');

if ($q === '') {
    echo json_encode([]);
    exit;
}

// Sanitise: reject overly long queries.
if (mb_strlen($q) > 100) {
    http_response_code(400);
    echo json_encode(['error' => 'Query too long']);
    exit;
}

$model  = new PostModel();
$posts  = $model->searchPosts($q);

// Truncate history snippet to 120 chars for card display.
$result = array_map(function (array $p): array {
    return [
        'id'           => (int) $p['id'],
        'title'        => htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'),
        'country'      => htmlspecialchars($p['country'], ENT_QUOTES, 'UTF-8'),
        'genre'        => htmlspecialchars($p['genre'], ENT_QUOTES, 'UTF-8'),
        'cost_level'   => $p['cost_level'],
        'short_history'=> mb_substr(strip_tags($p['short_history']), 0, 120) . '…',
    ];
}, $posts);

echo json_encode($result);
