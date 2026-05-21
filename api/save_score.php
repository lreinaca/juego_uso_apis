<?php

declare(strict_types=1);

require_once __DIR__ . '/../back/helpers.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'message' => 'Método no permitido.'], 405);
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw ?: '{}', true);

$score = filter_var($payload['score'] ?? null, FILTER_VALIDATE_INT);
$rounds = filter_var($payload['rounds'] ?? null, FILTER_VALIDATE_INT);

if ($score === false || $rounds === false || $score < 0 || $rounds <= 0) {
    app_log('/api/save_score.php', 'invalid_payload', ['payload' => $payload]);
    json_response(['ok' => false, 'message' => 'Datos de puntaje inválidos.'], 422);
}

$userId = (int)($_SESSION['user']['id'] ?? 0);
$stmt = db()->prepare('INSERT INTO scores (user_id, score, rounds) VALUES (:user_id, :score, :rounds)');
$stmt->execute([
    ':user_id' => $userId,
    ':score' => $score,
    ':rounds' => $rounds,
]);

app_log('/api/save_score.php', 'score_saved', [
    'user_id' => $userId,
    'score' => $score,
    'rounds' => $rounds,
]);

json_response([
    'ok' => true,
    'message' => 'Puntaje guardado correctamente.',
    'savedAt' => date('Y-m-d H:i:s'),
]);
