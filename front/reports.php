<?php

declare(strict_types=1);

require_once __DIR__ . '/../back/helpers.php';
require_login();

$user = current_user();
$isAdmin = is_admin();
app_log('/front/reports.php', 'open_reports');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes | Solar Score Arena</title>
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
                    <h1 class="brand-title">Reportes de puntajes</h1>
                </div>
            </div>

            <div class="connected-user-pill">
                <span class="pill-label">Usuario activo</span>
                <strong class="pill-value"><?= esc($user['full_name']) ?></strong>
            </div>

            <a class="logout-btn" href="dashboard.php">Juego</a>
            <a class="logout-btn" href="../back/logout.php">Cerrar sesión</a>
        </div>
    </header>

    <main class="page-shell">
        <article class="panel reveal-up visible" id="reportsPanel">
            <div class="section-head">
                <h2 class="section-title"><?= $isAdmin ? 'Reportes globales' : 'Mis reportes' ?></h2>
                <select id="userFilter" class="soft-select" <?= $isAdmin ? '' : 'hidden' ?>></select>
            </div>

            <div class="report-cards" id="summaryCards"></div>

            <h3 class="table-title">Historial por persona</h3>
            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Puntaje</th>
                            <th>Rondas</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody"></tbody>
                </table>
            </div>

            <h3 class="table-title">Agrupado por semana</h3>
            <div class="table-shell"><table><thead><tr><th>Periodo</th><th>Usuario</th><th>Partidas</th><th>Promedio</th><th>Máximo</th></tr></thead><tbody id="weekBody"></tbody></table></div>

            <h3 class="table-title">Agrupado por mes</h3>
            <div class="table-shell"><table><thead><tr><th>Periodo</th><th>Usuario</th><th>Partidas</th><th>Promedio</th><th>Máximo</th></tr></thead><tbody id="monthBody"></tbody></table></div>

            <h3 class="table-title">Todo el tiempo</h3>
            <div class="table-shell"><table><thead><tr><th>Usuario</th><th>Partidas</th><th>Promedio</th><th>Mejor</th></tr></thead><tbody id="allTimeBody"></tbody></table></div>
        </article>
    </main>

    <script>
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
