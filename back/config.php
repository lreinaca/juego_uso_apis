<?php

declare(strict_types=1);

define('APP_NAME', 'Solar Score Arena');
define('APP_TIMEZONE', 'America/Bogota');
define('DB_FILE', __DIR__ . '/../database/solar_scores.sqlite');
define('LOG_DIR', __DIR__ . '/../logs');
define('LOG_FILE_NAME', 'solar_events.log');
define('ROUNDS_PER_GAME', 5);

date_default_timezone_set(APP_TIMEZONE);
