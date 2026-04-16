<?php
// controllers/LoginController.php

require_once __DIR__ . '/../Login_models/UserModel.php';

class LoginController {
    private $userModel;

    public function __construct(PDO $db) {
        $this->userModel = new UserModel($db);
    }

    public function login(string $username, string $wachtwoord): ?string {
        $user = $this->userModel->getUserByUsername($username);

        if (!$user) {
            return "Gebruiker niet gevonden.";
        }

        if (!password_verify($wachtwoord, $user['password'])) {
            return "Onjuist wachtwoord.";
        }

        // Login succesvol
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: /../hoofdpagina.php");
        exit;
    }
}
