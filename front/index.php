<?php

declare(strict_types=1);

require_once __DIR__ . '/../back/helpers.php';

// Front controller del modulo visual:
// con sesion -> dashboard, sin sesion -> login.

if (current_user()) {
    redirect_to('dashboard.php');
}

redirect_to('login.php');
