<?php include 'layout_top.php'; ?>
<h2>Comment Moderation</h2>
<table>
<tr><th>Post</th><th>User</th><th>Comment</th><th>Date</th><th>Action</th></tr>
<?php foreach ($comments as $c): ?>
<tr>
    <td><?= e($c['title']) ?></td><td><?= e($c['name']) ?></td><td><?= e($c['content']) ?></td><td><?= e($c['created_at']) ?></td>
    <td><button onclick="apiAction('delete_comment',<?= e($c['id']) ?>)">Delete</button></td>
</tr>
<?php endforeach; ?>
</table>
<?php include 'layout_bottom.php'; ?>
