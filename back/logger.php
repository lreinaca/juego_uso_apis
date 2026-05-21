<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function app_log(string $route, string $event, array $context = []): void
{
    if (!is_dir(LOG_DIR)) {
        mkdir(LOG_DIR, 0775, true);
    }

    $entry = [
        'timestamp' => date('c'),
        'route' => $route,
        'event' => $event,
        'user' => $_SESSION['user']['username'] ?? null,
        'context' => $context,
    ];

    $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    file_put_contents(LOG_DIR . '/' . LOG_FILE_NAME, $line, FILE_APPEND | LOCK_EX);
}
