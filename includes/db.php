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

function home_defaults(): array
{
    return require __DIR__ . '/home_defaults.php';
}

/**
 * All home sections. Missing sections are saved into DB from defaults
 * (so existing index.php content is stored first, then admin can change it).
 * If DB is down, defaults are returned so the site never breaks.
 */
function home_all(): array
{
    $defaults = home_defaults();
    try {
        $pdo  = db();
        $rows = $pdo->query('SELECT section_key, content FROM home_sections')->fetchAll(PDO::FETCH_KEY_PAIR);
        $ins  = $pdo->prepare('INSERT IGNORE INTO home_sections (section_key, content) VALUES (?, ?)');
        $out  = [];
        foreach ($defaults as $key => $def) {
            if (!isset($rows[$key])) {
                $ins->execute([$key, json_enc($def)]);
                $out[$key] = $def;
                continue;
            }
            $saved = json_decode($rows[$key], true);
            $out[$key] = is_array($saved) ? array_merge($def, $saved) : $def;
        }
        return $out;
    } catch (Throwable $e) {
        return $defaults;
    }
}

function home_save(string $key, array $data): void
{
    $st = db()->prepare(
        'INSERT INTO home_sections (section_key, content) VALUES (:k, :c)
         ON DUPLICATE KEY UPDATE content = VALUES(content)'
    );
    $st->execute(['k' => $key, 'c' => json_enc($data)]);
}