<?php
// models/UserModel.php
class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getPersonnelUsername() {
        $stmt = $this->pdo->query("
            SELECT TOP 1 username 
            FROM [user] 
            WHERE role = 'personnel' 
            ORDER BY NEWID()
        ");
        return $stmt->fetchColumn();
    }

    public function getClientName($username) {
        $stmt = $this->pdo->prepare("
            SELECT first_name, last_name 
            FROM [user] 
            WHERE username = :username
        ");
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['first_name'] . ' ' . $row['last_name'] : 'gast';
    }
}
