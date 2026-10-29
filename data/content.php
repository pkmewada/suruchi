<?php
declare(strict_types=1);

require_once __DIR__ . '/page_content.php';

function home_defaults(): array
{
    return page_defaults()['home'];
}

function home_all(): array
{
    try {
        return page_all('home');
    } catch (Throwable $exception) {
        error_log($exception->getMessage());
        return home_defaults();
    }
}

function home_save(string $key, array $data): void
{
    page_save('home', $key, $data);
}

function uploaded_image_paths($value): array
{
    if (is_array($value)) {
        $paths = [];
        foreach ($value as $item) {
            $paths = array_merge($paths, uploaded_image_paths($item));
        }
        return array_values(array_unique($paths));
    }

    return is_string($value) && preg_match('~^assets/images/uploads/[A-Za-z0-9][A-Za-z0-9._-]*$~D', $value)
        ? [$value]
        : [];
}

function cleanup_replaced_uploads(array $previousData, array $newData, array $requestedCleanup = []): array
{
    $previousPaths = uploaded_image_paths($previousData);
    $newPaths = uploaded_image_paths($newData);
    $candidates = array_unique(array_merge(array_diff($previousPaths, $newPaths), $requestedCleanup));
    if (!$candidates) {
        return [];
    }

    $directory = realpath(__DIR__ . '/../assets/images/uploads');
    if ($directory === false) {
        return [];
    }

    $referenceCheck = db()->prepare('SELECT COUNT(*) FROM page_sections WHERE LOCATE(:path, content) > 0');
    $warnings = [];
    foreach ($candidates as $path) {
        $path = (string)$path;
        if (!uploaded_image_paths($path)) {
            continue;
        }

        $referenceCheck->execute(['path' => $path]);
        if ((int)$referenceCheck->fetchColumn() > 0) {
            continue;
        }

        $filename = substr($path, strlen('assets/images/uploads/'));
        $file = realpath($directory . DIRECTORY_SEPARATOR . $filename);
        if ($file === false || strtolower(dirname($file)) !== strtolower($directory) || !is_file($file)) {
            continue;
        }
        if (!unlink($file)) {
            $warnings[] = $filename;
            error_log('Unable to remove replaced uploaded image: ' . $file);
        }
    }

    return $warnings;
}
