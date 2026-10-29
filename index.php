<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/data/content.php';
require_once __DIR__ . '/includes/router.php';

$route = app_route();
$pages = [
    'home' => 'home.php',
    'about' => 'about.php',
    '4fs' => '4fs.php',
    'design-research' => 'design-research.php',
    'product' => 'product.php',
    'contact' => 'contact.php',
];

if (!isset($pages[$route])) {
    http_response_code(404);
    require __DIR__ . '/data/pages/404.php';
    exit;
}

require __DIR__ . '/data/pages/' . $pages[$route];
