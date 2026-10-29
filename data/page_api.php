<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/content.php';

function page_api(string $page): void
{
    header('Content-Type: application/json; charset=utf-8');
    $respond = static function (array $body, int $status = 200): void {
        http_response_code($status);
        echo json_enc($body);
        exit;
    };

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        try {
            $all = page_all($page);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $respond(['ok' => false, 'error' => 'Database unavailable. Import the project SQL file and try again.'], 500);
        }
        $section = (string)($_GET['section'] ?? '');
        if ($section === '') {
            $respond(['ok' => true, 'data' => $all]);
        }
        if (!isset($all[$section])) {
            $respond(['ok' => false, 'error' => 'Section not found'], 404);
        }
        $respond(['ok' => true, 'section' => $section, 'data' => $all[$section]]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: GET, POST');
        $respond(['ok' => false, 'error' => 'Method not allowed'], 405);
    }
    if (empty($_SESSION['user_id'])) {
        $respond(['ok' => false, 'error' => 'Please login first'], 401);
    }
    $token = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $token)) {
        $respond(['ok' => false, 'error' => 'Session expired, refresh the page'], 419);
    }

    $body = json_decode((string)file_get_contents('php://input'), true);
    $section = is_array($body) ? (string)($body['section'] ?? '') : '';
    $data = is_array($body) ? ($body['data'] ?? null) : null;
    $requestedCleanup = is_array($body['cleanup'] ?? null) ? $body['cleanup'] : [];
    if (!is_array($data) || !isset(page_defaults()[$page][$section])) {
        $respond(['ok' => false, 'error' => 'Invalid section or data'], 422);
    }
    try {
        $previousData = page_all($page)[$section];
        page_save($page, $section, $data);
    } catch (InvalidArgumentException $e) {
        $respond(['ok' => false, 'error' => 'Content does not match the section structure'], 422);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $respond(['ok' => false, 'error' => 'Database unavailable. Import the project SQL file and try again.'], 500);
    }
    try {
        $warnings = cleanup_replaced_uploads($previousData, $data, $requestedCleanup);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        $warnings = ['Unable to verify or remove replaced uploads.'];
    }
    $respond(['ok' => true, 'message' => 'Saved', 'cleanupWarnings' => $warnings]);
}
