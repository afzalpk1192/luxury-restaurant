<?php
/**
 * Elite Estates - Public API: Filter & Search Properties
 *
 * Method: GET
 * Parameters (Optional):
 *   - location: string (e.g. 'Beverly Hills, CA')
 *   - property_type: string (e.g. 'Modern Villa', 'Penthouse')
 *   - price_range: string ('$1M - $5M', '$5M - $10M', '$10M+')
 *   - search: string
 *   - limit: int (default 50)
 */

declare(strict_types=1);

require_once __DIR__ . '/../src/Helpers/Response.php';
require_once __DIR__ . '/../src/Helpers/Validator.php';
require_once __DIR__ . '/../src/Models/Property.php';

use EliteEstates\Helpers\Response;
use EliteEstates\Helpers\Validator;
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

try {
    $filters = [
        'status'        => 'active',
        'location'      => isset($_GET['location']) ? Validator::sanitizeString($_GET['location']) : null,
        'property_type' => isset($_GET['property_type']) ? Validator::sanitizeString($_GET['property_type']) : null,
        'price_range'   => isset($_GET['price_range']) ? Validator::sanitizeString($_GET['price_range']) : null,
        'search'        => isset($_GET['search']) ? Validator::sanitizeString($_GET['search']) : null
    ];

    $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 50;
    $offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;

    $propertyModel = new Property();
    $properties = $propertyModel->getAll($filters, $limit, $offset);

    Response::json([
        'total'      => count($properties),
        'filters'    => array_filter($filters),
        'properties' => $properties
    ], 'Properties retrieved successfully');

} catch (Throwable $e) {
    Response::error("Failed to retrieve properties: " . $e->getMessage(), 500);
}
