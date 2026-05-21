<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

// Redireccion HTTP con finalizacion inmediata del script.
function redirect_to(string $path): void
{
    header('Location: ' . $path);
    exit;
}

// Escape HTML para render seguro en vistas.
function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Devuelve el usuario autenticado de la sesion actual.
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

// Identifica si la sesion actual tiene privilegios de administrador.
function is_admin(): bool
{
    return (bool)($_SESSION['user']['is_admin'] ?? false);
}

// Guardia de autenticacion para pantallas y endpoints privados.
function require_login(): void
{
    if (!isset($_SESSION['user'])) {
        app_log('/auth', 'blocked_unauthenticated');
        redirect_to('../front/login.php?error=' . urlencode('Inicia sesión para continuar.'));
    }
}

// Salida JSON uniforme para endpoints internos.
function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}
