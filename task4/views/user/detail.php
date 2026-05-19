<?php
// views/user/detail.php
// Variables: $post, $comments, $cost, $user, $csrf
$pageTitle = htmlspecialchars($post['title']);
require __DIR__ . '/../partials/header.php';

$canComment = ($user['role'] === 'user' && !empty($user['is_verified']));
$costBadges = ['low' => '💚 Budget-Friendly', 'medium' => '💛 Moderate', 'high' => '🔴 Premium'];
?>

<main class="container detail-page">

  <!-- Breadcrumb -->
  <nav class="breadcrumb">
    <a href="/browse.php">Browse</a> <span>/</span>
    <span><?= htmlspecialchars($post['country']) ?></span> <span>/</span>
    <span><?= htmlspecialchars($post['title']) ?></span>
  </nav>

  <!-- Post Header -->
  <header class="post-header">
    <div class="post-meta-tags">
      <span class="tag-genre"><?= htmlspecialchars(ucfirst($post['genre'])) ?></span>
      <span class="tag-cost <?= $post['cost_level'] ?>">
        <?= $costBadges[$post['cost_level']] ?? 'Unknown Cost' ?>
      </span>
    </div>
    <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>
    <p class="post-country-info">
      🌍 <?= htmlspecialchars($post['country']) ?> &nbsp;|&nbsp;
      ✍ Submitted by <?= htmlspecialchars($post['scout_name']) ?> &nbsp;|&nbsp;
      📅 <?= date('M d, Y', strtotime($post['created_at'])) ?>
    </p>
  </header>

  <div class="post-layout">

    <!-- Main Content -->
    <article class="post-content">

      <?php if (!empty($post['image_path'])): ?>
        <img src="/public/uploads/posts/<?= htmlspecialchars($post['image_path']) ?>"
             alt="<?= htmlspecialchars($post['title']) ?>" class="post-image">
      <?php endif; ?>

      <section class="post-section">
        <h2>About This Destination</h2>
        <p><?= nl2br(htmlspecialchars($post['short_history'])) ?></p>
      </section>

      <?php if (!empty($post['travel_medium_info'])): ?>
        <section class="post-section">
          <h2>How To Get There</h2>
          <p><?= nl2br(htmlspecialchars($post['travel_medium_info'])) ?></p>
        </section>
      <?php endif; ?>

    </article>

    <!-- Sidebar -->
    <aside class="post-sidebar">

      <!-- Cost Calculator -->
      <div class="sidebar-card calculator-card">
        <h3 class="sidebar-title">💰 Cost Estimator</h3>
        <p class="base-cost-info">
          Base cost:
          <strong id="baseCostDisplay">
            <?= $cost['currency'] ?> <?= number_format($cost['base_cost'], 0) ?>
          </strong>
          <em>(per week, per person)</em>
        </p>

        <div class="calc-field">
          <label for="calcTravelers">Travelers</label>
          <input type="number" id="calcTravelers" min="1" max="50" value="1"
                 class="calc-input" data-error="travelers-err">
          <span class="field-error" id="travelers-err"></span>
        </div>
        <div class="calc-field">
          <label for="calcDays">Days</label>
          <input type="number" id="calcDays" min="1" max="365" value="7"
                 class="calc-input" data-error="days-err">
          <span class="field-error" id="days-err"></span>
        </div>

        <button id="calcBtn" class="btn-primary full-width"
                data-post-id="<?= (int) $post['id'] ?>">Calculate</button>

        <div id="calcResult" class="calc-result hidden">
          <p>Estimated Total</p>
          <p class="calc-total" id="calcTotal">—</p>
          <p class="calc-note" id="calcNote"></p>
        </div>
      </div>

      <!-- Quick Facts -->
      <div class="sidebar-card">
        <h3 class="sidebar-title">📋 Quick Facts</h3>
        <ul class="quick-facts">
          <li><span>Country</span><strong><?= htmlspecialchars($post['country']) ?></strong></li>
          <li><span>Genre</span><strong><?= htmlspecialchars(ucfirst($post['genre'])) ?></strong></li>
          <li><span>Cost Level</span><strong><?= ucfirst($post['cost_level']) ?></strong></li>
          <?php if (!empty($post['travel_medium_info'])): ?>
            <li><span>Travel Medium</span><strong><?= htmlspecialchars(mb_substr($post['travel_medium_info'], 0, 40)) ?></strong></li>
          <?php endif; ?>
        </ul>
      </div>

    </aside>
  </div>

  <!-- ── Comments Section ──────────────────────────────────────────────── -->
  <section class="comments-section" id="commentsSection">
    <h2 class="comments-title">
      Comments <span class="comment-count" id="commentCount">(<?= count($comments) ?>)</span>
    </h2>

    <!-- Comment Form (verified general users only) -->
    <?php if ($canComment): ?>
      <div class="comment-form-wrap">
        <h3>Leave a Comment</h3>
        <div id="commentFormError" class="alert alert-error hidden"></div>
        <form id="commentForm" novalidate>
          <input type="hidden" name="post_id"    value="<?= (int) $post['id'] ?>">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

          <div class="form-group">
            <label for="commenterName">Name</label>
            <input type="text" id="commenterName"
                   value="<?= htmlspecialchars($user['name']) ?>"
                   readonly class="input-readonly">
          </div>
          <div class="form-group">
            <label for="commentContent">Comment <span class="char-count" id="charCount">0/1000</span></label>
            <textarea id="commentContent" name="content"
                      rows="4" maxlength="1000"
                      placeholder="Share your experience or tips…"
                      required></textarea>
            <span class="field-error" id="content-err"></span>
          </div>
          <button type="submit" class="btn-primary" id="submitCommentBtn">Post Comment</button>
        </form>
      </div>
    <?php elseif (!$user['id']): ?>
      <p class="comment-gate"><a href="/login.php">Log in</a> to leave a comment.</p>
    <?php else: ?>
      <p class="comment-gate">Only verified general users can post comments.</p>
    <?php endif; ?>

    <!-- Comment List -->
    <div class="comment-list" id="commentList">
      <?php foreach ($comments as $c): ?>
        <?php include __DIR__ . '/partials/comment_item.php'; ?>
      <?php endforeach; ?>
      <?php if (empty($comments)): ?>
        <p class="empty-comments" id="emptyMsg">No comments yet. Be the first!</p>
      <?php endif; ?>
    </div>
  </section>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
<script>
  const CURRENT_USER_ID = <?= (int) ($user['id'] ?? 0) ?>;
  const CSRF_TOKEN      = <?= json_encode($csrf) ?>;
  const CAN_COMMENT     = <?= $canComment ? 'true' : 'false' ?>;
</script>
<script src="/public/js/detail.js"></script>
