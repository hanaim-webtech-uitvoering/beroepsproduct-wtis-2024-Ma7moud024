<?php
require_once __DIR__ . '/../models/ProductModel.php';
session_start();

$winkelwagen = $_SESSION['winkelwagen'] ?? [];
$productenData = [];
$totaal = 0;

if (!empty($winkelwagen)) {
    $model = new ProductModel();
    $producten = $model->getProductenByNames(array_keys($winkelwagen));

    foreach ($producten as $product) {
        $productenData[$product['name']] = $product;
    }

    foreach ($winkelwagen as $naam => $aantal) {
        if (isset($productenData[$naam])) {
            $totaal += $productenData[$naam]['price'] * $aantal;
        }
    }
}

require __DIR__ . '/../views/winkelwagen.view.php';
