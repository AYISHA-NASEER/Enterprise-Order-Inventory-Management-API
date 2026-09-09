<?php

header('Content-Type: application/json');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

/*
|--------------------------------------------------------------------------
| Create Shipment
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/shipments') {

    $body = json_decode(file_get_contents('php://input'), true);

    echo json_encode([
        'shipment_id' => 'SHIP-' . rand(1000, 9999),
        'tracking_number' => 'TRK-' . rand(100000, 999999),
        'carrier' => 'MockExpress',
        'status' => 'pending',
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Get Shipment Status
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'GET'
    && preg_match('#^/shipments/([^/]+)$#', $path, $matches)
) {

    $shipmentId = $matches[1];

    echo json_encode([
        'shipment_id' => $shipmentId,
        'tracking_number' => 'TRK-123456',
        'carrier' => 'MockExpress',
        'status' => 'in_transit',
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Endpoint Not Found
|--------------------------------------------------------------------------
*/

http_response_code(404);

echo json_encode([
    'message' => 'Endpoint not found'
]);