<?php
class Bestelling {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addProductToCart($productId, $quantity) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        // Example logic for adding to cart
        $_SESSION['cart'][] = [
            'product_id' => $productId,
            'quantity' => $quantity
        ];
    }

    public function clearCart() {
        unset($_SESSION['cart']);
    }

    public function submitOrder($userId) {
        // Example logic for submitting the order
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            throw new Exception("Your cart is empty.");
        }
        foreach ($_SESSION['cart'] as $item) {
            // Insert into orders table
            $sql = "INSERT INTO orders (user_id, product_id, quantity, order_date) VALUES (?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $item['product_id'], $item['quantity']]);
        }
        // Clear the cart after successful order submission
        $this->clearCart();
    }
}
?>
