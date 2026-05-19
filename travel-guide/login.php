<?php
session_start();
include 'config/db.php';

$message = "";

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {

    $token = hash('sha256', $_COOKIE['remember_me']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['is_verified'] == 1) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        header("Location: home.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        if ($user['is_verified'] == 0) {
            $message = "Your account is not approved by admin yet.";
        }

        elseif (password_verify($password, $user['password_hash'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if (isset($_POST['remember'])) {

                $token = bin2hex(random_bytes(32));
                $hashedToken = hash('sha256', $token);

                $update = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $update->execute([$hashedToken, $user['id']]);

                setcookie(
                    "remember_me",
                    $token,
                    time() + (30 * 24 * 60 * 60),
                    "/"
                );
            }

            header("Location: home.php");
            exit;

        } else {
            $message = "Invalid email or password";
        }

    } else {
        $message = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(135deg, #667eea, #764ba2);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: Arial;
}

.login-card{
    width: 100%;
    max-width: 420px;
    background: #fff;
    border-radius: 18px;
    padding: 35px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.form-control{
    border-radius: 10px;
}

.btn-login{
    background: #667eea;
    border: none;
    border-radius: 10px;
    padding: 10px;
    font-weight: 600;
}

.btn-login:hover{
    background: #5a67d8;
}

.error{
    color: red;
    text-align: center;
    margin-bottom: 10px;
}
</style>

</head>

<body>

<div class="login-card">

<h2 class="text-center">Welcome Back 👋</h2>
<p class="text-center text-muted mb-4">Login to continue</p>

<?php if($message != ""): ?>
    <div class="error"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="mb-3">
    <input type="checkbox" name="remember">
    <label>Remember Me</label>
</div>

<button class="btn btn-login w-100 text-white">
Login
</button>

</form>

<a href="register.php" class="d-block text-center mt-3 text-primary">
Don't have an account? Sign up
</a>

</div>

</body>
</html>