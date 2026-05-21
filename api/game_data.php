<?php

declare(strict_types=1);

require_once __DIR__ . '/../back/helpers.php';
require_login();

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 12,
        'header' => "Accept: application/json\r\n",
    ]
]);

$url = 'https://api.le-systeme-solaire.net/rest/bodies/';
$raw = @file_get_contents($url, false, $context);

if ($raw === false) {
    app_log('/api/game_data.php', 'api_fetch_failed');
    json_response(['ok' => false, 'message' => 'No fue posible consultar la API pública.'], 502);
}

$decoded = json_decode($raw, true);
$bodies = $decoded['bodies'] ?? [];

$planets = [];
foreach ($bodies as $body) {
    if (!($body['isPlanet'] ?? false)) {
        continue;
    }

    $name = trim((string)($body['englishName'] ?? ''));
    if ($name === '') {
        continue;
    }

    $moons = isset($body['moons']) && is_array($body['moons']) ? count($body['moons']) : 0;
    $planets[] = [
        'name' => $name,
        'clues' => [
            'Tipo: ' . ($body['bodyType'] ?? 'Desconocido'),
            'Gravedad: ' . ($body['gravity'] ?? 'N/D') . ' m/s2',
            'Periodo orbital: ' . ($body['sideralOrbit'] ?? 'N/D') . ' dias',
            'Lunas conocidas: ' . $moons,
            'Densidad: ' . ($body['density'] ?? 'N/D'),
        ],
    ];
}

shuffle($planets);
app_log('/api/game_data.php', 'api_fetch_success', ['planets' => count($planets)]);
json_response(['ok' => true, 'planets' => $planets]);
