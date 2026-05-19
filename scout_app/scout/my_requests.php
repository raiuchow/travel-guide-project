<?php
/**
 * My Requests Page
 * List, Edit (AJAX), Delete (AJAX) post requests
 */

require_once __DIR__ . '/../bootstrap/autoload.php';
require_once __DIR__ . '/../bootstrap/helpers.php';

use App\Models\PostRequest;
use App\Services\ValidationService;

requireScout();

$user = currentUser();
$postRequestModel = new PostRequest(db());

// ── AJAX Edit Handler ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)($_POST['request_id'] ?? 0);
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']);

    $validator = new ValidationService();
    $validator->required('title', $_POST['title'] ?? '')
              ->required('short_history', $_POST['short_history'] ?? '')
              ->required('country', $_POST['country'] ?? '')
              ->required('genre', $_POST['genre'] ?? '')
              ->required('cost_level', $_POST['cost_level'] ?? '')
              ->required('travel_medium_info', $_POST['travel_medium_info'] ?? '')
              ->inList('genre', $_POST['genre'] ?? '', ['beach','mountain','city','historical','cultural','adventure','other'])
              ->inList('cost_level', $_POST['cost_level'] ?? '', ['low','medium','high']);

    if (!$validator->isValid()) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $validator->firstError()]);
            exit;
        }
        flash('error', $validator->firstError());
    } else {
        $postData = [
            'title'              => sanitize($_POST['title']),
            'short_history'      => sanitize($_POST['short_history']),
            'country'            => sanitize($_POST['country']),
            'genre'              => $_POST['genre'],
            'cost_level'         => $_POST['cost_level'],
            'travel_medium_info' => sanitize($_POST['travel_medium_info']),
        ];

        // Keep existing image
        $existing = $postRequestModel->getByIdForScout($id, $user['id']);
        $postData['image'] = $existing['post_data']['image'] ?? null;

        $updated = $postRequestModel->update($id, $user['id'], $postData);
        
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => $updated]);
            exit;
        }
        
        flash($updated ? 'success' : 'error', $updated ? 'Request updated.' : 'Update failed.');
    }
}

// ── AJAX Delete Handler ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents('php://input'), $body);
    $id = (int)($body['request_id'] ?? 0);
    $deleted = $postRequestModel->delete($id, $user['id']);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $deleted]);
    exit;
}

$requests = $postRequestModel->getAllByScout($user['id']);
$pageTitle = 'My Requests';

require __DIR__ . '/../views/layouts/scout_header.php';
?>

<?php if (hasFlash('success')): ?>
  <div class="alert alert-success">✓ <?= e(getFlash('success')) ?></div>
<?php endif; ?>

<?php if (hasFlash('error')): ?>
  <div class="alert alert-error">⚠ <?= e(getFlash('error')) ?></div>
<?php endif; ?>

<div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
  <a href="create_request.php" class="btn btn-primary">✚ New Request</a>
</div>

<div class="card">
  <div class="card-title"><span class="dot"></span> My Post Requests</div>

  <?php if (empty($requests)): ?>
    <div class="empty-state">
      <div class="icon">📭</div>
      <p>You haven't submitted any requests yet.</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th><th>Title</th><th>Genre</th><th>Cost</th>
            <th>Country</th><th>Status</th><th>Submitted</th><th>Actions</th>
          </tr>
        </thead>
        <tbody id="requestsTable">
          <?php foreach ($requests as $req):
            $pd = $req['post_data'];
            $isPending = $req['status'] === 'pending';
          ?>
          <tr id="row-<?= $req['id'] ?>">
            <td style="color:var(--muted);font-size:.8rem;">#<?= $req['id'] ?></td>
            <td><?= e($pd['title'] ?? '—') ?></td>
            <td><?= e(ucfirst($pd['genre'] ?? '—')) ?></td>
            <td><?= e(ucfirst($pd['cost_level'] ?? '—')) ?></td>
            <td><?= e(substr($pd['country'] ?? '—', 0, 30)) ?></td>
            <td><span class="status status-<?= $req['status'] ?>"><?= ucfirst($req['status']) ?></span></td>
            <td style="color:var(--muted);font-size:.8rem;"><?= date('d M Y', strtotime($req['requested_at'])) ?></td>
            <td>
              <div class="actions">
                <?php if ($isPending): ?>
                  <button class="btn btn-ghost" style="font-size:.78rem;padding:.4rem .8rem;"
                    onclick="openEdit(<?= $req['id'] ?>, <?= e(json_encode($pd), ENT_QUOTES) ?>)">
                    ✎ Edit
                  </button>
                  <button class="btn btn-danger" style="font-size:.78rem;padding:.4rem .8rem;"
                    onclick="confirmDelete(<?= $req['id'] ?>)">
                    🗑 Delete
                  </button>
                <?php else: ?>
                  <span style="color:var(--muted);font-size:.78rem;">—</span>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-title">✎ Edit Request</div>
    <div id="editAlert" style="display:none;" class="alert alert-error"></div>

    <form id="editForm">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="request_id" id="editId">
      <div class="form-grid">
        <div class="form-group">
          <label>Title *</label>
          <input type="text" name="title" id="editTitle" maxlength="150" required>
        </div>
        <div class="form-group">
          <label>Country *</label>
          <input type="text" name="country" id="editCountry" required>
        </div>
        <div class="form-group">
          <label>Genre *</label>
          <select name="genre" id="editGenre" required>
            <?php foreach (['beach','mountain','city','historical','cultural','adventure','other'] as $g): ?>
              <option value="<?= $g ?>"><?= ucfirst($g) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Cost Level *</label>
          <select name="cost_level" id="editCostLevel" required>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
          </select>
        </div>
        <div class="form-group span2">
          <label>Travel Medium *</label>
          <input type="text" name="travel_medium_info" id="editTravel" required>
        </div>
        <div class="form-group span2">
          <label>Short History *</label>
          <textarea name="short_history" id="editHistory" required></textarea>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" onclick="closeEdit()">Cancel</button>
        <button type="submit" class="btn btn-primary" id="editSubmitBtn">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal">
    <div class="modal-title">🗑 Confirm Deletion</div>
    <p style="color:var(--muted);font-size:.9rem;">
      This will permanently delete the request. This action cannot be undone.
    </p>
    <div class="modal-actions">
      <button class="btn btn-ghost" onclick="closeDelete()">Cancel</button>
      <button class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
    </div>
  </div>
</div>

<script>
let currentDeleteId = null;

function openEdit(id, data) {
  document.getElementById('editId').value = id;
  document.getElementById('editTitle').value = data.title || '';
  document.getElementById('editCountry').value = data.country || '';
  document.getElementById('editGenre').value = data.genre || '';
  document.getElementById('editCostLevel').value = data.cost_level || '';
  document.getElementById('editTravel').value = data.travel_medium_info || '';
  document.getElementById('editHistory').value = data.short_history || '';
  document.getElementById('editAlert').style.display = 'none';
  document.getElementById('editModal').classList.add('active');
}

function closeEdit() {
  document.getElementById('editModal').classList.remove('active');
}

document.getElementById('editForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('editSubmitBtn');
  btn.disabled = true;
  btn.textContent = 'Saving…';

  const fd = new FormData(this);
  const resp = await fetch(location.href, {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: fd
  });
  const data = await resp.json();
  btn.disabled = false;
  btn.textContent = 'Save Changes';

  if (data.success) {
    closeEdit();
    location.reload();
  } else {
    const alert = document.getElementById('editAlert');
    alert.textContent = data.error || 'Failed to update.';
    alert.style.display = 'block';
  }
});

function confirmDelete(id) {
  currentDeleteId = id;
  document.getElementById('deleteModal').classList.add('active');
}

function closeDelete() {
  document.getElementById('deleteModal').classList.remove('active');
  currentDeleteId = null;
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!currentDeleteId) return;
  const btn = this;
  btn.disabled = true;
  btn.textContent = 'Deleting…';

  const resp = await fetch(location.href, {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'request_id=' + currentDeleteId
  });
  const data = await resp.json();

  if (data.success) {
    document.getElementById('row-' + currentDeleteId)?.remove();
    closeDelete();
  } else {
    alert('Deletion failed.');
    closeDelete();
  }
  btn.disabled = false;
  btn.textContent = 'Yes, Delete';
});

document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', function(e) {
    if (e.target === this) {
      this.classList.remove('active');
      currentDeleteId = null;
    }
  });
});
</script>

<?php require __DIR__ . '/../views/layouts/scout_footer.php'; ?>
