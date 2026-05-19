<?php
class AdminModel {
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function dashboardCounts() {
        return [
            'admins' => $this->db->query("SELECT COUNT(*) c FROM users WHERE role='admin'")->fetch()['c'],
            'scouts' => $this->db->query("SELECT COUNT(*) c FROM users WHERE role='scout'")->fetch()['c'],
            'users' => $this->db->query("SELECT COUNT(*) c FROM users WHERE role='user'")->fetch()['c'],
            'pending' => $this->db->query("SELECT COUNT(*) c FROM post_requests WHERE status='pending'")->fetch()['c'],
            'posts' => $this->db->query("SELECT COUNT(*) c FROM posts")->fetch()['c'],
            'comments' => $this->db->query("SELECT COUNT(*) c FROM comments")->fetch()['c']
        ];
    }

    public function allUsers() {
        return $this->db->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
    }

    public function addUser($name, $email, $password, $role, $verified) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users(name,email,password_hash,role,is_verified,created_at) VALUES(?,?,?,?,?,NOW())");
        return $stmt->execute([$name, $email, $hash, $role, $verified]);
    }

    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);
        return $stmt->fetch() ? true : false;
    }

    public function toggleVerify($id) {
        $stmt = $this->db->prepare("UPDATE users SET is_verified = IF(is_verified=1,0,1) WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function deleteUser($id, $currentAdminId) {
        if ($id == $currentAdminId) return false;
        $this->db->prepare("DELETE FROM comments WHERE user_id=?")->execute([$id]);
        $this->db->prepare("DELETE FROM wishlist WHERE user_id=?")->execute([$id]);
        $this->db->prepare("DELETE FROM post_requests WHERE scout_id=?")->execute([$id]);
        $this->db->prepare("DELETE FROM posts WHERE scout_id=?")->execute([$id]);
        return $this->db->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    }

    public function pendingRequests() {
        return $this->db->query("SELECT pr.*, u.name scout_name FROM post_requests pr LEFT JOIN users u ON pr.scout_id=u.id WHERE pr.status='pending' ORDER BY pr.id DESC")->fetchAll();
    }

    public function approveRequest($id) {
        $stmt = $this->db->prepare("SELECT * FROM post_requests WHERE id=? AND status='pending'");
        $stmt->execute([$id]);
        $req = $stmt->fetch();
        if (!$req) return false;

        $data = json_decode($req['post_data'], true);
        if (!$data) return false;

        $insert = $this->db->prepare("INSERT INTO posts(scout_id,title,short_history,country,genre,cost_level,travel_medium_info,status,created_at,updated_at) VALUES(?,?,?,?,?,?,?,'approved',NOW(),NOW())");
        $insert->execute([
            $req['scout_id'],
            $data['title'] ?? '',
            $data['short_history'] ?? '',
            $data['country'] ?? '',
            $data['genre'] ?? '',
            $data['cost_level'] ?? 'low',
            $data['travel_medium_info'] ?? ''
        ]);
        return $this->db->prepare("DELETE FROM post_requests WHERE id=?")->execute([$id]);
    }

    public function rejectRequest($id) {
        return $this->db->prepare("UPDATE post_requests SET status='rejected' WHERE id=?")->execute([$id]);
    }

    public function allPosts() {
        return $this->db->query("SELECT * FROM posts ORDER BY id DESC")->fetchAll();
    }

    public function updatePost($id, $title, $history, $country, $genre, $cost, $medium) {
        $stmt = $this->db->prepare("UPDATE posts SET title=?, short_history=?, country=?, genre=?, cost_level=?, travel_medium_info=?, updated_at=NOW() WHERE id=?");
        return $stmt->execute([$title, $history, $country, $genre, $cost, $medium, $id]);
    }

    public function deletePost($id) {
        $this->db->prepare("DELETE FROM comments WHERE post_id=?")->execute([$id]);
        $this->db->prepare("DELETE FROM wishlist WHERE post_id=?")->execute([$id]);
        return $this->db->prepare("DELETE FROM posts WHERE id=?")->execute([$id]);
    }

    public function allComments() {
        return $this->db->query("SELECT c.*, p.title, u.name FROM comments c LEFT JOIN posts p ON c.post_id=p.id LEFT JOIN users u ON c.user_id=u.id ORDER BY c.id DESC")->fetchAll();
    }

    public function deleteComment($id) {
        return $this->db->prepare("DELETE FROM comments WHERE id=?")->execute([$id]);
    }
}
