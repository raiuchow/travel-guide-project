<?php include 'layout_top.php'; ?>
<h2>User Management</h2>
<?php foreach ($errors ?? [] as $er): ?><p class="error"><?= e($er) ?></p><?php endforeach; ?>
<form method="post" onsubmit="return validateUserForm()" class="box">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <h3>Add New User</h3>
    <input name="name" placeholder="Name">
    <input name="email" placeholder="Email">
    <input name="password" type="password" placeholder="Password min 8 chars">
    <select name="role"><option value="user">User</option><option value="scout">Scout</option><option value="admin">Admin</option></select>
    <label><input type="checkbox" name="is_verified" checked> Verified</label>
    <button>Add User</button>
</form>
<table>
<tr><th>Name</th><th>Email</th><th>Role</th><th>Verified</th><th>Action</th></tr>
<?php foreach ($users as $u): ?>
<tr id="user<?= e($u['id']) ?>">
    <td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td><td><?= $u['is_verified'] ? 'Yes' : 'No' ?></td>
    <td>
        <button onclick="apiAction('toggle_user', <?= e($u['id']) ?>)">Toggle Verify</button>
        <button onclick="apiAction('delete_user', <?= e($u['id']) ?>)">Delete</button>
    </td>
</tr>
<?php endforeach; ?>
</table>
<?php include 'layout_bottom.php'; ?>
