<?php
class Product {
    private $pdo;
    private $id;
    private $name;
    private $description;
    private $price;

    // Constructor
    public function __construct($pdo, $id = null) {
        $this->pdo = $pdo;
        if ($id) {
            $this->id = $id;
            $this->fetchProductDetails();
        }
    }

    // Fetch product details from database
    private function fetchProductDetails() {
        try {
            $sql = "SELECT name, description, price FROM products WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $this->name = $product['name'];
                $this->description = $product['description'];
                $this->price = $product['price'];
            } else {
                throw new Exception("Product not found.");
            }
        } catch (\PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }

    // Static method to add a new product
    public static function addProduct($pdo, $name, $description, $price) {
        try {
            $sql = "INSERT INTO products (name, description, price) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price]);
        } catch (\PDOException $e) {
            die("Error adding product: " . $e->getMessage());
        }
    }

    // Static method to delete a product
    public static function deleteProduct($pdo, $id) {
        try {
            $sql = "DELETE FROM products WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
        } catch (\PDOException $e) {
            die("Error deleting product: " . $e->getMessage());
        }
    }

    // Static method to update a product
    public static function updateProduct($pdo, $id, $name, $description, $price) {
        try {
            $sql = "UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price, $id]);
        } catch (\PDOException $e) {
            die("Error updating product: " . $e->getMessage());
        }
    }

    // Method to increase product quantity (if applicable in the future)
    public function increaseQuantity($amount) {
        // Implementation needed based on the database schema
    }

    // Method to decrease product quantity (if applicable in the future)
    public function decreaseQuantity($amount) {
        // Implementation needed based on the database schema
    }

    // Static method to fetch all products
    public static function fetchAllProducts($pdo) {
        try {
            $sql = "SELECT * FROM products";
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }
}
?>
