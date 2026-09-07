<?php
/**
 * Elite Estates - Inquiry Model
 *
 * Manages customer contact inquiries, private viewing requests, and lead statuses.
 */

declare(strict_types=1);

namespace EliteEstates\Models;

require_once __DIR__ . '/../../config/database.php';

use EliteEstates\Config\Database;
use PDO;

class Inquiry
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Create new inquiry from public contact form or viewing booking
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO inquiries (
            property_id, full_name, email, phone, message, status, ip_address
        ) VALUES (
            :property_id, :full_name, :email, :phone, :message, :status, :ip_address
        )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':property_id' => !empty($data['property_id']) ? (int)$data['property_id'] : null,
            ':full_name'   => trim($data['full_name']),
            ':email'       => trim($data['email']),
            ':phone'       => trim($data['phone']),
            ':message'     => trim($data['message']),
            ':status'      => 'new',
            ':ip_address'  => $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Get all inquiries for admin dashboard, including joined property title & ref_code
     */
    public function getAll(?string $status = null, int $limit = 100): array
    {
        $sql = "SELECT i.*, p.title as property_title, p.ref_code as property_ref_code, p.price as property_price
                FROM inquiries i
                LEFT JOIN properties p ON i.property_id = p.id";
        
        $params = [];
        if ($status !== null && $status !== 'all') {
            $sql .= " WHERE i.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY i.created_at DESC LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Update inquiry status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['new', 'in_review', 'contacted', 'closed'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE inquiries SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    /**
     * Delete inquiry
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM inquiries WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Count unread/new inquiries
     */
    public function countNew(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn();
    }
}
