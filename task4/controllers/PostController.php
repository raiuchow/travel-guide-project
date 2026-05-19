<?php
// controllers/PostController.php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/CostEstimateModel.php';

class PostController {

    private PostModel        $postModel;
    private CommentModel     $commentModel;
    private CostEstimateModel $costModel;

    public function __construct() {
        $this->postModel    = new PostModel();
        $this->commentModel = new CommentModel();
        $this->costModel    = new CostEstimateModel();
    }

    // ── Browse Published Posts ────────────────────────────────────────────────

    public function browse(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Browsing allowed for any verified user (user / scout / admin).
        if (!isVerifiedAny()) {
            header('Location: /login.php?error=not_verified');
            exit;
        }

        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $limit  = 9;
        $offset = ($page - 1) * $limit;

        $posts     = $this->postModel->getApprovedPosts($limit, $offset);
        $total     = $this->postModel->countApproved();
        $pages     = (int) ceil($total / $limit);
        $countries = $this->postModel->getDistinctCountries();
        $genres    = $this->postModel->getDistinctGenres();
        $user      = currentUser();

        require __DIR__ . '/../views/user/browse.php';
    }

    // ── Post Detail ───────────────────────────────────────────────────────────

    public function detail(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isVerifiedAny()) {
            header('Location: /login.php?error=not_verified');
            exit;
        }

        $id   = (int) ($_GET['id'] ?? 0);
        $post = $this->postModel->getPostById($id);

        if (!$post) {
            http_response_code(404);
            require __DIR__ . '/../views/user/404.php';
            return;
        }

        $comments = $this->commentModel->getCommentsByPost($id);
        $cost     = $this->costModel->getByPost($id, $post['cost_level']);
        $user     = currentUser();
        $csrf     = csrfToken();

        require __DIR__ . '/../views/user/detail.php';
    }
}
