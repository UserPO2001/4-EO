<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require('connection.php');
require('Gebruiker.php');

$userId = $_SESSION['user_id'];
$gebruiker = new Gebruiker($pdo, $userId);

$userDetails = $gebruiker->getUserDetails();
$orders = $gebruiker->getOrders();

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Pagina</title>
</head>
<body>
    <h2>Welkom, <?php echo htmlspecialchars($userDetails['firstname']); ?>!</h2>
    <p>Gebruikersnaam: <?php echo htmlspecialchars($userDetails['username']); ?></p>
    <p>Voornaam: <?php echo htmlspecialchars($userDetails['firstname']); ?></p>
    <p>Achternaam: <?php echo htmlspecialchars($userDetails['lastname']); ?></p>
    <a href="order.php">Bestelling plaatsen</a> | <a href="?logout">Uitloggen</a>
    <h3>Jouw Bestellingen:</h3>
    <ul>
        <?php foreach ($orders as $order) { ?>
            <li>
                <?php echo htmlspecialchars($order['product_name']) . " - " . htmlspecialchars($order['quantity']) . " op " . htmlspecialchars($order['order_date']); ?>
                <br>
                Prijs per stuk: €<?php echo number_format($order['product_price'], 2); ?>
                <br>
                Totaal: €<?php echo number_format($order['product_price'] * $order['quantity'], 2); ?>
            </li>
        <?php } ?>
    </ul>
</body>
</html>
