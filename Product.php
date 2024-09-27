<?php
class Product {
    private $pdo;
    private $id;
    private $name;
    private $description;
    private $price;
    private $stock; // Added stock property

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
            $sql = "SELECT name, description, price, stock FROM products WHERE id = ?"; // Fetch stock
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $this->name = $product['name'];
                $this->description = $product['description'];
                $this->price = $product['price'];
                $this->stock = $product['stock']; // Initialize stock
            } else {
                throw new Exception("Product not found.");
            }
        } catch (\PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }

    // Static method to add a new product
    public static function addProduct($pdo, $name, $description, $price, $stock = 0) {
        try {
            $sql = "INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)"; // Add stock
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price, $stock]);
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
    public static function updateProduct($pdo, $id, $name, $description, $price, $stock) {
        try {
            $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?"; // Update stock
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price, $stock, $id]);
        } catch (\PDOException $e) {
            die("Error updating product: " . $e->getMessage());
        }
    }

    // Method to increase product quantity
    public function increaseStock($amount) {
        $this->stock += $amount; // Increase stock
        $this->updateStockInDB(); // Update database
    }

    // Method to decrease product quantity
    public function decreaseStock($amount) {
        if ($this->stock >= $amount) {
            $this->stock -= $amount; // Decrease stock
            $this->updateStockInDB(); // Update database
        } else {
            throw new Exception("Insufficient stock to decrease.");
        }
    }

    // Method to update stock in the database
    private function updateStockInDB() {
        try {
            $sql = "UPDATE products SET stock = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->stock, $this->id]);
        } catch (\PDOException $e) {
            die("Error updating stock: " . $e->getMessage());
        }
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
