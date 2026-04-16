<?php
require_once __DIR__ . '/../../db_connectie.php';
require_once __DIR__ . '/../Registratie_models/UserModel.php';

class RegisterController {
    private $userModel;

    public function __construct() {
        $db = maakVerbinding();
        $this->userModel = new UserModel($db);
    }

    public function handleForm() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $data = [
                'username' => trim($_POST['Username']),
                'voornaam' => trim($_POST['naam']),
                'achternaam' => trim($_POST['achternaam']),
                'adres' => trim($_POST['Adres']),
                'wachtwoord' => password_hash($_POST['wachtwoord'], PASSWORD_DEFAULT),
                'role' => $_POST['role']
            ];

            try {
                $this->userModel->registerUser($data);
                header("Location: Inlog_pagina.php");
                exit;
            } catch (PDOException $e) {
                return "Fout bij registratie: " . $e->getMessage();
            }
        }
        return null;
    }
}
