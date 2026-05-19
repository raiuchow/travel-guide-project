<?php
/**
 * Approved Posts Page
 * View approved posts (read-only from posts table)
 */

require_once __DIR__ . '/../bootstrap/autoload.php';
require_once __DIR__ . '/../bootstrap/helpers.php';

use App\Models\Post;

requireScout();

$user = currentUser();
$postModel = new Post(db());
$posts = $postModel->getApprovedByScout($user['id']);

$pageTitle = 'Approved Posts';

require __DIR__ . '/../views/layouts/scout_header.php';
?>

<div class="card">
  <div class="card-title"><span class="dot"></span> Your Approved Posts</div>

  <?php if (empty($posts)): ?>
    <div class="empty-state">
      <div class="icon">🌍</div>
      <p>None of your submissions have been approved yet.</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th><th>Title</th><th>Country</th><th>Genre</th>
            <th>Cost</th><th>Approved On</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $post): ?>
          <tr>
            <td style="color:var(--muted);font-size:.8rem;">#<?= $post['id'] ?></td>
            <td><?= e($post['title']) ?></td>
            <td><?= e($post['country']) ?></td>
            <td><?= e(ucfirst($post['genre'])) ?></td>
            <td><?= e(ucfirst($post['cost_level'])) ?></td>
            <td style="color:var(--muted);font-size:.8rem;">
              <?= date('d M Y', strtotime($post['updated_at'])) ?>
            </td>
            <td>
              <a href="create_request.php?post_id=<?= $post['id'] ?>"
                 class="btn btn-warning" style="font-size:.78rem;padding:.4rem .8rem;">
                📝 Request Changes
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="card" style="background:rgba(0,212,170,0.04);border-color:rgba(0,212,170,0.2);">
  <div style="display:flex;gap:1rem;align-items:flex-start;">
    <span style="font-size:1.4rem;">💡</span>
    <div>
      <div style="font-family:var(--font-head);font-weight:700;font-size:.9rem;margin-bottom:.25rem;">
        How Change Requests Work
      </div>
      <p style="font-size:.83rem;color:var(--muted);line-height:1.7;">
        Click <strong style="color:var(--text);">Request Changes</strong> on any approved post to open a 
        pre-filled form. Your submission creates a new post_request record with the 
        original post ID attached. An admin will review and apply changes if approved.
      </p>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../views/layouts/scout_footer.php'; ?>
