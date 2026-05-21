<?php

require_once __DIR__ . '/bootstrap/autoload.php';
require_once __DIR__ . '/bootstrap/helpers.php';

use Config\Database;
use App\Services\AuthService;

// Already logged in → redirect
if (AuthService::isLoggedIn()) {
    header('Location: ' . appUrl('scout/my_requests.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $db   = Database::getInstance();
        $conn = $db->getConnection(); // \mysqli

        // Prepared statement — prevents SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            // Verify password (password_verify for hashed, plain compare for legacy)
            $passwordOk = password_verify($password, $user['password_hash'])
                       || $password === $user['password_hash']; // fallback for plain-text dev data

            if ($passwordOk && $user['role'] === 'scout' && $user['is_verified']) {
                AuthService::login($user);
                header('Location: ' . appUrl('scout/my_requests.php'));
                exit;
            } else {
                $error = 'Only verified scouts can access this system.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login · ScoutHub</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400&display=swap" rel="stylesheet">
<style>
:root{--bg:#080c10;--surface:#0f1620;--border:#1e2d3d;--accent:#00d4aa;--text:#e8edf2;--muted:#6b7f94;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;
  display:flex;align-items:center;justify-content:center;}
.box{background:var(--surface);border:1px solid var(--border);border-radius:16px;
  padding:2.5rem;width:100%;max-width:400px;}
h1{font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:var(--accent);margin-bottom:.25rem;}
p.sub{font-size:.85rem;color:var(--muted);margin-bottom:1.75rem;}
label{font-size:.78rem;color:var(--muted);display:block;margin-bottom:.35rem;}
input{width:100%;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:8px;
  color:var(--text);font-size:.9rem;padding:.65rem .9rem;margin-bottom:1rem;outline:none;
  transition:border-color .2s;}
input:focus{border-color:var(--accent);}
button{width:100%;background:var(--accent);color:#000;border:none;border-radius:8px;
  padding:.75rem;font-size:.9rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;}
button:hover{background:#00b894;}
.err{background:rgba(230,57,70,.1);border:1px solid rgba(230,57,70,.3);color:#e63946;
  border-radius:8px;padding:.65rem .9rem;font-size:.85rem;margin-bottom:1rem;}
</style>
</head>
<body>
<div class="box">
  <h1>ScoutHub</h1>
  <p class="sub">Sign in to access the scout panel</p>
  <?php if ($error): ?><div class="err">⚠ <?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="POST">
    <label>Email</label>
    <input type="email" name="email" placeholder="scout@demo.com" required>
    <label>Password</label>
    <input type="password" name="password" placeholder="••••••••" required>
    <button type="submit">Sign In</button>
  </form>
</div>
</body>
</html>
