<?php
/**
 * Elite Estates - Public API: Single Property Details
 *
 * Method: GET
 * Query Param: id (int, required)
 */

declare(strict_types=1);

require_once __DIR__ . '/../src/Helpers/Response.php';
require_once __DIR__ . '/../src/Models/Property.php';

use EliteEstates\Helpers\Response;
use EliteEstates\Models\Property;

if (!headers_sent()) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    if (!headers_sent()) {
        http_response_code(200);
    }
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    Response::error('A valid property ID is required.', 400);
}

try {
    $propertyModel = new Property();
    $property = $propertyModel->getById($id);

    if (!$property) {
        Response::error('Property not found.', 404);
    }

    Response::json($property, 'Property retrieved successfully');

} catch (Throwable $e) {
    Response::error("Failed to retrieve property: " . $e->getMessage(), 500);
}
