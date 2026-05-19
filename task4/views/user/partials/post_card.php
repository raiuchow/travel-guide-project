<?php
// views/user/partials/post_card.php
// Expects $p = post row array
$costBadgeClass = [
    'low'    => 'badge-low',
    'medium' => 'badge-medium',
    'high'   => 'badge-high',
][$p['cost_level']] ?? 'badge-medium';
?>
<article class="post-card" data-id="<?= (int) $p['id'] ?>">
  <div class="card-genre-tag"><?= htmlspecialchars(ucfirst($p['genre'])) ?></div>
  <div class="card-body">
    <h2 class="card-title">
      <a href="/post.php?id=<?= (int) $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></a>
    </h2>
    <p class="card-country">📍 <?= htmlspecialchars($p['country']) ?></p>
    <p class="card-excerpt"><?= htmlspecialchars(mb_substr(strip_tags($p['short_history']), 0, 120)) ?>…</p>
    <div class="card-footer">
      <span class="badge <?= $costBadgeClass ?>"><?= ucfirst($p['cost_level']) ?> Cost</span>
      <a href="/post.php?id=<?= (int) $p['id'] ?>" class="btn-read-more">Read More →</a>
    </div>
  </div>
</article>
