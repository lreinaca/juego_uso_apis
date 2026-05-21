<?php

declare(strict_types=1);

require_once __DIR__ . '/../back/helpers.php';
require_login();

$user = current_user();
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
                    <p class="brand-kicker">Aplicación dinámica + API pública</p>
                    <h1 class="brand-title">Solar Score Arena</h1>
                </div>
            </div>

            <div class="connected-user-pill">
                <span class="pill-label">Usuario activo</span>
                <strong class="pill-value" id="navbarUser"><?= esc($user['full_name']) ?></strong>
            </div>

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

            <article class="panel reveal-up" id="reportsPanel">
                <div class="section-head">
                    <h2 class="section-title">Reportes de puntajes</h2>
                    <select id="userFilter" class="soft-select"></select>
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
        </section>
    </main>

    <script>
        window.APP_BOOTSTRAP = {
            roundsPerGame: <?= (int)ROUNDS_PER_GAME ?>,
            userId: <?= (int)$user['id'] ?>,
            userName: "<?= esc($user['full_name']) ?>"
        };
    </script>
    <script src="scripts/app.js"></script>
</body>
</html>
