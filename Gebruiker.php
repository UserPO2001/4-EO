<?php
class Gebruiker {
    private $pdo;
    private $id;
    private $username;
    private $firstname;
    private $lastname;

    public function __construct($pdo, $id = null) {
        $this->pdo = $pdo;
        if ($id) {
            $this->id = $id;
            $this->fetchUserDetails();
        }
    }

    private function fetchUserDetails() {
        try {
            $sql = "SELECT username, firstname, lastname FROM users WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $this->username = $user['username'];
                $this->firstname = $user['firstname'];
                $this->lastname = $user['lastname'];
            } else {
                throw new Exception("User not found.");
            }
        } catch (\PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }

    public static function register($pdo, $username, $password, $firstname, $lastname) {
        try {
            $sql = "INSERT INTO users (username, password, firstname, lastname) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute([$username, $passwordHash, $firstname, $lastname]);
        } catch (\PDOException $e) {
            die("Error registering user: " . $e->getMessage());
        }
    }

    public function getUserDetails() {
        return [
            'username' => $this->username,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname
        ];
    }

    public function getOrders() {
        $orders = [];
        try {
            $sql = "SELECT o.id, p.name AS product_name, p.price AS product_price, o.quantity, o.order_date 
                    FROM orders o 
                    JOIN products p ON o.product_id = p.id 
                    WHERE o.user_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->id]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
        return $orders;
    }
}
?>
