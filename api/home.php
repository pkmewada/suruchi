<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../data/content.php';

header('Content-Type: application/json; charset=utf-8');

function out(array $data, int $code = 200): void
{
    http_response_code($code);
    echo json_enc($data);
    exit;
}

/*
 * GET  api/home.php                 -> all sections (public)
 * GET  api/home.php?section=about   -> one section (public)
 * POST api/home.php?action=save     -> save section (admin only, JSON {section, data})
 * POST api/home.php?action=upload   -> upload image (admin only, multipart field "image")
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $all = home_all();
    $section = (string)($_GET['section'] ?? '');
    if ($section === '') {
        out(['ok' => true, 'data' => $all]);
    }
    if (!isset($all[$section])) {
        out(['ok' => false, 'error' => 'Section not found'], 404);
    }
    out(['ok' => true, 'section' => $section, 'data' => $all[$section]]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    out(['ok' => false, 'error' => 'Method not allowed'], 405);
}

if (empty($_SESSION['user_id'])) {
    out(['ok' => false, 'error' => 'Please login first'], 401);
}

$token = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $token)) {
    out(['ok' => false, 'error' => 'Session expired, refresh the page'], 419);
}

$action = (string)($_GET['action'] ?? 'save');

if ($action === 'upload') {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        out(['ok' => false, 'error' => 'Upload failed'], 400);
    }
    $file = $_FILES['image'];
    if ($file['size'] > 5 * 1024 * 1024) {
        out(['ok' => false, 'error' => 'Max size is 5 MB'], 400);
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        out(['ok' => false, 'error' => 'Only JPG, PNG, WEBP, GIF allowed'], 400);
    }
    $dir = __DIR__ . '/../assets/images/uploads/';
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        out(['ok' => false, 'error' => 'Cannot create assets/images/uploads folder'], 500);
    }
    $name = 'home-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
        out(['ok' => false, 'error' => 'Could not save file'], 500);
    }
    out(['ok' => true, 'path' => 'assets/images/uploads/' . $name]);
}

if ($action === 'save') {
    $body = json_decode((string)file_get_contents('php://input'), true);
    $section = is_array($body) ? (string)($body['section'] ?? '') : '';
    $data = is_array($body) ? ($body['data'] ?? null) : null;
    $requestedCleanup = is_array($body['cleanup'] ?? null) ? $body['cleanup'] : [];
    if (!array_key_exists($section, home_defaults()) || !is_array($data)) {
        out(['ok' => false, 'error' => 'Invalid section or data'], 422);
    }
    try {
        $previousData = home_all()[$section];
        home_save($section, $data);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        out(['ok' => false, 'error' => 'Database error'], 500);
    }
    try {
        $warnings = cleanup_replaced_uploads($previousData, $data, $requestedCleanup);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        $warnings = ['Unable to verify or remove replaced uploads.'];
    }
    out(['ok' => true, 'message' => 'Saved', 'cleanupWarnings' => $warnings]);
}

out(['ok' => false, 'error' => 'Unknown action'], 400);