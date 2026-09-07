<?php
/**
 * Elite Estates - Admin Authentication & Dashboard Metrics Model
 */

declare(strict_types=1);

namespace EliteEstates\Models;

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';

use EliteEstates\Config\Database;
use EliteEstates\Config\AppConfig;
use PDO;

class Admin
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Authenticate admin by username or email
     */
    public function authenticate(string $usernameOrEmail, string $password): ?array
    {
        $cleanInput = trim($usernameOrEmail);
        $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE username = :uname OR email = :uemail LIMIT 1");
        $stmt->execute([
            ':uname'  => $cleanInput,
            ':uemail' => $cleanInput
        ]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Update last login
            $upd = $this->pdo->prepare("UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE id = :id");
            $upd->execute([':id' => $admin['id']]);

            // Set session
            AppConfig::initSession();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];

            // Don't expose password hash
            unset($admin['password_hash']);
            return $admin;
        }

        return null;
    }

    /**
     * Verify if current session is an authenticated admin
     */
    public static function checkAuth(): bool
    {
        AppConfig::initSession();
        return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    /**
     * Enforce authentication, redirecting to login if not signed in
     */
    public static function requireAuth(): void
    {
        if (!self::checkAuth()) {
            header('Location: login.php');
            exit;
        }
    }

    /**
     * Log out current session
     */
    public static function logout(): void
    {
        AppConfig::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Get aggregate statistics for dashboard
     */
    public function getDashboardMetrics(): array
    {
        $totalListings = (int)$this->pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn();
        $activeListings = (int)$this->pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'active'")->fetchColumn();
        $totalPortfolioValue = (float)$this->pdo->query("SELECT SUM(price) FROM properties WHERE status = 'active'")->fetchColumn();
        $totalInquiries = (int)$this->pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
        $newInquiries = (int)$this->pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn();
        $totalSubscribers = (int)$this->pdo->query("SELECT COUNT(*) FROM subscribers WHERE status = 'subscribed'")->fetchColumn();

        return [
            'total_listings'        => $totalListings,
            'active_listings'       => $activeListings,
            'portfolio_value'       => $totalPortfolioValue,
            'formatted_portfolio'   => '$' . number_format($totalPortfolioValue / 1000000, 1) . 'M',
            'total_inquiries'       => $totalInquiries,
            'new_inquiries'         => $newInquiries,
            'total_subscribers'     => $totalSubscribers
        ];
    }
}
