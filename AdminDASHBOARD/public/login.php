<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
start_secure_session();
$db = (new Database())->connect();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $stmt = $db->prepare('SELECT * FROM users WHERE email=? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($pass, $user['password_hash']) && $user['role'] === 'admin') {
        $_SESSION['user_id']=$user['id']; $_SESSION['name']=$user['name']; $_SESSION['role']=$user['role'];
        header('Location: admin/dashboard.php'); exit;
    } else $error='Invalid admin login';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Admin Login</title>
        <link rel="stylesheet" href="assets/style.css">
    </head>
    <body>
        <main>
            <h2>Admin Login</h2>
            <?php if($error): ?>
                <p class="error"><?= e($error) ?></p>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input name="email" placeholder="Email">
                    <input type="password" name="password" placeholder="Password">
                    <button>Login</button>
                </form>
            </main>
        </body>
        </html>
