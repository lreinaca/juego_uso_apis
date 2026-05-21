<?php

declare(strict_types=1);

require_once __DIR__ . '/../back/helpers.php';

if (current_user()) {
    redirect_to('dashboard.php');
}

redirect_to('login.php');
