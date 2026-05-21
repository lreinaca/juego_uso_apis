<?php

declare(strict_types=1);

require_once __DIR__ . '/../back/helpers.php';
require_login();

// Endpoint interno del juego:
// consume API publica, filtra planetas y genera pistas estables para el frontend.
$headers = [
    'Accept: application/json',
];

$apiKey = defined('SOLAR_API_KEY') ? trim((string)SOLAR_API_KEY) : '';
if ($apiKey !== '') {
    $headers[] = 'Authorization: Bearer ' . $apiKey;
}

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 12,
        'ignore_errors' => true,
        'header' => implode("\r\n", $headers) . "\r\n",
    ]
]);

$url = 'https://api.le-systeme-solaire.net/rest/bodies/';
$raw = @file_get_contents($url, false, $context);

$statusCode = 0;
if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', (string)$http_response_header[0], $m)) {
    $statusCode = (int)$m[1];
}

if ($raw === false || $statusCode >= 400) {
    app_log('/api/game_data.php', 'api_fetch_failed', [
        'status_code' => $statusCode,
        'used_api_key' => $apiKey !== '',
    ]);
    json_response(['ok' => false, 'message' => 'No fue posible consultar la API pública.'], 502);
}

$decoded = json_decode($raw, true);
$bodies = $decoded['bodies'] ?? [];

$targetPlanets = [
    'Mercury', 'Venus', 'Earth', 'Mars', 'Jupiter', 'Saturn', 'Uranus', 'Neptune', 'Pluto'
];
$targetLookup = array_flip($targetPlanets);

$planets = [];
foreach ($bodies as $body) {
    $name = trim((string)($body['englishName'] ?? ''));
    if ($name === '' || !isset($targetLookup[$name])) {
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

// Garantiza exactamente los 9 planetas clasicos y evita duplicados.
$indexed = [];
foreach ($planets as $planet) {
    $indexed[$planet['name']] = $planet;
}

$ordered = [];
foreach ($targetPlanets as $planetName) {
    if (isset($indexed[$planetName])) {
        $ordered[] = $indexed[$planetName];
    }
}
$planets = $ordered;

shuffle($planets);

if (count($planets) < 9) {
    app_log('/api/game_data.php', 'api_data_insufficient', ['planets' => count($planets)]);
    json_response(['ok' => false, 'message' => 'La API pública no devolvió los 9 planetas esperados.'], 502);
}

app_log('/api/game_data.php', 'api_fetch_success', ['planets' => count($planets)]);
json_response(['ok' => true, 'planets' => $planets, 'source' => 'upstream']);
