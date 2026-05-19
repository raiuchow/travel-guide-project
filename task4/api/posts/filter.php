<?php
// api/posts/filter.php
// GET /api/posts/filter?country=&genre[]=&cost_level=
// Returns JSON array of filtered approved posts.

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/PostModel.php';

header('Content-Type: application/json');

if (!isVerifiedAny()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorised']);
    exit;
}

// --- Input validation ---
$allowedCost    = ['', 'low', 'medium', 'high'];
$allowedGenres  = ['beach', 'mountain', 'city', 'historical', 'nature', 'adventure', 'cultural'];

$country   = trim($_GET['country']    ?? '');
$costLevel = trim($_GET['cost_level'] ?? '');
$rawGenres = $_GET['genre'] ?? [];

// Validate cost_level
if (!in_array($costLevel, $allowedCost, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid cost_level']);
    exit;
}

// Filter genres to allowed values only (prevent injection via IN clause key names)
$genres = [];
if (is_array($rawGenres)) {
    foreach ($rawGenres as $g) {
        $g = trim((string) $g);
        if (in_array($g, $allowedGenres, true)) {
            $genres[] = $g;
        }
    }
}

// Validate country length
if (mb_strlen($country) > 100) {
    http_response_code(400);
    echo json_encode(['error' => 'Country value too long']);
    exit;
}

$model = new PostModel();
$posts = $model->filterPosts($country, $genres, $costLevel);

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
