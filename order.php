<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection and Bestelling class
require('connection.php');
require('Bestelling.php');

$error = "";
$products = [];
$bestelling = new Bestelling($pdo);

// Fetch products from the database
try {
    $sql = "SELECT id, name AS product_name, price FROM products";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    $error = "Fout bij het ophalen van producten: " . $e->getMessage();
}

// Handle adding item to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    try {
        $bestelling->addProductToCart($productId, $quantity);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

    header("Location: order.php");
    exit();
}

// Handle clearing the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_cart'])) {
    $bestelling->clearCart();

    header("Location: order.php");
    exit();
}

// Handle the order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $error = "Uw winkelmand is leeg.";
    } else {
        try {
            $userId = $_SESSION['user_id'];
            $bestelling->submitOrder($userId);
            header("Location: account.php");
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Close connection
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bestelling Plaatsen</title>
    <style>
        .container {
            display: flex;
            flex-direction: column; /* Change to column to stack vertically */
            align-items: flex-start; /* Align items to the left */
        }
        .cart-container {
            border: 1px solid #ccc;
            padding: 10px;
            width: 250px;
            margin-bottom: 20px; /* Add spacing below */
        }
        .cart-item {
            margin-bottom: 5px;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        form {
            width: 250px; /* Match the cart width */
        }
    </style>
    <script>
        function showPrice() {
            const products = <?php echo json_encode($products); ?>;
            const productId = document.getElementById("product").value;
            const selectedProduct = products.find(product => product.id == productId);
            if (selectedProduct) {
                const priceField = document.getElementById("price");
                priceField.textContent = "Prijs: €" + (selectedProduct.price * document.getElementById("quantity").value).toFixed(2);
            } else {
                document.getElementById("price").textContent = "";
            }
        }
    </script>
</head>
<body>
    <h2>Bestelling Plaatsen</h2>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- Main Container -->
    <div class="container">
        <!-- Cart Section -->
        <div class="cart-container">
            <h3>Winkelwagen</h3>
            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <?php
                    // Find the product based on the product ID
                    $product = array_filter($products, function($prod) use ($item) {
                        return $prod['id'] == $item['product_id'];
                    });
                    $product = array_shift($product);
                    ?>
                    <div class="cart-item">
                        <?php echo htmlspecialchars($product['product_name']) . " - " . htmlspecialchars($item['quantity']); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Geen items in de winkelwagen.</p>
            <?php endif; ?>
            <form method="post" action="order.php">
                <input type="submit" name="clear_cart" value="Winkelwagen Leegmaken"> <!-- Clear cart button -->
            </form>
        </div>

        <!-- Order Form -->
        <form method="post" action="order.php">
            <label for="product">Product:</label>
            <select name="product_id" id="product" onchange="showPrice()">
                <option value="">Selecteer een product</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo htmlspecialchars($product['id']); ?>">
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="quantity">Aantal:</label>
            <input type="number" name="quantity" id="quantity" value="1" min="1" step="1" onchange="showPrice()" required>
            <br>
            <div id="price"></div> <!-- Div to show the calculated price -->
            <br>
            <input type="submit" name="add_to_cart" value="Toevoegen aan winkelwagen"> <!-- Add to cart button -->
            <input type="submit" name="order" value="Bestellen"> <!-- Order button -->
        </form>
    </div>

    <a href="account.php">Terug naar Account</a>
</body>
</html>
