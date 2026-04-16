<?php
require_once __DIR__ . '/../../db_connectie.php';


class ProductModel {
    private $pdo;

    public function __construct() {
        $this->pdo = maakVerbinding();
    }

    public function getProductenByNames(array $namen): array {
        if (empty($namen)) return [];

        $placeholders = implode(',', array_fill(0, count($namen), '?'));
        $stmt = $this->pdo->prepare("SELECT name, price, type_id FROM product WHERE name IN ($placeholders)");
        $stmt->execute($namen);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
