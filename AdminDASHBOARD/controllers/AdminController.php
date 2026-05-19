<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/AdminModel.php';

class AdminController {
    private $model;
    public function __construct() {
        admin_only();
        $db = (new Database())->connect();
        $this->model = new AdminModel($db);
    }

    public function dashboard() {
        $counts = $this->model->dashboardCounts();
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    public function users() {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            check_csrf();
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            $verified = isset($_POST['is_verified']) ? 1 : 0;

            if ($name === '') $errors[] = 'Name is required';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
            if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters';
            if (!in_array($role, ['admin','scout','user'])) $errors[] = 'Invalid role';
            if ($this->model->emailExists($email)) $errors[] = 'Email already exists';

            if (!$errors) {
                $this->model->addUser($name, $email, $password, $role, $verified);
                header('Location: users.php?success=1'); exit;
            }
        }
        $users = $this->model->allUsers();
        include __DIR__ . '/../views/admin/users.php';
    }

    public function posts() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post'])) {
            check_csrf();
            $this->model->updatePost((int)$_POST['id'], trim($_POST['title']), trim($_POST['short_history']), trim($_POST['country']), trim($_POST['genre']), $_POST['cost_level'], trim($_POST['travel_medium_info']));
            header('Location: posts.php?updated=1'); exit;
        }
        $requests = $this->model->pendingRequests();
        $posts = $this->model->allPosts();
        include __DIR__ . '/../views/admin/posts.php';
    }

    public function comments() {
        $comments = $this->model->allComments();
        include __DIR__ . '/../views/admin/comments.php';
    }

    public function api($action) {
        check_csrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) json_response(['ok'=>false, 'message'=>'Invalid ID']);

        if ($action === 'toggle_user') $ok = $this->model->toggleVerify($id);
        elseif ($action === 'delete_user') $ok = $this->model->deleteUser($id, $_SESSION['user_id']);
        elseif ($action === 'approve_request') $ok = $this->model->approveRequest($id);
        elseif ($action === 'reject_request') $ok = $this->model->rejectRequest($id);
        elseif ($action === 'delete_post') $ok = $this->model->deletePost($id);
        elseif ($action === 'delete_comment') $ok = $this->model->deleteComment($id);
        else $ok = false;

        json_response(['ok'=>$ok]);
    }
}
