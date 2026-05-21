<?php

declare(strict_types=1);

// Carga constantes de entorno y credenciales de base de datos desde back/conexion.php.
require_once __DIR__ . '/conexion.php';

// Crea una unica instancia PDO para todo el request.
// Primer paso: conexion al servidor MySQL.
// Segundo paso: creacion del esquema si no existe.
// Tercer paso: reconexion a la base final y bootstrap de tablas/seed.

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $serverDsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', DB_HOST, DB_PORT);
    $serverPdo = new PDO($serverDsn, DB_USER, DB_PASSWORD, $options);
    $serverPdo->exec(
        'CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
    );

    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('SET NAMES utf8mb4');

    initialize_database($pdo);

    return $pdo;
}

function initialize_database(PDO $pdo): void
{
    // Estructura minima para autenticacion y almacenamiento de puntajes.
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(80) NOT NULL UNIQUE,
            full_name VARCHAR(120) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS scores (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            score INT NOT NULL,
            rounds INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_scores_user FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $indexStmt = $pdo->prepare("SHOW INDEX FROM scores WHERE Key_name = 'idx_scores_user_created'");
    $indexStmt->execute();
    $indexExists = $indexStmt->fetch();

    if (!$indexExists) {
        $pdo->exec('CREATE INDEX idx_scores_user_created ON scores(user_id, created_at)');
    }

    // Seed idempotente: asegura cuentas de acceso para la demo.
    seed_users($pdo);
}

function seed_users(PDO $pdo): void
{
    $seed = [
        ['username' => 'neo.solar', 'full_name' => 'Neo Solar', 'password' => 'sol12345'],
        ['username' => 'luna.bit', 'full_name' => 'Luna Bit', 'password' => 'luna12345'],
        ['username' => 'astro.admin', 'full_name' => 'Astro Admin', 'password' => 'admin12345'],
    ];

    $stmt = $pdo->prepare(
        'INSERT INTO users (username, full_name, password_hash)
         VALUES (:username, :full_name, :password_hash)
         ON DUPLICATE KEY UPDATE username = VALUES(username)'
    );

    foreach ($seed as $user) {
        $stmt->execute([
            ':username' => $user['username'],
            ':full_name' => $user['full_name'],
            ':password_hash' => password_hash($user['password'], PASSWORD_DEFAULT),
        ]);
    }
}
