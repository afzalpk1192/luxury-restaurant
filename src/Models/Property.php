<?php
/**
 * Elite Estates - Property Model
 *
 * Handles database queries, filtering, search, and CRUD for luxury listings.
 */

declare(strict_types=1);

namespace EliteEstates\Models;

require_once __DIR__ . '/../../config/database.php';

use EliteEstates\Config\Database;
use PDO;
use PDOException;

class Property
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Retrieve properties with dynamic filtering and sorting
     */
    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT p.*, 
                (SELECT COUNT(*) FROM property_images pi WHERE pi.property_id = p.id) as gallery_count
                FROM properties p 
                WHERE 1=1";
        $params = [];

        // Filter by Status (default to active for public queries)
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params[':status'] = $filters['status'];
        }

        // Filter by Location
        if (!empty($filters['location']) && $filters['location'] !== 'all') {
            $sql .= " AND (p.location LIKE :loc1 OR p.city LIKE :loc2 OR p.state LIKE :loc3)";
            $locVal = '%' . trim($filters['location']) . '%';
            $params[':loc1'] = $locVal;
            $params[':loc2'] = $locVal;
            $params[':loc3'] = $locVal;
        }

        // Filter by Property Type
        if (!empty($filters['property_type']) && $filters['property_type'] !== 'all') {
            $sql .= " AND p.property_type = :prop_type";
            $params[':prop_type'] = trim($filters['property_type']);
        }

        // Filter by Price Range string (e.g. '$1M - $5M', '$5M - $10M', '$10M+')
        if (!empty($filters['price_range']) && $filters['price_range'] !== 'all') {
            switch ($filters['price_range']) {
                case '$1M - $5M':
                    $sql .= " AND p.price >= 1000000 AND p.price <= 5000000";
                    break;
                case '$5M - $10M':
                    $sql .= " AND p.price > 5000000 AND p.price <= 10000000";
                    break;
                case '$10M+':
                    $sql .= " AND p.price > 10000000";
                    break;
            }
        }

        // Filter by Min / Max Price numeric
        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params[':min_price'] = (float)$filters['min_price'];
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params[':max_price'] = (float)$filters['max_price'];
        }

        // Filter by Featured
        if (isset($filters['is_featured'])) {
            $sql .= " AND p.is_featured = :is_featured";
            $params[':is_featured'] = (int)$filters['is_featured'];
        }

        // Keyword Search
        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE :search1 OR p.description LIKE :search2 OR p.location LIKE :search3 OR p.ref_code LIKE :search4)";
            $searchVal = '%' . trim($filters['search']) . '%';
            $params[':search1'] = $searchVal;
            $params[':search2'] = $searchVal;
            $params[':search3'] = $searchVal;
            $params[':search4'] = $searchVal;
        }

        $sql .= " ORDER BY p.is_featured DESC, p.id ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();

        // Format amenities and prices
        return array_map([$this, 'formatPropertyRecord'], $rows);
    }

    /**
     * Get single property by ID with gallery images
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM properties WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $prop = $stmt->fetch();

        if (!$prop) {
            return null;
        }

        $formatted = $this->formatPropertyRecord($prop);

        // Fetch gallery images
        $imgStmt = $this->pdo->prepare("SELECT * FROM property_images WHERE property_id = :prop_id ORDER BY display_order ASC, id ASC");
        $imgStmt->execute([':prop_id' => $id]);
        $formatted['gallery'] = $imgStmt->fetchAll();

        return $formatted;
    }

    /**
     * Create new luxury property
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO properties (
            ref_code, title, slug, tag, property_type, location, city, state, address,
            price, beds, baths, sqft, lot_size, year_built, featured_image, description,
            amenities, status, is_featured
        ) VALUES (
            :ref_code, :title, :slug, :tag, :property_type, :location, :city, :state, :address,
            :price, :beds, :baths, :sqft, :lot_size, :year_built, :featured_image, :description,
            :amenities, :status, :is_featured
        )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ref_code'       => $data['ref_code'] ?? ('EE-' . rand(10000, 99999) . '-LST'),
            ':title'          => $data['title'],
            ':slug'           => $this->generateSlug($data['title']),
            ':tag'            => $data['tag'] ?? 'For Sale',
            ':property_type'  => $data['property_type'] ?? 'Modern Villa',
            ':location'       => $data['location'],
            ':city'           => $data['city'] ?? '',
            ':state'          => $data['state'] ?? '',
            ':address'        => $data['address'] ?? null,
            ':price'          => (float)$data['price'],
            ':beds'           => (int)($data['beds'] ?? 1),
            ':baths'          => (float)($data['baths'] ?? 1.0),
            ':sqft'           => (int)($data['sqft'] ?? 1000),
            ':lot_size'       => $data['lot_size'] ?? null,
            ':year_built'     => !empty($data['year_built']) ? (int)$data['year_built'] : null,
            ':featured_image' => $data['featured_image'],
            ':description'    => $data['description'],
            ':amenities'      => is_array($data['amenities'] ?? null) ? json_encode($data['amenities']) : ($data['amenities'] ?? null),
            ':status'         => $data['status'] ?? 'active',
            ':is_featured'    => (int)($data['is_featured'] ?? 1)
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update existing property
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowed = [
            'ref_code', 'title', 'tag', 'property_type', 'location', 'city', 'state', 'address',
            'price', 'beds', 'baths', 'sqft', 'lot_size', 'year_built', 'featured_image',
            'description', 'amenities', 'status', 'is_featured'
        ];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "`{$field}` = :{$field}";
                if ($field === 'amenities' && is_array($data[$field])) {
                    $params[":{$field}"] = json_encode($data[$field]);
                } elseif ($field === 'price' || $field === 'baths') {
                    $params[":{$field}"] = (float)$data[$field];
                } elseif ($field === 'beds' || $field === 'sqft' || $field === 'is_featured' || $field === 'year_built') {
                    $params[":{$field}"] = !empty($data[$field]) ? (int)$data[$field] : null;
                } else {
                    $params[":{$field}"] = $data[$field];
                }
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE properties SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete property
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM properties WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Retrieve distinct locations for filter dropdown
     */
    public function getDistinctLocations(): array
    {
        $stmt = $this->pdo->query("SELECT DISTINCT location FROM properties WHERE status = 'active' ORDER BY location ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Retrieve distinct property types for filter dropdown
     */
    public function getDistinctTypes(): array
    {
        $stmt = $this->pdo->query("SELECT DISTINCT property_type FROM properties WHERE status = 'active' ORDER BY property_type ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Helper to format record
     */
    private function formatPropertyRecord(array $row): array
    {
        if (isset($row['amenities']) && is_string($row['amenities'])) {
            $row['amenities_list'] = json_decode($row['amenities'], true) ?? [];
        } else {
            $row['amenities_list'] = [];
        }

        $row['formatted_price'] = '$' . number_format((float)$row['price'], 0);
        $row['formatted_sqft'] = number_format((int)$row['sqft']);
        return $row;
    }

    /**
     * Generate URL friendly slug
     */
    private function generateSlug(string $title): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        return $slug ?: 'estate-' . time();
    }
}
