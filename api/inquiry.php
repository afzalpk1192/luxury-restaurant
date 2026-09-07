<?php
/**
 * Elite Estates - Public API: Submit Property Inquiry / Contact Message
 *
 * Method: POST
 * Body (JSON or Form):
 *   - full_name: string (required)
 *   - email: string (required)
 *   - phone: string (required)
 *   - message: string (required)
 *   - property_id: int (optional)
 */

declare(strict_types=1);

require_once __DIR__ . '/../src/Helpers/Response.php';
require_once __DIR__ . '/../src/Helpers/Validator.php';
require_once __DIR__ . '/../src/Models/Inquiry.php';

use EliteEstates\Helpers\Response;
use EliteEstates\Helpers\Validator;
use EliteEstates\Models\Inquiry;

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

$errors = [];

$fullName = Validator::sanitizeString($input['full_name'] ?? '');
if (empty($fullName)) {
    $errors['full_name'] = 'Full name is required.';
} elseif (strlen($fullName) > 100) {
    $errors['full_name'] = 'Full name must not exceed 100 characters.';
}

$email = trim($input['email'] ?? '');
if (empty($email)) {
    $errors['email'] = 'Email address is required.';
} elseif (!Validator::validateEmail($email)) {
    $errors['email'] = 'Please enter a valid email address.';
}

$phone = trim($input['phone'] ?? '');
if (empty($phone)) {
    $errors['phone'] = 'Phone number is required.';
} elseif (!Validator::validatePhone($phone)) {
    $errors['phone'] = 'Please enter a valid phone number.';
}

$message = Validator::sanitizeString($input['message'] ?? '');
if (empty($message)) {
    $errors['message'] = 'Message or requirements description is required.';
}

$propertyId = !empty($input['property_id']) ? (int)$input['property_id'] : null;

if (!empty($errors)) {
    Response::error('Validation failed. Please correct the highlighted errors.', 422, $errors);
}

try {
    $inquiryModel = new Inquiry();
    $inquiryId = $inquiryModel->create([
        'property_id' => $propertyId,
        'full_name'   => $fullName,
        'email'       => $email,
        'phone'       => $phone,
        'message'     => $message
    ]);

    Response::json([
        'inquiry_id' => $inquiryId,
        'status'     => 'received'
    ], 'Your inquiry has been submitted successfully. A private concierge representative will contact you shortly.', 201);

} catch (Throwable $e) {
    Response::error("Failed to process inquiry: " . $e->getMessage(), 500);
}
