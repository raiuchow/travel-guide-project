# Task 4 — General USER: Browse, Search, Filter, Comments & Cost Estimate

**Student:** 22-49489-3  
**Course:** Web Technologies  

---

## Directory Structure

```
task4/
├── config/
│   ├── database.php          # PDO singleton with prepared statements
│   ├── auth.php              # Session helpers, CSRF, role checks
│   └── schema_task4.sql      # Indexes + cost_estimates DDL (run once)
│
├── models/
│   ├── PostModel.php         # getApprovedPosts, searchPosts, filterPosts
│   ├── CommentModel.php      # getCommentsByPost, addComment, deleteComment
│   └── CostEstimateModel.php # getByPost with level fallback
│
├── controllers/
│   └── PostController.php    # browse() + detail() actions
│
├── views/
│   ├── partials/
│   │   ├── header.php        # Navbar (role-aware links)
│   │   └── footer.php
│   └── user/
│       ├── browse.php        # Post grid + search + filters
│       ├── detail.php        # Full post + comments + calculator
│       ├── 404.php
│       └── partials/
│           ├── post_card.php
│           └── comment_item.php
│
├── api/
│   ├── posts/
│   │   ├── search.php        # GET /api/posts/search.php?q=
│   │   └── filter.php        # GET /api/posts/filter.php?country=&genre[]=&cost_level=
│   ├── comments/
│   │   ├── add.php           # POST /api/comments/add.php
│   │   └── delete.php        # DELETE /api/comments/delete.php?id=N
│   └── cost/
│       └── estimate.php      # GET /api/cost/estimate.php?post_id=&travelers=&days=
│
├── public/
│   ├── css/style.css
│   └── js/
│       ├── browse.js         # Live search + filter AJAX
│       └── detail.js         # Comments AJAX + cost calculator
│
├── browse.php                # Entry: Browse posts
├── post.php                  # Entry: Post detail
└── .htaccess                 # Security headers + comment DELETE routing
```

---

## Setup

1. **Run the schema file** once on your shared database:
   ```sql
   SOURCE config/schema_task4.sql;
   ```

2. **Edit `config/database.php`** with your DB credentials.

3. **Place the task4 folder** inside your web root so paths like `/api/posts/search.php` resolve correctly (or adjust `RewriteBase` in `.htaccess`).

4. **Enable `mod_rewrite`** in Apache for the DELETE comment URL pattern.

---

## Grading Criteria Checklist

| # | Criterion                | How it's met |
|---|--------------------------|-------------|
| 1 | **Web Security**         | SQL injection: PDO + prepared statements on every query. XSS: `htmlspecialchars()` on all output, `strip_tags()` on comment input. CSRF: token generated in session, verified on every mutating request (POST/DELETE). Passwords are hashed by Task 1 (not in scope here). |
| 2 | **UI (HTML/CSS)**        | Responsive grid layout, Playfair Display + DM Sans fonts, sand/teal palette, smooth hover transitions, skeleton loaders. |
| 3 | **Feature Completeness** | Browse, detail, search, filter, comments (add/delete), cost calculator — all working. |
| 4 | **DB**                   | Uses shared schema tables (`posts`, `comments`, `cost_estimates`, `users`). No schema alterations. Proper FK usage. |
| 5 | **Auth (Session)**       | `requireVerifiedAny()` guards browse/detail. `requireVerifiedUser()` (role=user + is_verified=1) gates comment posting and cost use. |
| 6 | **MVC**                  | Models handle all DB queries, Controllers dispatch to views, Views contain only presentation logic. |
| 7 | **JS Validation**        | `browse.js`: filters client-side before firing AJAX. `detail.js`: comment non-empty + max-length check, char counter; calculator integer range checks. |
| 8 | **PHP Validation**       | Every API endpoint validates input server-side before any DB write (types, ranges, lengths, role checks). |
| 9 | **AJAX / JSON**          | 4 AJAX endpoints all returning `Content-Type: application/json`: search, filter, add comment, delete comment, cost estimate. |
| 10 | **Git Contribution**    | Feature branch `feature/task4-22-49489-3`. Min 3 commits. PR merged into `main`. |

---

## API Reference

### `GET /api/posts/search.php?q={keyword}`
Returns array of matching approved posts (title or country).

### `GET /api/posts/filter.php?country=&genre[]=&cost_level=`
Returns filtered post array. All params optional.

### `POST /api/comments/add.php`
Body: `post_id`, `content`, `csrf_token`  
Auth: verified general user only.  
Returns: new comment object.

### `DELETE /api/comments/delete.php?id={id}`  
Header: `X-CSRF-Token: {token}`  
Auth: verified general user, must own comment.  
Returns: `{success: true, deleted_id: N}`

### `GET /api/cost/estimate.php?post_id=&travelers=&days=`
Returns: `{base_cost, currency, travelers, days, total, source}`

---

## Git Flow

```bash
git checkout -b feature/task4-22-49489-3
# ... work ...
git add .
git commit -m "feat: add PostModel with search and filter"
git commit -m "feat: AJAX comment add/delete endpoints with CSRF"
git commit -m "feat: cost calculator with client-side JS validation"
git push origin feature/task4-22-49489-3
# Open Pull Request into main
```
