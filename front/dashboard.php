<?php

declare(strict_types=1);

require_once __DIR__ . '/../back/helpers.php';
require_login();

// Vista principal autenticada: shell visual del juego.
$user = current_user();
$isAdmin = is_admin();
app_log('/front/dashboard.php', 'open_dashboard');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Solar Score Arena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <header class="top-navbar">
        <div class="nav-shell">
            <div class="brand-wrap">
                <div class="brand-icon">SS</div>
                <div>
                    <p class="brand-kicker"><?= $isAdmin ? 'Panel admin multiusuario' : 'Dashboard personal' ?></p>
                    <h1 class="brand-title">Solar Score Arena<?= $isAdmin ? ' - Admin' : '' ?></h1>
                </div>
            </div>

            <div class="connected-user-pill">
                <span class="pill-label">Usuario activo</span>
                <strong class="pill-value" id="navbarUser"><?= esc($user['full_name']) ?></strong>
            </div>

            <a class="logout-btn" href="reports.php">Reportes</a>
            <a class="logout-btn" href="../back/logout.php">Cerrar sesión</a>
        </div>
    </header>

    <main class="page-shell">
        <section class="hero-card reveal-up">
            <h2 class="hero-title">Adivina el planeta y acumula puntaje</h2>
            <p class="hero-subtitle">El juego consume una API pública del sistema solar. Al finalizar cada partida, el puntaje se registra en base de datos con fecha.</p>
            <div class="metrics-row">
                <article class="metric-card tone-score">
                    <span class="metric-label">Puntaje actual</span>
                    <h3 class="metric-value" id="scoreValue">0</h3>
                </article>
                <article class="metric-card tone-rounds">
                    <span class="metric-label">Rondas</span>
                    <h3 class="metric-value" id="roundsValue">0/<?= ROUNDS_PER_GAME ?></h3>
                </article>
                <article class="metric-card tone-streak">
                    <span class="metric-label">Racha</span>
                    <h3 class="metric-value" id="streakValue">0</h3>
                </article>
            </div>
        </section>

        <section class="content-grid">
            <article class="panel reveal-up" id="gamePanel">
                <div class="section-head">
                    <h2 class="section-title">Juego dinámico</h2>
                    <button id="startGameBtn" class="btn-primary">Iniciar partida</button>
                </div>
                <div id="gameContainer" class="game-placeholder">Presiona "Iniciar partida" para cargar pistas desde la API.</div>
            </article>

            <article class="panel reveal-up visible">
                <div class="section-head">
                    <h2 class="section-title">Reportes de puntajes</h2>
                    <a class="btn-primary" href="reports.php">Ir a reportes</a>
                </div>
                <p class="hero-subtitle">Consulta tu historial en una página dedicada. Si eres administrador, podrás filtrar por persona.</p>
            </article>
        </section>
    </main>

    <script>
        // Datos iniciales inyectados por PHP para el runtime JS.
        window.APP_BOOTSTRAP = {
            roundsPerGame: <?= (int)ROUNDS_PER_GAME ?>,
            userId: <?= (int)$user['id'] ?>,
            userName: "<?= esc($user['full_name']) ?>",
            isAdmin: <?= $isAdmin ? 'true' : 'false' ?>
        };
    </script>
    <script src="scripts/app.js"></script>
</body>
</html>
