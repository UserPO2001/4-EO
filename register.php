<?php
session_start();
require('connection.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    try {
        $sql = "INSERT INTO users (username, password, firstname, lastname) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $password, $firstname, $lastname]);

        // Redirect to login page
        header("Location: login.php");
        exit();
    } catch (\PDOException $e) {
        $error = "Fout bij registratie: " . $e->getMessage();
    }
}

// Close connection
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registratie</title>
</head>
<body>
    <h2>Registreren</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="register.php">
        <label for="username">Gebruikersnaam:</label>
        <input type="text" name="username" id="username" required>
        <label for="password">Wachtwoord:</label>
        <input type="password" name="password" id="password" required>
        <label for="firstname">Voornaam:</label>
        <input type="text" name="firstname" id="firstname" required>
        <label for="lastname">Achternaam:</label>
        <input type="text" name="lastname" id="lastname" required>
        <input type="submit" value="Registreren">
    </form>
    <a href="login.php">Terug naar Inloggen</a>
</body>
</html>
