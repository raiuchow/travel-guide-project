/**
 * public/js/detail.js
 * Handles:
 *   1. Comment form submission (AJAX POST)
 *   2. Comment deletion (AJAX DELETE)
 *   3. Cost calculator (AJAX GET)
 *   4. Client-side JS validation on all inputs
 *
 * Expects globals set by PHP in the view:
 *   CURRENT_USER_ID, CSRF_TOKEN, CAN_COMMENT
 */

'use strict';

(function () {

  // ── Utilities ─────────────────────────────────────────────────────────────
  function escapeHtml(str) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(str || '').replace(/[&<>"']/g, m => map[m]);
  }

  function formatDate(isoStr) {
    try {
      const d = new Date(isoStr);
      return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
             + ' · ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    } catch (_) { return isoStr; }
  }

  function showError(id, msg) {
    const el = document.getElementById(id);
    if (el) el.textContent = msg;
  }
  function clearError(id) { showError(id, ''); }

  function setAlert(el, msg, type = 'error') {
    if (!el) return;
    el.className = `alert alert-${type}`;
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 6000);
  }

  function updateCommentCount(delta) {
    const el = document.getElementById('commentCount');
    if (!el) return;
    const n = (parseInt(el.textContent.replace(/\D/g, ''), 10) || 0) + delta;
    el.textContent = `(${n})`;
  }

  // ─────────────────────────────────────────────────────────────────────────
  // 1. COMMENT FORM
  // ─────────────────────────────────────────────────────────────────────────
  const commentForm    = document.getElementById('commentForm');
  const contentInput   = document.getElementById('commentContent');
  const submitBtn      = document.getElementById('submitCommentBtn');
  const formErrorEl    = document.getElementById('commentFormError');
  const commentList    = document.getElementById('commentList');
  const emptyMsg       = document.getElementById('emptyMsg');
  const charCountEl    = document.getElementById('charCount');
  const MAX_COMMENT    = 1000;

  // ── Character counter ─────────────────────────────────────────────────────
  if (contentInput && charCountEl) {
    contentInput.addEventListener('input', () => {
      const n = contentInput.value.length;
      charCountEl.textContent = `${n}/${MAX_COMMENT}`;
      charCountEl.classList.toggle('near', n >= MAX_COMMENT * 0.8 && n < MAX_COMMENT);
      charCountEl.classList.toggle('over', n >= MAX_COMMENT);
    });
  }

  // ── JS Validation ─────────────────────────────────────────────────────────
  function validateCommentForm() {
    let valid = true;
    clearError('content-err');

    const content = contentInput ? contentInput.value.trim() : '';
    if (!content) {
      showError('content-err', 'Comment cannot be empty.');
      valid = false;
    } else if (content.length > MAX_COMMENT) {
      showError('content-err', `Comment must be ${MAX_COMMENT} characters or fewer.`);
      valid = false;
    }
    return valid;
  }

  // ── Build comment HTML ────────────────────────────────────────────────────
  function buildCommentHTML(c) {
    const isOwn = (typeof CURRENT_USER_ID !== 'undefined') && (CURRENT_USER_ID === parseInt(c.user_id, 10));
    const deleteBtn = isOwn
      ? `<button class="btn-delete-comment" data-id="${c.id}" title="Delete your comment">✕</button>`
      : '';
    return `
      <div class="comment-item" id="comment-${c.id}">
        <div class="comment-avatar">${escapeHtml((c.reviewer_name || '?').charAt(0).toUpperCase())}</div>
        <div class="comment-body">
          <div class="comment-header">
            <strong class="comment-author">${escapeHtml(c.reviewer_name)}</strong>
            <span class="comment-date">${formatDate(c.created_at)}</span>
            ${deleteBtn}
          </div>
          <p class="comment-text">${escapeHtml(c.content).replace(/\n/g, '<br>')}</p>
        </div>
      </div>`;
  }

  // ── Submit comment ────────────────────────────────────────────────────────
  if (commentForm && typeof CAN_COMMENT !== 'undefined' && CAN_COMMENT) {
    commentForm.addEventListener('submit', async function (e) {
      e.preventDefault();

      if (!validateCommentForm()) return;

      submitBtn.disabled = true;
      submitBtn.textContent = 'Posting…';

      const formData = new FormData(commentForm);

      try {
        const resp = await fetch('/api/comments/add.php', {
          method: 'POST',
          body: formData,
        });

        const data = await resp.json();

        if (!resp.ok) {
          const msg = data.errors ? data.errors.join(' ') : (data.error || 'Failed to post comment.');
          setAlert(formErrorEl, msg, 'error');
          return;
        }

        // Prepend new comment
        if (emptyMsg) emptyMsg.remove();
        commentList.insertAdjacentHTML('afterbegin', buildCommentHTML(data));

        // Attach delete handler to new button
        const newItem = document.getElementById(`comment-${data.id}`);
        const delBtn  = newItem && newItem.querySelector('.btn-delete-comment');
        if (delBtn) delBtn.addEventListener('click', handleDeleteComment);

        updateCommentCount(+1);
        commentForm.reset();
        if (charCountEl) charCountEl.textContent = `0/${MAX_COMMENT}`;
        clearError('content-err');
        setAlert(formErrorEl, 'Comment posted!', 'success');

      } catch (err) {
        setAlert(formErrorEl, 'Network error. Please try again.', 'error');
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Post Comment';
      }
    });
  }

  // ─────────────────────────────────────────────────────────────────────────
  // 2. DELETE COMMENT
  // ─────────────────────────────────────────────────────────────────────────
  async function handleDeleteComment(e) {
    const btn       = e.currentTarget;
    const commentId = parseInt(btn.dataset.id, 10);
    if (!commentId) return;

    if (!confirm('Delete this comment?')) return;

    btn.disabled = true;

    try {
      const resp = await fetch(`/api/comments/delete.php?id=${commentId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-Token': (typeof CSRF_TOKEN !== 'undefined') ? CSRF_TOKEN : '',
        },
      });

      const data = await resp.json();

      if (!resp.ok) {
        alert(data.error || 'Could not delete comment.');
        btn.disabled = false;
        return;
      }

      // Animate out and remove
      const item = document.getElementById(`comment-${commentId}`);
      if (item) {
        item.style.transition = 'opacity .3s, transform .3s';
        item.style.opacity    = '0';
        item.style.transform  = 'translateX(-10px)';
        setTimeout(() => {
          item.remove();
          updateCommentCount(-1);
          // Show empty message if no comments left
          if (commentList && commentList.querySelectorAll('.comment-item').length === 0) {
            commentList.innerHTML = '<p class="empty-comments" id="emptyMsg">No comments yet. Be the first!</p>';
          }
        }, 300);
      }

    } catch (err) {
      alert('Network error. Please try again.');
      btn.disabled = false;
    }
  }

  // Attach delete handlers to existing comments on page load
  document.querySelectorAll('.btn-delete-comment').forEach(btn => {
    btn.addEventListener('click', handleDeleteComment);
  });

  // ─────────────────────────────────────────────────────────────────────────
  // 3. COST CALCULATOR
  // ─────────────────────────────────────────────────────────────────────────
  const calcBtn      = document.getElementById('calcBtn');
  const calcResult   = document.getElementById('calcResult');
  const calcTotal    = document.getElementById('calcTotal');
  const calcNote     = document.getElementById('calcNote');
  const travelersEl  = document.getElementById('calcTravelers');
  const daysEl       = document.getElementById('calcDays');

  // ── JS validation for calculator ─────────────────────────────────────────
  function validateCalc() {
    let valid = true;
    clearError('travelers-err');
    clearError('days-err');

    const travelers = parseInt(travelersEl ? travelersEl.value : '1', 10);
    const days      = parseInt(daysEl      ? daysEl.value      : '7', 10);

    if (isNaN(travelers) || travelers < 1 || travelers > 50) {
      showError('travelers-err', 'Enter 1–50 travelers.');
      valid = false;
    }
    if (isNaN(days) || days < 1 || days > 365) {
      showError('days-err', 'Enter 1–365 days.');
      valid = false;
    }
    return valid;
  }

  if (calcBtn) {
    calcBtn.addEventListener('click', async function () {
      if (!validateCalc()) return;

      const postId    = parseInt(calcBtn.dataset.postId, 10);
      const travelers = parseInt(travelersEl.value, 10);
      const days      = parseInt(daysEl.value, 10);

      calcBtn.disabled    = true;
      calcBtn.textContent = 'Calculating…';

      try {
        const url = `/api/cost/estimate.php?post_id=${postId}&travelers=${travelers}&days=${days}`;
        const resp = await fetch(url);
        const data = await resp.json();

        if (!resp.ok) {
          const msg = data.errors ? data.errors.join(' ') : (data.error || 'Calculation failed.');
          showError('travelers-err', msg);
          return;
        }

        const formatted = new Intl.NumberFormat('en-US', {
          style: 'currency', currency: data.currency || 'USD',
          maximumFractionDigits: 0,
        }).format(data.total);

        calcTotal.textContent = formatted;
        calcNote.textContent  = data.source === 'estimate'
          ? `Estimate based on ${travelers} traveler${travelers > 1 ? 's' : ''} for ${days} day${days > 1 ? 's' : ''}`
          : `Based on verified cost data · ${travelers} traveler${travelers > 1 ? 's' : ''} · ${days} day${days > 1 ? 's' : ''}`;

        calcResult.classList.remove('hidden');

      } catch (err) {
        showError('travelers-err', 'Network error. Please try again.');
      } finally {
        calcBtn.disabled    = false;
        calcBtn.textContent = 'Calculate';
      }
    });

    // Trigger on Enter inside inputs
    [travelersEl, daysEl].forEach(el => {
      if (el) el.addEventListener('keydown', e => { if (e.key === 'Enter') calcBtn.click(); });
    });
  }

})();
