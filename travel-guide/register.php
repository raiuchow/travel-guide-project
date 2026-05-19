<?php
session_start();
include 'config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    $allowed_roles = ['user', 'scout', 'admin'];

    if (!in_array($role, $allowed_roles)) {
        die("Invalid role selected");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
    }

    elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters";
    }

    else {

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $message = "Email already exists";
        }

        else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password_hash, role, is_verified)
                 VALUES (?, ?, ?, ?, 0)"
            );

            $stmt->execute([$name, $email, $hash, $role]);

            $_SESSION['message'] = "Registration Successful 🎉 Wait for admin approval.";

            header("Location: login.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #667eea, #764ba2);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: Arial;
}

.card-box {
    width: 100%;
    max-width: 450px;
    background: white;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.form-control {
    border-radius: 10px;
}

.btn-register {
    background: #667eea;
    border: none;
    border-radius: 10px;
    padding: 10px;
    font-weight: 600;
}

.btn-register:hover {
    background: #5a67d8;
}

.message {
    text-align: center;
    margin-bottom: 10px;
    color: red;
}

.small-link {
    text-align: center;
    display: block;
    margin-top: 15px;
    color: #667eea;
    text-decoration: none;
}

.small-link:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="card-box">

<h2 class="text-center mb-2">Create Account 🚀</h2>
<p class="text-center text-muted mb-4">Join us and get started</p>

<?php if ($message != ""): ?>
    <div class="message">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<form method="POST" onsubmit="return validateForm()">

<div class="mb-3">
<label>Name</label>
<input type="text" name="name" class="form-control" required>
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="mb-3">
<label>Role</label>
<select name="role" class="form-control" required>
<option value="user">User</option>
<option value="scout">Scout</option>
<option value="admin">Admin</option>
</select>
</div>

<button class="btn btn-register w-100 text-white">
Register
</button>

</form>

<a href="login.php" class="small-link">
Already have an account? Login
</a>

</div>

<script>
function validateForm(){

let email = document.querySelector("[name=email]").value;
let pass = document.querySelector("[name=password]").value;

let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

if(!emailPattern.test(email)){
    alert("Invalid email");
    return false;
}

if(pass.length < 8){
    alert("Password must be at least 8 characters");
    return false;
}

return true;
}
</script>

</body>
</html>