<?php

declare(strict_types=1);

// Carga constantes de entorno y credenciales de base de datos desde back/conexion.php.
require_once __DIR__ . '/conexion.php';

// Bootstrap comun para toda la aplicacion:
// 1) timezone/config
// 2) sesion
// 3) logger y base de datos disponibles

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/logger.php';
require_once __DIR__ . '/db.php';
