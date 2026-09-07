<?php
/**
 * Elite Estates - Request Validation & Sanitization Helper
 */

declare(strict_types=1);

namespace EliteEstates\Helpers;

class Validator
{
    /**
     * Sanitize string against XSS and excessive whitespace
     */
    public static function sanitizeString(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        $trimmed = trim($value);
        return htmlspecialchars($trimmed, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Validate email format
     */
    public static function validateEmail(string $email): bool
    {
        return (bool)filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate phone number (allows +, digits, spaces, hyphens, parentheses)
     */
    public static function validatePhone(string $phone): bool
    {
        $cleaned = trim($phone);
        return (bool)preg_match('/^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.\/0-9]{6,20}$/', $cleaned);
    }

    /**
     * Get JSON request payload or fallback to $_POST
     */
    public static function getRequestData(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            return is_array($data) ? $data : [];
        }

        return $_POST;
    }
}
