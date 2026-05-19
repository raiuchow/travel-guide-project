<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['update_profile'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check->execute([$email, $user_id]);

    if ($check->rowCount() > 0) {
        $message = "Email already exists!";
    } else {

        $imageName = $user['profile_image'];

        if (!empty($_FILES['image']['name'])) {

            $uploadDir = "public/uploads/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $imageName = time() . "_" . basename($_FILES['image']['name']);

            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                $uploadDir . $imageName
            );
        }

        $update = $conn->prepare("UPDATE users SET name=?, email=?, profile_image=? WHERE id=?");
        $update->execute([$name, $email, $imageName, $user_id]);

        $_SESSION['name'] = $name;

        $message = "Profile updated successfully!";
    }
}

if (isset($_POST['change_password'])) {

    $current = $_POST['current_password'];
    $new = $_POST['new_password'];

    if (password_verify($current, $user['password_hash'])) {

        if (strlen($new) < 8) {
            $message = "Password must be at least 8 characters!";
        } else {

            $hash = password_hash($new, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
            $update->execute([$hash, $user_id]);

            $message = "Password changed successfully!";
        }

    } else {
        $message = "Current password is wrong!";
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