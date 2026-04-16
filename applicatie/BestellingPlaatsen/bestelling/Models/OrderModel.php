<?php
// models/OrderModel.php
class OrderModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createOrder($client_username, $client_name, $personnel, $adres) {
        $sql = "
            INSERT INTO pizza_order (client_username, client_name, personnel_username, datetime, status, address)
            VALUES (:client_username, :client_name, :personnel_username, GETDATE(), 1, :address)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':client_username' => $client_username,
            ':client_name' => $client_name,
            ':personnel_username' => $personnel,
            ':address' => $adres
        ]);

        return $this->pdo->lastInsertId();
    }

    public function addProductToOrder($order_id, $product_name, $quantity) {
        $sql = "
            INSERT INTO pizza_order_product (order_id, product_name, quantity)
            VALUES (:order_id, :product_name, :quantity)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':order_id' => $order_id,
            ':product_name' => $product_name,
            ':quantity' => $quantity
        ]);
    }
}
