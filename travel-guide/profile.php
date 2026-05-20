<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "travel_guide");

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

        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $message = "Email already exists";
        }

        else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password_hash, role, is_verified) VALUES (?, ?, ?, ?, 0)");
            mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hash, $role);
            mysqli_stmt_execute($stmt);

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
<title>Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
    font-family: Arial;
}

.container-box{
    max-width:650px;
    margin:50px auto;
}

.card-box{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 5px 20px rgba(0,0,0,0.1);
    margin-bottom:20px;
}

.header{
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:white;
    padding:25px;
    border-radius:15px;
    margin-bottom:20px;
}

img{
    width:100px;
    height:100px;
    border-radius:50%;
    object-fit:cover;
}

.success{
    background:#d4edda;
    color:#155724;
    padding:10px;
    border-radius:5px;
    margin-bottom:10px;
}
</style>

</head>

<body>

<div class="container-box">

<div class="header">
    <h2>My Profile 👤</h2>
    <p>Update your account information</p>
</div>

<?php if($message != ""): ?>
<div class="success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="card-box">

<h4>Update Profile</h4>

<form method="POST" enctype="multipart/form-data">

<div class="text-center mb-3">
    <img src="public/uploads/<?php echo $user['profile_image'] ?? 'default.png'; ?>">
</div>

<div class="mb-3">
<label>Name</label>
<input type="text" name="name" class="form-control" value="<?php echo $user['name']; ?>" required>
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
</div>

<div class="mb-3">
<label>Profile Image</label>
<input type="file" name="image" class="form-control">
</div>

<button name="update_profile" class="btn btn-primary w-100">
Update Profile
</button>

</form>

</div>

<div class="card-box">

<h4>Change Password 🔒</h4>

<form method="POST">

<div class="mb-3">
<label>Current Password</label>
<input type="password" name="current_password" class="form-control" required>
</div>

<div class="mb-3">
<label>New Password</label>
<input type="password" name="new_password" class="form-control" required>
</div>

<button name="change_password" class="btn btn-warning w-100">
Change Password
</button>

</form>

</div>

</div>

</body>
</html>
