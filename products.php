<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require('connection.php');
require('Product.php');

$error = "";

// Fetch products
$products = Product::fetchAllProducts($pdo);

// Handle add product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['product_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;

    Product::addProduct($pdo, $name, $description, $price);
    header("Location: products.php");
    exit();
}

// Handle delete product
if (isset($_GET['delete'])) {
    $productId = $_GET['delete'];
    Product::deleteProduct($pdo, $productId);
    header("Location: products.php");
    exit();
}

// Handle update product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'] ?? 0;
    $name = $_POST['product_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;

    Product::updateProduct($pdo, $product_id, $name, $description, $price);
    header("Location: products.php");
    exit();
}

$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Producten</title>
</head>
<body>
    <h2>Producten Overzicht</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>Beschrijving</th>
            <th>Prijs</th>
            <th>Acties</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['id']); ?></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['description']); ?></td>
                <td><?php echo htmlspecialchars($product['price']); ?></td>
                <td>
                    <a href="products.php?delete=<?php echo $product['id']; ?>">Verwijderen</a>
                    <form method="post" action="products.php" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        Naam: <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                        Beschrijving: <input type="text" name="description" value="<?php echo htmlspecialchars($product['description']); ?>">
                        Prijs: <input type="text" name="price" value="<?php echo htmlspecialchars($product['price']); ?>">
                        <input type="submit" name="update_product" value="Bijwerken">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <h2>Nieuw Product Toevoegen</h2>
    <form method="post" action="products.php">
        Naam: <input type="text" name="product_name">
        Beschrijving: <input type="text" name="description">
        Prijs: <input type="text" name="price">
        <input type="submit" name="add_product" value="Toevoegen">
    </form>
</body>
</html>
