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
<link rel="stylesheet" href="../assets/css/dashboard.css">

</head>
<body>
<div class="layout">
  <aside>
    <h1>Suruchi Creation</h1><small>Content dashboard</small>
    <nav id="nav" aria-label="Page sections"></nav>
    <div class="side-links">
      <a href="../" target="_blank" rel="noopener">View website &#8599;</a>
      <a href="logout.php">Logout (<?= e($_SESSION['user_name'] ?? 'Admin') ?>)</a>
    </div>
  </aside>
  <main>
    <div class="top"><h2 id="title">Dashboard</h2></div>
    <p class="hint">Choose a page and section, update its content, then save. Changes appear on the website immediately.</p>
    <div id="form"></div>
    <div class="save-bar"><button class="btn save-btn" id="saveBtn">Save changes</button><span id="msg" role="status"></span></div>
  </main>
</div>
<script type="application/json" id="dashboard-data"><?= json_encode(['csrf' => $_SESSION['csrf_token'], 'data' => $data, 'pageLabels' => $pageLabels], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<script src="../assets/js/dashboard.js" defer></script>

</body>
</html>