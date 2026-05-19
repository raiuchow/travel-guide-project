<?php include 'layout_top.php'; ?>
<h2>Post Moderation</h2>
<h3>Pending Requests</h3>
<table>
<tr><th>ID</th><th>Scout</th><th>Data</th><th>Action</th></tr>
<?php foreach ($requests as $r): ?>
<tr>
    <td><?= e($r['id']) ?></td><td><?= e($r['scout_name']) ?></td><td><pre><?= e($r['post_data']) ?></pre></td>
    <td><button onclick="apiAction('approve_request',<?= e($r['id']) ?>)">Approve</button><button onclick="apiAction('reject_request',<?= e($r['id']) ?>)">Reject</button></td>
</tr>
<?php endforeach; ?>
</table>
<h3>All Posts</h3>
<?php foreach ($posts as $p): ?>
<form method="post" class="box" onsubmit="return validatePostForm(this)">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" name="edit_post" value="1">
    <input type="hidden" name="id" value="<?= e($p['id']) ?>">
    <input name="title" value="<?= e($p['title']) ?>">
    <input name="country" value="<?= e($p['country']) ?>">
    <input name="genre" value="<?= e($p['genre']) ?>">
    <select name="cost_level"><option <?= $p['cost_level']=='low'?'selected':'' ?>>low</option><option <?= $p['cost_level']=='medium'?'selected':'' ?>>medium</option><option <?= $p['cost_level']=='high'?'selected':'' ?>>high</option></select>
    <textarea name="short_history"><?= e($p['short_history']) ?></textarea>
    <textarea name="travel_medium_info"><?= e($p['travel_medium_info']) ?></textarea>
    <button>Save</button>
    <button type="button" onclick="apiAction('delete_post',<?= e($p['id']) ?>)">Delete</button>
</form>
<?php endforeach; ?>
<?php include 'layout_bottom.php'; ?>
