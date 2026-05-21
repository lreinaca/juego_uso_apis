(() => {
    // Estado de juego en memoria del navegador.
    const state = {
        score: 0,
        rounds: 0,
        streak: 0,
        planets: [],
        selectedQuestions: [],
        currentQuestion: 0,
    };

    const roundsTarget = Number(window.APP_BOOTSTRAP?.roundsPerGame || 5);
    // Flag entregado por PHP para habilitar filtro multiusuario solo en admin.
    const isAdmin = Boolean(window.APP_BOOTSTRAP?.isAdmin);

    const scoreValue = document.querySelector('#scoreValue');
    const roundsValue = document.querySelector('#roundsValue');
    const streakValue = document.querySelector('#streakValue');
    const gameContainer = document.querySelector('#gameContainer');
    const startGameBtn = document.querySelector('#startGameBtn');
    const userFilter = document.querySelector('#userFilter');

    const summaryCards = document.querySelector('#summaryCards');
    const historyBody = document.querySelector('#historyBody');
    const weekBody = document.querySelector('#weekBody');
    const monthBody = document.querySelector('#monthBody');
    const allTimeBody = document.querySelector('#allTimeBody');

    const hasGameArea = Boolean(scoreValue && roundsValue && streakValue && gameContainer);
    const hasReportsArea = Boolean(summaryCards && historyBody && weekBody && monthBody && allTimeBody && userFilter);

    // Escape HTML defensivo para render de datos dinamicos.
    const esc = (v) => String(v ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');

    const shuffle = (arr) => {
        const copy = [...arr];
        for (let i = copy.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [copy[i], copy[j]] = [copy[j], copy[i]];
        }
        return copy;
    };

    // Reinicia el estado de una partida completa.
    const resetGame = () => {
        state.score = 0;
        state.rounds = 0;
        state.streak = 0;
        state.currentQuestion = 0;
        state.selectedQuestions = [];
        updateScoreBoard();
    };

    const updateScoreBoard = () => {
        if (!hasGameArea) {
            return;
        }
        scoreValue.textContent = state.score;
        roundsValue.textContent = `${state.rounds}/${roundsTarget}`;
        streakValue.textContent = state.streak;
    };

    // Pinta una ronda del quiz y procesa la respuesta del jugador.
    const renderQuestion = () => {
        const current = state.selectedQuestions[state.currentQuestion];
        if (!current) {
            gameContainer.innerHTML = '<p>No hay pregunta disponible.</p>';
            return;
        }

        const options = shuffle(state.planets).map((p) => p.name);

        gameContainer.innerHTML = `
            <div class="quiz-card">
                <p><strong>Pregunta ${state.currentQuestion + 1} de ${roundsTarget}</strong></p>
                <ul class="clues-list">
                    ${current.clues.map((clue) => `<li>${esc(clue)}</li>`).join('')}
                </ul>
                <label for="planetSelect">Selecciona el planeta correcto</label>
                <select id="planetSelect" class="soft-select">
                    <option value="">Elige una opción</option>
                    ${shuffle(options).map((name) => `<option value="${esc(name)}">${esc(name)}</option>`).join('')}
                </select>
                <button id="answerBtn" class="btn-primary" style="margin-top:0.7rem">Responder</button>
                <div id="feedback" style="margin-top:0.65rem;color:#475569"></div>
            </div>
        `;

        document.querySelector('#answerBtn')?.addEventListener('click', () => {
            const selected = document.querySelector('#planetSelect')?.value || '';
            const feedback = document.querySelector('#feedback');
            if (!selected) {
                feedback.textContent = 'Selecciona una opción antes de responder.';
                return;
            }

            state.rounds += 1;
            if (selected === current.name) {
                const points = 10 + (state.streak * 2);
                state.score += points;
                state.streak += 1;
                feedback.textContent = `Correcto. +${points} puntos.`;
            } else {
                state.streak = 0;
                feedback.textContent = `Incorrecto. Era ${current.name}.`;
            }

            updateScoreBoard();

            setTimeout(() => {
                state.currentQuestion += 1;
                if (state.currentQuestion >= roundsTarget) {
                    finishGame();
                    return;
                }
                renderQuestion();
            }, 850);
        });
    };

    // Cierra partida, persiste puntaje y refresca reportes.
    const finishGame = async () => {
        gameContainer.innerHTML = '<div class="quiz-card"><p>Guardando puntaje...</p></div>';

        try {
            const resp = await fetch('../api/save_score.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ score: state.score, rounds: state.rounds }),
            });
            const data = await resp.json();

            gameContainer.innerHTML = `
                <div class="quiz-card">
                    <h3>Partida completada</h3>
                    <p>Puntaje final: <strong>${state.score}</strong></p>
                    <p>${esc(data.message || '')}</p>
                    <button id="restartBtn" class="btn-primary">Jugar otra vez</button>
                </div>
            `;

            document.querySelector('#restartBtn')?.addEventListener('click', startGame);
            await loadReports();
        } catch (error) {
            gameContainer.innerHTML = '<div class="quiz-card"><p>No se pudo guardar el puntaje.</p></div>';
        }
    };

    // Arranca una nueva partida consultando planetas desde la API interna.
    const startGame = async () => {
        if (!hasGameArea) {
            return;
        }

        resetGame();
        gameContainer.innerHTML = '<div class="quiz-card"><p>Cargando preguntas desde API pública...</p></div>';

        try {
            // El quiz siempre arranca con datos del endpoint interno conectado a la API publica.
            const resp = await fetch('../api/game_data.php', { cache: 'no-store' });
            const payload = await resp.json();

            if (!payload.ok || !Array.isArray(payload.planets) || payload.planets.length < 4) {
                gameContainer.innerHTML = '<div class="quiz-card"><p>La API no devolvió suficientes datos para jugar.</p></div>';
                return;
            }

            state.planets = payload.planets;
            state.selectedQuestions = shuffle(payload.planets).slice(0, roundsTarget);
            renderQuestion();
        } catch (error) {
            gameContainer.innerHTML = '<div class="quiz-card"><p>Fallo de conexión con la API pública.</p></div>';
        }
    };

    // Helper de render tabular para todas las secciones de reportes.
    const rows = (items, cells) => {
        if (!items || items.length === 0) {
            return '<tr><td colspan="5">Sin datos disponibles.</td></tr>';
        }
        return items.map((item) => `<tr>${cells(item).map((c) => `<td>${esc(c)}</td>`).join('')}</tr>`).join('');
    };

    // Hidrata panel de reportes consumiendo endpoint consolidado.
    const loadReports = async (forceUserId) => {
        if (!hasReportsArea) {
            return;
        }

        const chosenUser = Number(
            isAdmin
                ? (forceUserId || userFilter.value || window.APP_BOOTSTRAP.userId)
                : window.APP_BOOTSTRAP.userId
        );

        try {
            const resp = await fetch(`../api/report_data.php?userId=${encodeURIComponent(chosenUser)}`, { cache: 'no-store' });
            const payload = await resp.json();

            if (!payload.ok) {
                summaryCards.innerHTML = '<article class="report-card"><span>Error</span><strong>No fue posible cargar reportes</strong></article>';
                return;
            }

            if (isAdmin) {
                // Solo admin puede alternar usuario para ver reportes ajenos.
                userFilter.hidden = false;
                userFilter.innerHTML = (payload.users || [])
                    .map((u) => `<option value="${Number(u.id)}" ${Number(u.id) === Number(payload.selectedUserId) ? 'selected' : ''}>${esc(u.full_name)}</option>`)
                    .join('');
            } else {
                // Usuario normal: sin filtro, dashboard personal fijo.
                userFilter.hidden = true;
            }

            const summary = payload.summary || {};
            summaryCards.innerHTML = `
                <article class="report-card"><span>Total partidas</span><strong>${Number(summary.totalGames || 0)}</strong></article>
                <article class="report-card"><span>Promedio</span><strong>${Number(summary.avgScore || 0).toFixed(2)}</strong></article>
                <article class="report-card"><span>Mejor puntaje</span><strong>${Number(summary.bestScore || 0)}</strong></article>
            `;

            historyBody.innerHTML = rows(payload.history, (h) => [h.full_name, h.score, h.rounds, h.created_at]);
            weekBody.innerHTML = rows(payload.weekly, (w) => [w.period, w.full_name, w.games, w.avg_score, w.max_score]);
            monthBody.innerHTML = rows(payload.monthly, (m) => [m.period, m.full_name, m.games, m.avg_score, m.max_score]);
            allTimeBody.innerHTML = rows(payload.allTime, (a) => [a.full_name, a.games, a.avg_score, a.best_score]);
        } catch (error) {
            summaryCards.innerHTML = '<article class="report-card"><span>Error</span><strong>No fue posible cargar reportes</strong></article>';
        }
    };

    // Listeners principales de UI.
    if (hasReportsArea && isAdmin) {
        userFilter?.addEventListener('change', () => loadReports(userFilter.value));
    }
    if (hasGameArea) {
        startGameBtn?.addEventListener('click', startGame);
    }

    document.querySelectorAll('.reveal-up').forEach((el, i) => {
        setTimeout(() => el.classList.add('visible'), i * 90);
    });

    if (hasReportsArea) {
        loadReports(window.APP_BOOTSTRAP.userId);
    }
})();
