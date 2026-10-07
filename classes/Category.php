<?php
require_once __DIR__ . '/../config/Database.php';

class Category
{
    private $conn;
    private $table = "categories";

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Get all categories (used for the top category bar)
    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM {$this->table} ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    // Get a single category by id
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Add a new category (used by admin)
    public function add($name, $image)
    {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (name, image) VALUES (?, ?)");
        return $stmt->execute([$name, $image]);
    }
    // Update an existing category
    public function update($id, $name, $image = null)
    {
        if ($image) {
            $stmt = $this->conn->prepare("UPDATE {$this->table} SET name = ?, image = ? WHERE id = ?");
            return $stmt->execute([$name, $image, $id]);
        }
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }
    // Delete a category
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Count how many products are inside a category (shown in admin panel)
    public function countProducts($id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? (int)$row['total'] : 0;
    }
}
