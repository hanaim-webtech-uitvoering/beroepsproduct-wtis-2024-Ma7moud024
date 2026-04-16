<?php
require_once __DIR__ . '/../db_connectie.php';


function haalAlleProductenMetCategorieen() {
    $pdo = maakVerbinding();
    $sql = "
        SELECT p.name AS product_name, p.price, pt.name AS category_name
        FROM product p
        JOIN ProductType pt ON p.type_id = pt.name
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function haalIngredientenPerProduct() {
    $pdo = maakVerbinding();
    $sql = "SELECT product_name, ingredient_name FROM product_Ingredient";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ingredienten = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($ingredienten as $rij) {
        $result[$rij['product_name']][] = $rij['ingredient_name'];
    }
    return $result;
}
