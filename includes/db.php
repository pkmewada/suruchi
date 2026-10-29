<?php
declare(strict_types=1);

/*
 * Database connection + helpers.  Update values for your XAMPP / hosting.
 */
const DB_HOST = 'localhost';
const DB_NAME = 'suruchi_creation';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
    return $pdo;
}

/** Escape for HTML output */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/** Escape + turn *word* into highlighted span (used in testimonials) */
function hl($value): string
{
    return preg_replace('/\*(.+?)\*/u', '<span class="text-decoration-line-bottom-medium fw-700">$1</span>', e($value));
}

function json_enc($data): string
{
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
