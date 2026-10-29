<?php
declare(strict_types=1);

$scriptPath = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$appBase = rtrim(str_replace('\\', '/', dirname($scriptPath)), '/.');
if ($appBase === '/') {
    $appBase = '';
}

if (!defined('APP_BASE')) {
    define('APP_BASE', $appBase);
}

function app_url(string $path = ''): string
{
    return APP_BASE . '/' . ltrim($path, '/');
}

function app_href(string $path): string
{
    $path = trim($path);
    if ($path === '' || $path[0] === '#' || preg_match('/^(?:[a-z][a-z0-9+.-]*:|\\/\\/)/i', $path)) {
        return $path;
    }

    $routes = [
        'demo-branding-agency-contact.html' => 'contact',
        'demo-branding-agency-about.html' => 'about',
        'demo-branding-agency-services.html' => '4fs',
        'demo-branding-agency-single-project-slider.html' => 'product',
    ];
    $path = $routes[$path] ?? $path;

    return app_url($path);
}

function app_route(): string
{
    $route = trim((string)($_GET['page'] ?? ''), '/');
    if ($route === '') {
        $requestPath = parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
        $route = trim(substr((string)$requestPath, strlen(APP_BASE)), '/');
    }

    return $route === '' ? 'home' : $route;
}
