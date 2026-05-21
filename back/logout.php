<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

app_log('/back/logout.php', 'logout', ['username' => $_SESSION['user']['username'] ?? null]);

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();
redirect_to('../front/login.php?msg=' . urlencode('Sesión cerrada correctamente.'));
