<?php include 'layout_top.php'; ?>
<h2>Dashboard Summary</h2>
<div class="cards">
    <div>Admins<br><b><?= e($counts['admins']) ?></b></div>
    <div>Scouts<br><b><?= e($counts['scouts']) ?></b></div>
    <div>Users<br><b><?= e($counts['users']) ?></b></div>
    <div>Pending Requests<br><b><?= e($counts['pending']) ?></b></div>
    <div>Total Posts<br><b><?= e($counts['posts']) ?></b></div>
    <div>Total Comments<br><b><?= e($counts['comments']) ?></b></div>
</div>
<?php include 'layout_bottom.php'; ?>
