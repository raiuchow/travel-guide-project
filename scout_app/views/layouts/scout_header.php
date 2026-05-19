<?php
/**
 * Scout Layout Header
 * Shared header for all scout pages
 */

$user = currentUser();
$pageTitle = $pageTitle ?? 'Scout Panel';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> · ScoutHub</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #080c10; --surface: #0f1620; --border: #1e2d3d;
  --accent: #00d4aa; --accent2: #ff6b35; --danger: #e63946;
  --warning: #f4a261; --text: #e8edf2; --muted: #6b7f94;
  --radius: 12px; --font-head: 'Syne', sans-serif; --font-body: 'DM Sans', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
  font-family: var(--font-body); background: var(--bg); color: var(--text);
  min-height: 100vh; display: flex; line-height: 1.6;
}
.sidebar {
  width: 240px; min-height: 100vh; background: var(--surface);
  border-right: 1px solid var(--border); display: flex; flex-direction: column;
  padding: 1.5rem 1rem; position: fixed; top: 0; left: 0; z-index: 100;
}
.logo {
  font-family: var(--font-head); font-size: 1.4rem; font-weight: 800;
  color: var(--accent); letter-spacing: -0.03em; padding: 0.5rem 0.75rem 1.5rem;
  border-bottom: 1px solid var(--border); margin-bottom: 1.5rem;
}
.logo span { color: var(--accent2); }
.nav-label {
  font-size: 0.65rem; font-weight: 600; letter-spacing: 0.12em;
  text-transform: uppercase; color: var(--muted); padding: 0 0.75rem 0.5rem; margin-top: 0.5rem;
}
.nav a {
  display: flex; align-items: center; gap: 0.6rem; padding: 0.6rem 0.75rem;
  border-radius: 8px; color: var(--muted); text-decoration: none;
  font-size: 0.9rem; font-weight: 400; transition: all 0.2s; margin-bottom: 2px;
}
.nav a:hover, .nav a.active {
  background: rgba(0,212,170,0.08); color: var(--accent);
}
.nav a .icon { font-size: 1rem; width: 20px; text-align: center; }
.sidebar-footer {
  margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border);
}
.user-chip {
  display: flex; align-items: center; gap: 0.6rem; padding: 0.6rem 0.75rem;
  border-radius: 8px; background: rgba(255,255,255,0.03);
}
.avatar {
  width: 32px; height: 32px; border-radius: 50%;
  background: linear-gradient(135deg, var(--accent), var(--accent2));
  display: flex; align-items: center; justify-content: center;
  font-size: 0.75rem; font-weight: 700; color: #000; flex-shrink: 0;
}
.user-info .name { font-size: 0.82rem; font-weight: 500; color: var(--text); }
.user-info .role { font-size: 0.7rem; color: var(--accent); }
.main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
.topbar {
  height: 60px; background: var(--surface); border-bottom: 1px solid var(--border);
  display: flex; align-items: center; padding: 0 2rem; gap: 1rem;
}
.topbar h1 { font-family: var(--font-head); font-size: 1.1rem; font-weight: 700; flex: 1; }
.badge-verified {
  font-size: 0.7rem; background: rgba(0,212,170,0.12); color: var(--accent);
  border: 1px solid rgba(0,212,170,0.25); border-radius: 100px;
  padding: 0.2rem 0.7rem; font-weight: 500;
}
.content { padding: 2rem; flex: 1; }
.card {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 1.5rem; margin-bottom: 1.5rem;
}
.card-title {
  font-family: var(--font-head); font-size: 1rem; font-weight: 700;
  color: var(--text); margin-bottom: 1.25rem; display: flex;
  align-items: center; gap: 0.5rem;
}
.card-title .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent); }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-grid.full { grid-template-columns: 1fr; }
.form-group { display: flex; flex-direction: column; gap: 0.4rem; }
.form-group.span2 { grid-column: span 2; }
label {
  font-size: 0.78rem; font-weight: 500; color: var(--muted); letter-spacing: 0.02em;
}
input[type=text], input[type=email], input[type=password], input[type=file], textarea, select {
  background: rgba(255,255,255,0.04); border: 1px solid var(--border);
  border-radius: 8px; color: var(--text); font-family: var(--font-body);
  font-size: 0.9rem; padding: 0.65rem 0.9rem; width: 100%;
  transition: border-color 0.2s, box-shadow 0.2s; outline: none;
}
input:focus, textarea:focus, select:focus {
  border-color: var(--accent); box-shadow: 0 0 0 3px rgba(0,212,170,0.1);
}
select option { background: var(--surface); }
textarea { resize: vertical; min-height: 90px; }
.btn {
  display: inline-flex; align-items: center; gap: 0.4rem;
  padding: 0.6rem 1.2rem; border-radius: 8px; font-family: var(--font-body);
  font-size: 0.85rem; font-weight: 500; cursor: pointer; border: none;
  transition: all 0.2s; text-decoration: none;
}
.btn-primary { background: var(--accent); color: #000; }
.btn-primary:hover { background: #00b894; transform: translateY(-1px); }
.btn-danger { background: transparent; border: 1px solid var(--danger); color: var(--danger); }
.btn-danger:hover { background: var(--danger); color: #fff; }
.btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--muted); }
.btn-ghost:hover { border-color: var(--accent); color: var(--accent); }
.btn-warning { background: transparent; border: 1px solid var(--warning); color: var(--warning); }
.btn-warning:hover { background: var(--warning); color: #000; }
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th {
  font-size: 0.7rem; font-weight: 600; letter-spacing: 0.08em;
  text-transform: uppercase; color: var(--muted); padding: 0.75rem 1rem;
  text-align: left; border-bottom: 1px solid var(--border);
}
td {
  padding: 0.9rem 1rem; font-size: 0.875rem;
  border-bottom: 1px solid rgba(30,45,61,0.5); vertical-align: middle;
}
tr:last-child td { border-bottom: none; }
tr:hover td { background: rgba(255,255,255,0.015); }
.status {
  display: inline-flex; align-items: center; gap: 0.3rem;
  padding: 0.25rem 0.7rem; border-radius: 100px;
  font-size: 0.72rem; font-weight: 600; letter-spacing: 0.04em;
}
.status::before { content:''; width:5px; height:5px; border-radius:50%; }
.status-pending { background:rgba(244,162,97,0.12); color:var(--warning); border:1px solid rgba(244,162,97,0.25); }
.status-pending::before { background: var(--warning); }
.status-approved { background:rgba(0,212,170,0.12); color:var(--accent); border:1px solid rgba(0,212,170,0.25); }
.status-approved::before { background: var(--accent); }
.status-rejected { background:rgba(230,57,70,0.12); color:var(--danger); border:1px solid rgba(230,57,70,0.25); }
.status-rejected::before { background: var(--danger); }
.alert {
  padding: 0.8rem 1rem; border-radius: 8px; font-size: 0.875rem;
  margin-bottom: 1rem; border-left: 3px solid;
}
.alert-success { background:rgba(0,212,170,0.08); border-color:var(--accent); color:var(--accent); }
.alert-error { background:rgba(230,57,70,0.08); border-color:var(--danger); color:var(--danger); }
.modal-overlay {
  display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7);
  z-index: 500; align-items: center; justify-content: center;
}
.modal-overlay.active { display: flex; }
.modal {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 2rem; width: 90%; max-width: 520px;
  animation: modalIn 0.2s ease;
}
@keyframes modalIn {
  from { opacity:0; transform:scale(0.95) translateY(10px); }
  to { opacity:1; transform:scale(1) translateY(0); }
}
.modal-title { font-family: var(--font-head); font-size: 1.1rem; font-weight: 700; margin-bottom: 0.75rem; }
.modal-actions { display: flex; gap: 0.75rem; margin-top: 1.25rem; justify-content: flex-end; }
.empty-state { text-align: center; padding: 3rem 1rem; color: var(--muted); }
.empty-state .icon { font-size: 2.5rem; margin-bottom: 0.75rem; }
.empty-state p { font-size: 0.9rem; }
.actions { display:flex; gap:0.5rem; flex-wrap:wrap; }
.hint { font-size: 0.75rem; color: var(--muted); margin-top: 0.25rem; }
.thumb-preview {
  display: none; width: 80px; height: 80px; object-fit: cover;
  border-radius: 6px; border: 1px solid var(--border); margin-top: 0.5rem;
}
</style>
</head>
<body>

<aside class="sidebar">
  <div class="logo">Scout<span>Hub</span></div>
  <div class="nav-label">Scout Menu</div>
  <nav class="nav">
    <a href="/scout/my_requests.php" class="<?= basename($_SERVER['PHP_SELF'])==='my_requests.php' ? 'active':'' ?>">
      <span class="icon">📋</span> My Requests
    </a>
    <a href="/scout/create_request.php" class="<?= basename($_SERVER['PHP_SELF'])==='create_request.php' ? 'active':'' ?>">
      <span class="icon">✚</span> New Request
    </a>
    <a href="/scout/approved_posts.php" class="<?= basename($_SERVER['PHP_SELF'])==='approved_posts.php' ? 'active':'' ?>">
      <span class="icon">✅</span> Approved Posts
    </a>
  </nav>
  <div class="sidebar-footer">
    <div class="user-chip">
      <div class="avatar"><?= strtoupper(substr($user['name'],0,1)) ?></div>
      <div class="user-info">
        <div class="name"><?= e($user['name']) ?></div>
        <div class="role">Scout · Verified</div>
      </div>
    </div>
    <a href="/logout.php" class="btn btn-ghost" style="width:100%;margin-top:.75rem;justify-content:center;">
      ⬿ Logout
    </a>
  </div>
</aside>

<div class="main">
  <div class="topbar">
    <h1><?= e($pageTitle) ?></h1>
    <span class="badge-verified">✓ Verified Scout</span>
  </div>
  <div class="content">
