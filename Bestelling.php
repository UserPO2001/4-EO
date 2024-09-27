<?php
class Bestelling {
    private $pdo;
    private $cart = [];

    public function __construct($pdo) {
        $this->pdo = $pdo;
        if (isset($_SESSION['cart'])) {
            $this->cart = $_SESSION['cart'];
        }
    }

    public function addProductToCart($productId, $quantity) {
        // Check if product is already in the cart
        $existingProduct = array_filter($this->cart, function($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($existingProduct) {
            // Update quantity if product exists in cart
            $index = array_keys($existingProduct)[0];
            $this->cart[$index]['quantity'] += $quantity;
        } else {
            // Add new product to cart
            $this->cart[] = ['product_id' => $productId, 'quantity' => $quantity];
        }

        $_SESSION['cart'] = $this->cart;
    }

    public function clearCart() {
        $this->cart = [];
        $_SESSION['cart'] = [];
    }

    public function submitOrder($userId) {
        $this->pdo->beginTransaction();

        try {
            foreach ($this->cart as $item) {
                // Check stock availability
                $sql = "SELECT stock FROM products WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product && $product['stock'] >= $item['quantity']) {
                    // Insert order into orders table
                    $sql = "INSERT INTO orders (user_id, product_id, quantity, order_date) VALUES (?, ?, ?, NOW())";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$userId, $item['product_id'], $item['quantity']]);

                    // Update stock in products table
                    $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([$item['quantity'], $item['product_id']]);
                } else {
                    throw new Exception("Insufficient stock for product ID: " . $item['product_id']);
                }
            }

            $this->clearCart(); // Clear cart after successful order
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e; // Rethrow exception to be caught in order.php
        }
    }
}
?>
