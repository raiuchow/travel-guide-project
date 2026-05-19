<?php
// views/user/browse.php
// Variables available: $posts, $total, $page, $pages, $countries, $genres, $user
$pageTitle = 'Browse Destinations';
require __DIR__ . '/../partials/header.php';
?>

<main class="container">

  <!-- Hero -->
  <section class="browse-hero">
    <h1 class="hero-title">Discover <em>Your</em> Next Adventure</h1>
    <p class="hero-sub">Explore handpicked destinations from scouts around the globe.</p>

    <!-- Live Search -->
    <div class="search-wrap">
      <input type="text" id="liveSearch" class="search-input"
             placeholder="Search by destination or country…"
             autocomplete="off" maxlength="100">
      <span class="search-icon">&#9906;</span>
    </div>
  </section>

  <!-- Filters -->
  <aside class="filter-bar">
    <div class="filter-group">
      <label for="filterCountry">Country</label>
      <select id="filterCountry">
        <option value="">All Countries</option>
        <?php foreach ($countries as $c): ?>
          <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="filter-group">
      <label>Genre</label>
      <div class="genre-checkboxes">
        <?php foreach ($genres as $g): ?>
          <label class="chip-label">
            <input type="checkbox" class="filterGenre" value="<?= htmlspecialchars($g) ?>">
            <?= htmlspecialchars(ucfirst($g)) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="filter-group">
      <label>Cost Level</label>
      <div class="cost-radios">
        <label class="chip-label"><input type="radio" name="filterCost" value=""> Any</label>
        <label class="chip-label"><input type="radio" name="filterCost" value="low"> Low</label>
        <label class="chip-label"><input type="radio" name="filterCost" value="medium"> Medium</label>
        <label class="chip-label"><input type="radio" name="filterCost" value="high"> High</label>
      </div>
    </div>

    <button id="clearFilters" class="btn-ghost">Clear Filters</button>
  </aside>

  <!-- Results count -->
  <div class="results-header">
    <span id="resultCount"><?= $total ?> destinations</span>
  </div>

  <!-- Post Grid -->
  <div id="postGrid" class="post-grid">
    <?php if (empty($posts)): ?>
      <p class="empty-state">No destinations found. Try adjusting your filters.</p>
    <?php else: ?>
      <?php foreach ($posts as $p): ?>
        <?php include __DIR__ . '/partials/post_card.php'; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Pagination (only shown when not filtering) -->
  <?php if ($pages > 1): ?>
    <nav class="pagination" id="paginationWrap">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?page=<?= $i ?>"
           class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </nav>
  <?php endif; ?>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
<script src="/public/js/browse.js"></script>
