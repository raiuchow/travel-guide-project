<?php
// views/user/partials/comment_item.php
// Expects $c = comment row, $user = current user
?>
<div class="comment-item" id="comment-<?= (int) $c['id'] ?>">
  <div class="comment-avatar">
    <?= strtoupper(mb_substr($c['reviewer_name'], 0, 1)) ?>
  </div>
  <div class="comment-body">
    <div class="comment-header">
      <strong class="comment-author"><?= htmlspecialchars($c['reviewer_name']) ?></strong>
      <span class="comment-date"><?= date('M d, Y · H:i', strtotime($c['created_at'])) ?></span>
      <?php if (isset($user) && (int)$user['id'] === (int)$c['user_id']): ?>
        <button class="btn-delete-comment"
                data-id="<?= (int) $c['id'] ?>"
                title="Delete your comment">✕</button>
      <?php endif; ?>
    </div>
    <p class="comment-text"><?= nl2br(htmlspecialchars($c['content'])) ?></p>
  </div>
</div>
