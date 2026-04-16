<?php
class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function registerUser($data) {
        $sql = "INSERT INTO dbo.[User] (username, first_name, last_name, address, password, role)
                VALUES (:username, :voornaam, :achternaam, :adres, :wachtwoord, :role)";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':username' => $data['username'],
            ':voornaam' => $data['voornaam'],
            ':achternaam' => $data['achternaam'],
            ':adres' => $data['adres'],
            ':wachtwoord' => $data['wachtwoord'],
            ':role' => $data['role']
        ]);
    }
}
