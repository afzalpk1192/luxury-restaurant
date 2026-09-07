<?php
/**
 * Elite Estates - Newsletter Subscriber Model
 */

declare(strict_types=1);

namespace EliteEstates\Models;

require_once __DIR__ . '/../../config/database.php';

use EliteEstates\Config\Database;
use PDO;

class Subscriber
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Subscribe an email address
     * Returns true if newly subscribed, false if already subscribed
     */
    public function subscribe(string $email): bool
    {
        $cleanEmail = strtolower(trim($email));

        // Check if already exists
        $stmt = $this->pdo->prepare("SELECT id, status FROM subscribers WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $cleanEmail]);
        $existing = $stmt->fetch();

        if ($existing) {
            if ($existing['status'] === 'unsubscribed') {
                // Re-subscribe
                $update = $this->pdo->prepare("UPDATE subscribers SET status = 'subscribed', subscribed_at = CURRENT_TIMESTAMP WHERE id = :id");
                return $update->execute([':id' => $existing['id']]);
            }
            // Already active
            return false;
        }

        $insert = $this->pdo->prepare("INSERT INTO subscribers (email, status) VALUES (:email, 'subscribed')");
        return $insert->execute([':email' => $cleanEmail]);
    }

    /**
     * Get all subscribers
     */
    public function getAll(int $limit = 500): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM subscribers ORDER BY subscribed_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count active subscribers
     */
    public function countActive(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM subscribers WHERE status = 'subscribed'")->fetchColumn();
    }
}
