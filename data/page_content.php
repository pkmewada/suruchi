<?php
declare(strict_types=1);

function page_defaults(): array
{
    return require __DIR__ . '/page_defaults.php';
}

function page_all(string $page): array
{
    $defaults = page_defaults();
    if (!isset($defaults[$page])) {
        throw new InvalidArgumentException('Unknown page');
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT section_key, content FROM page_sections WHERE page_key = :page');
    $stmt->execute(['page' => $page]);
    $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $insert = $pdo->prepare('INSERT IGNORE INTO page_sections (page_key, section_key, content) VALUES (:page, :section, :content)');
    $result = [];

    foreach ($defaults[$page] as $section => $default) {
        if (!isset($rows[$section])) {
            $result[$section] = $default;
            $insert->execute(['page' => $page, 'section' => $section, 'content' => json_enc($default)]);
            continue;
        }
        $saved = json_decode($rows[$section], true);
        if ($page === 'home') {
            $result[$section] = is_array($saved) ? array_merge($default, $saved) : $default;
        } else {
            $result[$section] = is_array($saved) && page_data_matches($default, $saved) ? $saved : $default;
        }
    }
    return $result;
}

function page_save(string $page, string $section, array $data): void
{
    $defaults = page_defaults();
    if (!isset($defaults[$page][$section])) {
        throw new InvalidArgumentException('Unknown page section');
    }
    if ($page !== 'home' && !page_data_matches($defaults[$page][$section], $data)) {
        throw new InvalidArgumentException('Page content does not match the section structure');
    }

    $stmt = db()->prepare(
        'INSERT INTO page_sections (page_key, section_key, content) VALUES (:page, :section, :content)
         ON DUPLICATE KEY UPDATE content = VALUES(content)'
    );
    $stmt->execute(['page' => $page, 'section' => $section, 'content' => json_enc($data)]);
}

function page_data_matches($template, $value): bool
{
    if (!is_array($template)) {
        return is_string($value) || is_int($value) || is_float($value) || is_bool($value);
    }
    if (!is_array($value) || count($template) !== count($value)) {
        return false;
    }
    if (array_keys($template) !== array_keys($value)) {
        return false;
    }
    foreach ($template as $key => $childTemplate) {
        if (!page_data_matches($childTemplate, $value[$key])) {
            return false;
        }
    }
    return true;
}
