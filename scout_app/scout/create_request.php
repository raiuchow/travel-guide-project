<?php
/**
 * Create Post Request Page
 * Form to submit new post request or change request for approved post
 */

require_once __DIR__ . '/../bootstrap/autoload.php';
require_once __DIR__ . '/../bootstrap/helpers.php';

use App\Models\PostRequest;
use App\Services\ValidationService;
use App\Services\ImageUploadService;

requireScout();

$user = currentUser();
$postRequestModel = new PostRequest(db());
$errors = [];
$success = '';

// ── POST Handler ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $validator = new ValidationService();
    $validator->required('title', $_POST['title'] ?? '')
              ->required('short_history', $_POST['short_history'] ?? '')
              ->required('country', $_POST['country'] ?? '')
              ->required('genre', $_POST['genre'] ?? '')
              ->required('cost_level', $_POST['cost_level'] ?? '')
              ->required('travel_medium_info', $_POST['travel_medium_info'] ?? '')
              ->inList('genre', $_POST['genre'] ?? '', ['beach','mountain','city','historical','cultural','adventure','other'])
              ->inList('cost_level', $_POST['cost_level'] ?? '', ['low','medium','high'])
              ->maxLength('title', $_POST['title'] ?? '', 150);

    if (!$validator->isValid()) {
        $errors = $validator->getErrors();
    } else {
        // Optional image upload
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            try {
                $uploader = new ImageUploadService();
                $imagePath = $uploader->upload($_FILES['image']);
            } catch (RuntimeException $e) {
                $errors['image'] = $e->getMessage();
            }
        }

        if (empty($errors)) {
            $postData = [
                'title'              => sanitize($_POST['title']),
                'short_history'      => sanitize($_POST['short_history']),
                'country'            => sanitize($_POST['country']),
                'genre'              => $_POST['genre'],
                'cost_level'         => $_POST['cost_level'],
                'travel_medium_info' => sanitize($_POST['travel_medium_info']),
                'image'              => $imagePath,
            ];

            $originalPostId = !empty($_POST['original_post_id']) && is_numeric($_POST['original_post_id'])
                ? (int)$_POST['original_post_id']
                : null;

            $postRequestModel->create($user['id'], $postData, $originalPostId);

            flash('success', 'Your request has been submitted and is pending admin review.');
            redirect('/scout/my_requests.php');
        }
    }
}

$originalPostId = (int)($_GET['post_id'] ?? 0);
$pageTitle = $originalPostId ? 'Request Changes' : 'New Post Request';

require __DIR__ . '/../views/layouts/scout_header.php';
?>

<?php if ($success): ?>
  <div class="alert alert-success">✓ <?= e($success) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-title"><span class="dot"></span>
    <?= $originalPostId ? 'Request Changes for Post #'.$originalPostId : 'Submit New Place Request' ?>
  </div>

  <?php if (!empty($errors)): ?>
    <?php foreach ($errors as $error): ?>
      <div class="alert alert-error">⚠ <?= e($error) ?></div>
    <?php endforeach; ?>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" id="createForm" novalidate>
    <?php if ($originalPostId): ?>
      <input type="hidden" name="original_post_id" value="<?= $originalPostId ?>">
    <?php endif; ?>

    <div class="form-grid">
      <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" id="title" name="title" maxlength="150"
               value="<?= e($_POST['title'] ?? '') ?>"
               placeholder="e.g., Mount Fuji Sunrise Trail" required>
      </div>

      <div class="form-group">
        <label for="country">Country / Cultural Representation *</label>
        <input type="text" id="country" name="country"
               value="<?= e($_POST['country'] ?? '') ?>"
               placeholder="e.g., Japan – Shinto Heritage Site" required>
      </div>

      <div class="form-group">
        <label for="genre">Genre *</label>
        <select id="genre" name="genre" required>
          <option value="">— Select genre —</option>
          <?php foreach (['beach','mountain','city','historical','cultural','adventure','other'] as $g): ?>
            <option value="<?= $g ?>" <?= ($_POST['genre'] ?? '') === $g ? 'selected':'' ?>>
              <?= ucfirst($g) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="cost_level">Cost Level *</label>
        <select id="cost_level" name="cost_level" required>
          <option value="">— Select cost —</option>
          <?php foreach (['low'=>'💚 Low','medium'=>'🟡 Medium','high'=>'🔴 High'] as $v => $l): ?>
            <option value="<?= $v ?>" <?= ($_POST['cost_level'] ?? '') === $v ? 'selected':'' ?>>
              <?= $l ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group span2">
        <label for="travel_medium_info">Travel Medium Info *</label>
        <input type="text" id="travel_medium_info" name="travel_medium_info"
               value="<?= e($_POST['travel_medium_info'] ?? '') ?>"
               placeholder="e.g., Flight to Tokyo + 2h bus to Kawaguchiko" required>
      </div>

      <div class="form-group span2">
        <label for="short_history">Short History / Description *</label>
        <textarea id="short_history" name="short_history" required
                  placeholder="Describe the place, its cultural significance..."><?= e($_POST['short_history'] ?? '') ?></textarea>
      </div>

      <div class="form-group span2">
        <label for="image">Cover Image (optional, max 5 MB)</label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
        <span class="hint">JPEG, PNG, WebP or GIF · Max 5 MB</span>
        <img id="thumbPreview" class="thumb-preview" src="" alt="Preview">
      </div>
    </div>

    <div style="margin-top:1.25rem; display:flex; gap:.75rem;">
      <button type="submit" class="btn btn-primary">🚀 Submit Request</button>
      <a href="my_requests.php" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>

<script>
document.getElementById('createForm').addEventListener('submit', function(e) {
  const required = ['title','country','genre','cost_level','travel_medium_info','short_history'];
  let valid = true;
  
  required.forEach(name => {
    const el = document.querySelector(`[name="${name}"]`);
    if (!el || !el.value.trim()) {
      el && (el.style.borderColor = 'var(--danger)');
      valid = false;
    } else {
      el && (el.style.borderColor = '');
    }
  });
  
  if (!valid) {
    e.preventDefault();
    alert('Please fill in all required fields.');
    return;
  }
  
  const img = document.getElementById('image');
  if (img.files.length > 0 && img.files[0].size > 5 * 1024 * 1024) {
    e.preventDefault();
    alert('Image must be under 5 MB.');
  }
});

document.getElementById('image').addEventListener('change', function() {
  const preview = document.getElementById('thumbPreview');
  if (this.files && this.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(this.files[0]);
  }
});
</script>

<?php require __DIR__ . '/../views/layouts/scout_footer.php'; ?>
