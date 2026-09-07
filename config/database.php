<?php
/**
 * Elite Estates - Database Configuration & Connection Provider
 *
 * Uses PHP Data Objects (PDO) with strict error handling, utf8mb4 encoding,
 * and prepared statements to ensure maximum security against SQL injection.
 */

declare(strict_types=1);

namespace EliteEstates\Config;

use PDO;
use PDOException;

class Database
{
    // Database connection parameters
    private static string $host = '127.0.0.1';
    private static int $port = 3306;
    private static string $dbName = 'elite_estates';
    private static string $username = 'root';
    private static string $password = '';
    private static string $charset = 'utf8mb4';

    private static ?PDO $instance = null;

    /**
     * Get active singleton PDO connection
     *
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            // Check for environment variable overrides if present
            $host = getenv('DB_HOST') ?: self::$host;
            $port = (int)(getenv('DB_PORT') ?: self::$port);
            $dbName = getenv('DB_NAME') ?: self::$dbName;
            $username = getenv('DB_USER') ?: self::$username;
            $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : self::$password;

            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=" . self::$charset;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::$charset . " COLLATE utf8mb4_unicode_ci",
            ];

            try {
                self::$instance = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                // Return clean diagnostic error message
                throw new PDOException("Database Connection Error: " . $e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }

    /**
     * Get server-level PDO connection (without database selected)
     * Used for automatic database creation and migrations
     *
     * @return PDO
     */
    public static function getServerConnection(): PDO
    {
        $host = getenv('DB_HOST') ?: self::$host;
        $port = (int)(getenv('DB_PORT') ?: self::$port);
        $username = getenv('DB_USER') ?: self::$username;
        $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : self::$password;

        $dsn = "mysql:host={$host};port={$port};charset=" . self::$charset;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        return new PDO($dsn, $username, $password, $options);
    }

    /**
     * Retrieve current database name
     */
    public static function getDatabaseName(): string
    {
        return getenv('DB_NAME') ?: self::$dbName;
    }
}
