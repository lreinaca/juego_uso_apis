<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

// Endpoint de autenticacion por formulario (solo POST).
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('../front/login.php');
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    app_log('/back/auth_login.php', 'login_validation_error', ['username' => $username]);
    redirect_to('../front/login.php?error=' . urlencode('Debes ingresar usuario y contraseña.'));
}

// Busca usuario por username y valida hash de password.
try {
    $stmt = db()->prepare('SELECT id, username, full_name, password_hash FROM users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();
} catch (Throwable $e) {
    app_log('/back/auth_login.php', 'login_db_error', ['error' => $e->getMessage()]);
    redirect_to('../front/login.php?error=' . urlencode('No se pudo conectar a la base de datos. Revisa DB_USER/DB_PASSWORD en back/conexion.php.'));
}

if (!$user || !password_verify($password, $user['password_hash'])) {
    app_log('/back/auth_login.php', 'login_failed', ['username' => $username]);
    redirect_to('../front/login.php?error=' . urlencode('Credenciales inválidas.'));
}

// Persistencia de sesion utilizada por vistas y endpoints protegidos.
$_SESSION['user'] = [
    'id' => (int)$user['id'],
    'username' => $user['username'],
    'full_name' => $user['full_name'],
    // Regla simple actual: astro.admin se considera administrador.
    'is_admin' => $user['username'] === 'astro.admin',
];

app_log('/back/auth_login.php', 'login_success', ['username' => $user['username']]);
redirect_to('../front/dashboard.php');
