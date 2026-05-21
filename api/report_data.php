<?php

declare(strict_types=1);

require_once __DIR__ . '/../back/helpers.php';
require_login();

$pdo = db();
$currentUserId = (int)($_SESSION['user']['id'] ?? 0);
$requestedUserId = filter_input(INPUT_GET, 'userId', FILTER_VALIDATE_INT);
$selectedUserId = $requestedUserId ?: $currentUserId;

$users = $pdo->query('SELECT id, username, full_name FROM users ORDER BY full_name')->fetchAll();

$historyStmt = $pdo->prepare(
    'SELECT u.full_name, s.score, s.rounds, s.created_at
     FROM scores s
     INNER JOIN users u ON u.id = s.user_id
     WHERE s.user_id = :user_id
     ORDER BY s.created_at DESC
     LIMIT 100'
);
$historyStmt->execute([':user_id' => $selectedUserId]);
$history = $historyStmt->fetchAll();

$summaryStmt = $pdo->prepare(
    'SELECT COUNT(*) AS total_games, COALESCE(AVG(score), 0) AS avg_score, COALESCE(MAX(score), 0) AS best_score
     FROM scores
     WHERE user_id = :user_id'
);
$summaryStmt->execute([':user_id' => $selectedUserId]);
$summary = $summaryStmt->fetch();

$periodQuery = static function (string $periodExpr) use ($pdo, $selectedUserId): array {
    $sql = 'SELECT ' . $periodExpr . ' AS period, u.full_name, COUNT(*) AS games, ROUND(AVG(s.score), 2) AS avg_score, MAX(s.score) AS max_score
            FROM scores s
            INNER JOIN users u ON u.id = s.user_id
            WHERE s.user_id = :user_id
            GROUP BY period, u.full_name
            ORDER BY period DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $selectedUserId]);
    return $stmt->fetchAll();
};

$weekly = $periodQuery("DATE_FORMAT(s.created_at, '%x-W%v')");
$monthly = $periodQuery("DATE_FORMAT(s.created_at, '%Y-%m')");

$allTimeStmt = $pdo->query(
    'SELECT u.full_name, COUNT(*) AS games, ROUND(AVG(s.score), 2) AS avg_score, MAX(s.score) AS best_score
     FROM scores s
     INNER JOIN users u ON u.id = s.user_id
     GROUP BY u.full_name
     ORDER BY best_score DESC, avg_score DESC'
);
$allTime = $allTimeStmt->fetchAll();

app_log('/api/report_data.php', 'reports_loaded', ['selected_user_id' => $selectedUserId]);

json_response([
    'ok' => true,
    'users' => $users,
    'selectedUserId' => $selectedUserId,
    'summary' => [
        'totalGames' => (int)($summary['total_games'] ?? 0),
        'avgScore' => (float)($summary['avg_score'] ?? 0),
        'bestScore' => (int)($summary['best_score'] ?? 0),
    ],
    'history' => $history,
    'weekly' => $weekly,
    'monthly' => $monthly,
    'allTime' => $allTime,
]);
