<?php
require_once __DIR__ . '/../db_connectie.php';


function haalGebruikersRol($username) {
    $pdo = maakVerbinding();
    $sql = "SELECT role FROM [User] WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user['role'] ?? null;
}
