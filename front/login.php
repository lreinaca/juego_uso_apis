<?php

declare(strict_types=1);

require_once __DIR__ . '/../back/helpers.php';

if (current_user()) {
    redirect_to('dashboard.php');
}

$error = trim($_GET['error'] ?? '');
$msg = trim($_GET['msg'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso | Solar Score Arena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body class="auth-bg">
    <main class="auth-shell">
        <section class="auth-card reveal-up">
            <p class="auth-kicker">Acceso protegido</p>
            <h1>Solar Score Arena</h1>
            <p class="auth-subtitle">Inicia sesión para jugar, guardar puntajes en base de datos y consultar reportes.</p>

            <?php if ($error !== ''): ?>
                <div class="alert alert-error"><?= esc($error) ?></div>
            <?php endif; ?>
            <?php if ($msg !== ''): ?>
                <div class="alert alert-ok"><?= esc($msg) ?></div>
            <?php endif; ?>

            <form action="../back/auth_login.php" method="POST" class="auth-form">
                <label for="username">Usuario</label>
                <input id="username" name="username" type="text" placeholder="Ej: neo.solar" required>

                <label for="password">Contraseña</label>
                <input id="password" name="password" type="password" placeholder="Tu contraseña" required>

                <button type="submit">Entrar al sistema</button>
            </form>

            <div class="demo-users">
                <h2>Usuarios precargados</h2>
                <ul>
                    <li>neo.solar / sol12345</li>
                    <li>luna.bit / luna12345</li>
                    <li>astro.admin / admin12345</li>
                </ul>
            </div>
        </section>
    </main>
</body>
</html>
