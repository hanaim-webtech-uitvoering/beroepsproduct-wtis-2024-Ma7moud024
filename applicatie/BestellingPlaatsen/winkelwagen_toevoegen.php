<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'])) {
    $product = $_POST['product_name'];

    // Voeg toe of verhoog hoeveelheid
    if (isset($_SESSION['winkelwagen'][$product])) {
        $_SESSION['winkelwagen'][$product]++;
    } else {
        $_SESSION['winkelwagen'][$product] = 1;
    }
}

header('Location: winkelwagen.php');
exit;
