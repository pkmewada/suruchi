<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../data/content.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$pages = [];
foreach (page_defaults() as $key => $_sections) {
    $pages[$key] = page_all($key);
}
$data = $pages;
$pageLabels = [
    'home' => 'Home',
    'about' => 'About',
    '4fs' => '4FS / Services',
    'design-research' => 'Design Research',
    'product' => 'Product',
    'contact' => 'Contact',
];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard | Suruchi Creation</title>
<link rel="stylesheet" href="../assets/css/dashboard.css?v=20261030">

</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <a class="brand" href="dashboard.php" aria-label="Suruchi Creation dashboard">
      <span class="brand-mark">S</span>
      <span class="brand-copy"><strong>Suruchi Creation</strong><small>Content studio</small></span>
    </a>
    <div class="sidebar-label">YOUR PAGES</div>
    <nav id="page-nav" class="page-nav" aria-label="Website pages"></nav>
    <div class="side-links">
      <div class="admin-profile">
        <span class="avatar"><?= e(strtoupper(substr((string)($_SESSION['user_name'] ?? 'A'), 0, 1))) ?></span>
        <span class="admin-name"><?= e($_SESSION['user_name'] ?? 'Admin') ?><small>Administrator</small></span>
        <a class="logout-link" href="logout.php" aria-label="Logout" title="Logout">↗</a>
      </div>
    </div>
  </aside>
  <main>
    <header class="top">
      <div>
        <div class="eyebrow">CONTENT MANAGEMENT</div>
        <h1 id="title">Dashboard</h1>
        <p class="hint">Edit your page content and save when you are ready.</p>
      </div>
      <div class="top-actions">
        <label class="section-search">
          <span aria-hidden="true">⌕</span>
          <input id="section-filter" type="search" placeholder="Filter sections" autocomplete="off">
        </label>
        <div class="secure-chip"><span></span> Changes save securely</div>
      </div>
    </header>
    <nav id="section-nav" class="section-nav" aria-label="Page sections"></nav>
    <p id="empty-sections" class="empty-sections" hidden>No sections match your search.</p>
    <div id="form"></div>
    <footer class="save-bar">
      <span class="save-note">Your updates appear on the website after saving.</span>
      <span id="msg" role="status"></span>
      <button class="btn save-btn" id="saveBtn"><span>Save changes</span><span aria-hidden="true">→</span></button>
    </footer>
  </main>
</div>
<script type="application/json" id="dashboard-data"><?= json_encode(['csrf' => $_SESSION['csrf_token'], 'data' => $data, 'pageLabels' => $pageLabels], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<script src="../assets/js/dashboard.js?v=20261029" defer></script>

</body>
</html>