<?php

declare(strict_types=1);

// Configuracion central de aplicacion.
// Ajuste estos valores al cambiar de entorno (local, QA, produccion).

define('APP_NAME', 'Solar Score Arena');
// Si trabajas en otro computador, ajusta la zona horaria local.
define('APP_TIMEZONE', 'America/Bogota');

// Conexion MySQL por entorno.
// En otro equipo, actualiza host/puerto/usuario/password segun tu XAMPP o servidor.
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_NAME', 'solar_score_arena');
define('DB_USER', 'adminSS');
define('DB_PASSWORD', 'ss1234');

// Token de API publica (Bearer).
// Si expira o cambias de equipo, genera uno nuevo en:
// https://api.le-systeme-solaire.net/generatekey.html
define('SOLAR_API_KEY', 'bac3aa07-d669-4544-8a3a-76bd61dd81a8');

// Ruta/nombre de logs. Si no se escriben eventos en otro computador,
// revisa permisos de la carpeta logs y del archivo solar_events.log.
define('LOG_DIR', __DIR__ . '/../logs');
define('LOG_FILE_NAME', 'solar_events.log');
define('ROUNDS_PER_GAME', 5);

date_default_timezone_set(APP_TIMEZONE);
