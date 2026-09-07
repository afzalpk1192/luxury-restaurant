<?php
/**
 * Elite Estates - Database Migration & Seeder Runner
 *
 * Can be run from CLI: `php database/migrate.php`
 * Or in the browser: `http://localhost:8000/database/migrate.php`
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

use EliteEstates\Config\Database;

$isCli = (php_sapi_name() === 'cli');

function logMsg(string $message, string $type = 'info'): void {
    global $isCli;
    if ($isCli) {
        $colors = [
            'info'    => "\033[36m",
            'success' => "\033[32m",
            'error'   => "\033[31m",
            'warning' => "\033[33m",
            'reset'   => "\033[0m"
        ];
        echo ($colors[$type] ?? '') . "[{$type}] " . $message . ($colors['reset'] ?? '') . PHP_EOL;
    } else {
        $bg = match($type) {
            'success' => '#dcfce7; color: #166534; border-left: 4px solid #16a34a;',
            'error'   => '#fee2e2; color: #991b1b; border-left: 4px solid #dc2626;',
            'warning' => '#fef9c3; color: #854d0e; border-left: 4px solid #ca8a04;',
            default   => '#e0f2fe; color: #075985; border-left: 4px solid #0284c7;'
        };
        echo "<div style='padding: 10px 16px; margin-bottom: 8px; border-radius: 6px; font-family: monospace; background: {$bg}'><strong>[" . strtoupper($type) . "]</strong> {$message}</div>";
    }
}

if (!$isCli) {
    echo '<!DOCTYPE html><html><head><title>Elite Estates - Migration Runner</title><style>body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px; max-width: 900px; margin: auto; }</style></head><body>';
    echo '<h1 style="color: #c5a059; border-bottom: 1px solid #334155; padding-bottom: 16px;">Elite Estates Database Migration & Seeder</h1>';
}

try {
    logMsg("Attempting to connect to MySQL server...", 'info');
    $serverPdo = Database::getServerConnection();
    logMsg("Connected to MySQL server successfully!", 'success');

    $dbName = Database::getDatabaseName();
    logMsg("Creating database `{$dbName}` if it does not already exist...", 'info');
    $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    logMsg("Database `{$dbName}` is ready.", 'success');

    // Connect to specific database
    $dbPdo = Database::getConnection();

    // 1. Run Schema
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new RuntimeException("Schema file not found at: {$schemaFile}");
    }

    logMsg("Reading schema definitions from schema.sql...", 'info');
    $schemaSql = file_get_contents($schemaFile);
    
    // Execute multi-query statements
    $dbPdo->exec($schemaSql);
    logMsg("All database tables created successfully (properties, property_images, inquiries, subscribers, admins).", 'success');

    // 2. Run Seed
    $seedFile = __DIR__ . '/seed.sql';
    if (file_exists($seedFile)) {
        logMsg("Reading seed data from seed.sql...", 'info');
        $seedSql = file_get_contents($seedFile);
        $dbPdo->exec($seedSql);
        logMsg("Database seeded successfully with luxury estates, gallery images, admin, and mock inquiries!", 'success');
    }

    // Verify row counts
    $propCount = $dbPdo->query("SELECT COUNT(*) FROM `properties`")->fetchColumn();
    $adminCount = $dbPdo->query("SELECT COUNT(*) FROM `admins`")->fetchColumn();
    $inquiryCount = $dbPdo->query("SELECT COUNT(*) FROM `inquiries`")->fetchColumn();

    logMsg("Summary: {$propCount} properties active, {$adminCount} admin user, {$inquiryCount} inquiries loaded.", 'success');
    logMsg("Default Admin: admin@eliteestates.com | Password: Admin@12345", 'warning');
    logMsg("Migration completed successfully!", 'success');

    if (!$isCli) {
        echo '<div style="margin-top: 24px;"><a href="../index.php" style="background: linear-gradient(135deg, #c5a059 0%, #947a43 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 9999px; font-weight: bold; margin-right: 12px;">Visit Public Website</a>';
        echo '<a href="../admin/login.php" style="background: #1e293b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 9999px; font-weight: bold;">Go to Admin Portal</a></div>';
    }

} catch (PDOException $e) {
    logMsg("MySQL Connection/Query Failure: " . $e->getMessage(), 'error');
    logMsg("Troubleshooting tip: Ensure your MySQL service (e.g. XAMPP, WAMP, Laragon, or standalone MySQL) is running on port 3306.", 'warning');
    logMsg("You can customize host/user/password in `config/database.php` or environment variables.", 'info');
    exit(1);
} catch (Throwable $e) {
    logMsg("Migration Error: " . $e->getMessage(), 'error');
    exit(1);
}

if (!$isCli) {
    echo '</body></html>';
}
