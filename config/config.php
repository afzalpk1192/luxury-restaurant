<?php
/**
 * Elite Estates - Application Global Configuration
 */

declare(strict_types=1);

namespace EliteEstates\Config;

class AppConfig
{
    public const APP_NAME = 'Elite Estates';
    public const APP_TAGLINE = 'Luxury Real Estate Services';
    public const CONTACT_PHONE = '+1 (800) ELITE-RE';
    public const CONTACT_EMAIL = 'concierge@eliteestates.com';
    public const OFFICE_ADDRESS = '750 5th Ave, New York, NY';
    public const CURRENCY_SYMBOL = '$';
    
    /**
     * Determine base URL dynamically
     */
    public static function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $baseUrl = rtrim($protocol . $host . $scriptDir, '/');
        return $baseUrl;
    }

    /**
     * Start secure session if not already active
     */
    public static function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            session_start();
        }
    }
}
