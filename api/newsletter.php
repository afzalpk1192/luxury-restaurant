<?php
/**
 * Elite Estates - Public API: Newsletter Subscription
 *
 * Method: POST
 * Body (JSON or Form): email (string, required)
 */

declare(strict_types=1);

require_once __DIR__ . '/../src/Helpers/Response.php';
require_once __DIR__ . '/../src/Helpers/Validator.php';
require_once __DIR__ . '/../src/Models/Subscriber.php';

use EliteEstates\Helpers\Response;
use EliteEstates\Helpers\Validator;
use EliteEstates\Models\Subscriber;

if (!headers_sent()) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'POST') === 'OPTIONS') {
    if (!headers_sent()) {
        http_response_code(200);
    }
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'POST') !== 'POST') {
    Response::error('Only POST requests are permitted.', 405);
}

$input = Validator::getRequestData();
$email = trim($input['email'] ?? '');

if (empty($email) || !Validator::validateEmail($email)) {
    Response::error('Please provide a valid email address.', 422, ['email' => 'Valid email address required']);
}

try {
    $subscriberModel = new Subscriber();
    $isNew = $subscriberModel->subscribe($email);

    if ($isNew) {
        Response::json(['subscribed' => true], 'Thank you for joining our exclusive private portfolio newsletter.', 201);
    } else {
        Response::json(['subscribed' => true, 'already_registered' => true], 'You are already subscribed to the Elite Estates newsletter.', 200);
    }
} catch (Throwable $e) {
    Response::error("Subscription failed: " . $e->getMessage(), 500);
}
