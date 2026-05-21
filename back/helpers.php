<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function redirect_to(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!isset($_SESSION['user'])) {
        app_log('/auth', 'blocked_unauthenticated');
        redirect_to('../front/login.php?error=' . urlencode('Inicia sesión para continuar.'));
    }
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}
