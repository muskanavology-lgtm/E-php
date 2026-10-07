<?php
require_once __DIR__ . '/../config/Database.php';

class Product
{
    private $conn;
    private $table = "products";

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Get all products (newest first)
    public function getAll()
    {
        $sql = "SELECT p.*, c.name AS category_name
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                ORDER BY p.created_at DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll();
    }

    // Get a limited number of products (used for "Popular Products" on home page)
    public function getLatest($limit = 12)
    {
        $limit = (int)$limit;
        $sql = "SELECT p.*, c.name AS category_name
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                ORDER BY p.created_at DESC
                LIMIT $limit";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll();
    }

    // Get single product by id
    public function getById($id)
    {
        $sql = "SELECT p.*, c.name AS category_name
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Get all products belonging to one category (category-wise browsing)
    public function getByCategory($categoryId)
    {
        $sql = "SELECT p.*, c.name AS category_name
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = ?
                ORDER BY p.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    // Search products by keyword, optionally narrowed down to one category
    public function search($keyword, $categoryId = null)
    {
        $like = "%" . $keyword . "%";
        if ($categoryId) {
            $sql = "SELECT p.*, c.name AS category_name
                    FROM {$this->table} p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE (p.name LIKE ? OR p.description LIKE ?) AND p.category_id = ?
                    ORDER BY p.name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$like, $like, $categoryId]);
        } else {
            $sql = "SELECT p.*, c.name AS category_name
                    FROM {$this->table} p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.name LIKE ? OR p.description LIKE ?
                    ORDER BY p.name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$like, $like]);
        }
        return $stmt->fetchAll();
    }

    // Add a new product (used by admin)
    public function add($name, $description, $price, $categoryId, $stock, $image)
    {
        $sql = "INSERT INTO {$this->table} (name, description, price, category_id, stock, image, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$name, $description, $price, $categoryId, $stock, $image]);
    }

    // Update an existing product. Pass $image = null to keep the old image.
    public function update($id, $name, $description, $price, $categoryId, $stock, $image = null)
    {
        if ($image) {
            $sql = "UPDATE {$this->table}
                    SET name = ?, description = ?, price = ?, category_id = ?, stock = ?, image = ?
                    WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$name, $description, $price, $categoryId, $stock, $image, $id]);
        }
        $sql = "UPDATE {$this->table}
                SET name = ?, description = ?, price = ?, category_id = ?, stock = ?
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$name, $description, $price, $categoryId, $stock, $id]);
    }

    // Delete a product
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Reduce stock after checkout
    public function reduceStock($id, $qty)
    {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET stock = GREATEST(stock - ?, 0) WHERE id = ?");
        return $stmt->execute([$qty, $id]);
    }

    public function countAll()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) AS total FROM {$this->table}");
        $row = $stmt->fetch();
        return $row ? (int)$row['total'] : 0;
    }
}
