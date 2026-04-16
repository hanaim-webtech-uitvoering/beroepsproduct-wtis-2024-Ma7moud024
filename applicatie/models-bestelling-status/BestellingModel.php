<?php
require_once __DIR__ . '/../db_connectie.php';

function getBestellingenVanGebruiker($username) {
    $pdo = maakVerbinding();
    $sql = "SELECT order_id, datetime, status, address FROM pizza_order WHERE client_username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    return $stmt->fetchAll();
}
