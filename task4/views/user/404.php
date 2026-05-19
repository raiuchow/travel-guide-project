<?php
// views/user/404.php
$pageTitle = 'Page Not Found';
require __DIR__ . '/../partials/header.php';
?>
<main class="container">
  <div class="error-page">
    <h1 class="error-code">404</h1>
    <p class="error-msg">Destination not found or has been unpublished.</p>
    <a href="/browse.php" class="btn-primary">← Back to Browse</a>
  </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
