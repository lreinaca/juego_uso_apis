<?php

declare(strict_types=1);

$queryString = $_SERVER['QUERY_STRING'] ?? '';
$target = 'front/index.php';

if ($queryString !== '') {
    $target .= '?' . $queryString;
}

header('Location: ' . $target);
exit;
