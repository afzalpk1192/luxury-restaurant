<?php
/**
 * Elite Estates - JSON API Response Helper
 */

declare(strict_types=1);

namespace EliteEstates\Helpers;

class Response
{
    /**
     * Send successful JSON response
     */
    public static function json(mixed $data = null, string $message = 'Success', int $statusCode = 200): void
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
        }
        
        echo json_encode([
            'success'   => true,
            'message'   => $message,
            'data'      => $data,
            'timestamp' => time()
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Send error JSON response
     */
    public static function error(string $message = 'An error occurred', int $statusCode = 400, array $errors = []): void
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
        }
        
        echo json_encode([
            'success'   => false,
            'message'   => $message,
            'errors'    => $errors,
            'timestamp' => time()
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
