<?php


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "slijterij";

// Maak verbinding
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Maak tabel `users`
$sql = "CREATE TABLE users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(50),
    lastname VARCHAR(50),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Maak tabel `products`
$sql = "CREATE TABLE products (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Maak tabel `orders`
$sql = "CREATE TABLE orders (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    product_id INT(6) UNSIGNED,
    quantity INT(10) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";
$conn->query($sql);

$conn->close();
?>
