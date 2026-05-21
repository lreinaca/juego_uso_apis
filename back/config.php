<?php

declare(strict_types=1);

define('APP_NAME', 'Solar Score Arena');
define('APP_TIMEZONE', 'America/Bogota');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_NAME', 'solar_score_arena');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('LOG_DIR', __DIR__ . '/../logs');
define('LOG_FILE_NAME', 'solar_events.log');
define('ROUNDS_PER_GAME', 5);

date_default_timezone_set(APP_TIMEZONE);
