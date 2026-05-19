/**
 * public/js/browse.js
 * Handles live search (debounced) and multi-filter AJAX for the browse page.
 * All DOM updates done without page reload.
 */

'use strict';

(function () {

  // ── DOM refs ──────────────────────────────────────────────────────────────
  const searchInput   = document.getElementById('liveSearch');
  const countrySelect = document.getElementById('filterCountry');
  const genreBoxes    = document.querySelectorAll('.filterGenre');
  const costRadios    = document.querySelectorAll('input[name="filterCost"]');
  const clearBtn      = document.getElementById('clearFilters');
  const postGrid      = document.getElementById('postGrid');
  const resultCount   = document.getElementById('resultCount');
  const paginationWrap = document.getElementById('paginationWrap');

  // ── Debounce utility ──────────────────────────────────────────────────────
  function debounce(fn, delay) {
    let timer;
    return function (...args) {
      clearTimeout(timer);
      timer = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  // ── Build card HTML from JSON post object ─────────────────────────────────
  function buildCardHTML(post) {
    const badgeClass = { low: 'badge-low', medium: 'badge-medium', high: 'badge-high' }[post.cost_level] || 'badge-medium';
    const costLabel  = { low: 'Low Cost', medium: 'Medium Cost', high: 'High Cost' }[post.cost_level] || 'Cost';
    const genre      = post.genre ? post.genre.charAt(0).toUpperCase() + post.genre.slice(1) : '';
    return `
      <article class="post-card" data-id="${post.id}">
        <div class="card-genre-tag">${escapeHtml(genre)}</div>
        <div class="card-body">
          <h2 class="card-title">
            <a href="/post.php?id=${post.id}">${escapeHtml(post.title)}</a>
          </h2>
          <p class="card-country">📍 ${escapeHtml(post.country)}</p>
          <p class="card-excerpt">${escapeHtml(post.short_history)}</p>
          <div class="card-footer">
            <span class="badge ${badgeClass}">${costLabel}</span>
            <a href="/post.php?id=${post.id}" class="btn-read-more">Read More →</a>
          </div>
        </div>
      </article>`;
  }

  // ── Skeleton loaders ──────────────────────────────────────────────────────
  function showSkeletons(n = 6) {
    let html = '';
    for (let i = 0; i < n; i++) {
      html += `
        <div class="skeleton-card">
          <div class="skeleton-line" style="height:16px;width:30%"></div>
          <div class="skeleton-line" style="height:22px;width:80%;margin-top:.5rem"></div>
          <div class="skeleton-line" style="height:14px;width:50%"></div>
          <div class="skeleton-line" style="height:60px"></div>
        </div>`;
    }
    postGrid.innerHTML = html;
  }

  // ── Render posts or empty state ───────────────────────────────────────────
  function renderPosts(posts) {
    if (!Array.isArray(posts) || posts.length === 0) {
      postGrid.innerHTML = '<p class="empty-state">No destinations match your search. Try different filters.</p>';
      resultCount.textContent = '0 destinations';
      return;
    }
    postGrid.innerHTML = posts.map(buildCardHTML).join('');
    resultCount.textContent = `${posts.length} destination${posts.length !== 1 ? 's' : ''}`;
  }

  // ── Hide pagination when filtering / searching ────────────────────────────
  function hidePagination() {
    if (paginationWrap) paginationWrap.classList.add('hidden');
  }
  function showPagination() {
    if (paginationWrap) paginationWrap.classList.remove('hidden');
  }

  // ── Collect current filter values ─────────────────────────────────────────
  function getFilters() {
    const genres = [];
    genreBoxes.forEach(cb => { if (cb.checked) genres.push(cb.value); });
    const costRadio = [...costRadios].find(r => r.checked);
    return {
      country:    countrySelect ? countrySelect.value : '',
      genres,
      cost_level: costRadio ? costRadio.value : '',
    };
  }

  function hasActiveFilters(filters) {
    return filters.country !== ''
      || filters.genres.length > 0
      || filters.cost_level !== '';
  }

  // ── AJAX: live search ─────────────────────────────────────────────────────
  async function doSearch(q) {
    if (q.trim() === '') {
      // If no filters active either, reload original list
      const filters = getFilters();
      if (!hasActiveFilters(filters)) {
        location.reload();
        return;
      }
      doFilter();
      return;
    }

    showSkeletons(6);
    hidePagination();

    try {
      const resp = await fetch(`/api/posts/search.php?q=${encodeURIComponent(q.trim())}`);
      if (!resp.ok) throw new Error('Search failed');
      const posts = await resp.json();
      renderPosts(posts);
    } catch (err) {
      postGrid.innerHTML = '<p class="empty-state">Search failed. Please try again.</p>';
    }
  }

  // ── AJAX: filter ──────────────────────────────────────────────────────────
  async function doFilter() {
    const q = searchInput ? searchInput.value.trim() : '';
    // If search has content, prefer search
    if (q.length >= 2) {
      doSearch(q);
      return;
    }

    const filters = getFilters();

    if (!hasActiveFilters(filters)) {
      showPagination();
      location.reload();
      return;
    }

    showSkeletons(6);
    hidePagination();

    const params = new URLSearchParams();
    if (filters.country)    params.set('country', filters.country);
    if (filters.cost_level) params.set('cost_level', filters.cost_level);
    filters.genres.forEach(g => params.append('genre[]', g));

    try {
      const resp = await fetch(`/api/posts/filter.php?${params.toString()}`);
      if (!resp.ok) throw new Error('Filter failed');
      const posts = await resp.json();
      renderPosts(posts);
    } catch (err) {
      postGrid.innerHTML = '<p class="empty-state">Filter failed. Please try again.</p>';
    }
  }

  // ── Clear filters ─────────────────────────────────────────────────────────
  function clearAll() {
    if (searchInput) searchInput.value = '';
    if (countrySelect) countrySelect.value = '';
    genreBoxes.forEach(cb => { cb.checked = false; });
    costRadios.forEach(r => { r.value === '' && (r.checked = true); });
    showPagination();
    location.reload();
  }

  // ── XSS escape helper ─────────────────────────────────────────────────────
  function escapeHtml(str) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(str || '').replace(/[&<>"']/g, m => map[m]);
  }

  // ── Event Listeners ───────────────────────────────────────────────────────
  if (searchInput) {
    searchInput.addEventListener('input', debounce(e => doSearch(e.target.value), 380));
  }

  if (countrySelect) {
    countrySelect.addEventListener('change', doFilter);
  }

  genreBoxes.forEach(cb => cb.addEventListener('change', doFilter));
  costRadios.forEach(r => r.addEventListener('change', doFilter));

  if (clearBtn) {
    clearBtn.addEventListener('click', clearAll);
  }

})();
