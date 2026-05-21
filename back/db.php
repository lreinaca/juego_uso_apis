<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dbDir = dirname(DB_FILE);
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0775, true);
    }

    $pdo = new PDO('sqlite:' . DB_FILE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

    initialize_database($pdo);

    return $pdo;
}

function initialize_database(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            full_name TEXT NOT NULL,
            password_hash TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS scores (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            score INTEGER NOT NULL,
            rounds INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )'
    );

    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_scores_user_created ON scores(user_id, created_at)');

    seed_users($pdo);
}

function seed_users(PDO $pdo): void
{
    $seed = [
        ['username' => 'neo.solar', 'full_name' => 'Neo Solar', 'password' => 'sol12345'],
        ['username' => 'luna.bit', 'full_name' => 'Luna Bit', 'password' => 'luna12345'],
        ['username' => 'astro.admin', 'full_name' => 'Astro Admin', 'password' => 'admin12345'],
    ];

    $stmt = $pdo->prepare('INSERT OR IGNORE INTO users (username, full_name, password_hash) VALUES (:username, :full_name, :password_hash)');

    foreach ($seed as $user) {
        $stmt->execute([
            ':username' => $user['username'],
            ':full_name' => $user['full_name'],
            ':password_hash' => password_hash($user['password'], PASSWORD_DEFAULT),
        ]);
    }
}
