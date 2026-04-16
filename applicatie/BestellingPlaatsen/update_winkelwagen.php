<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['aantallen'] as $productNaam => $aantal) {
        $aantal = (int)$aantal;

        if ($aantal <= 0) {
            unset($_SESSION['winkelwagen'][$productNaam]); // Verwijder product
        } else {
            $_SESSION['winkelwagen'][$productNaam] = $aantal; // Werk aantal bij
        }
    }
}

header("Location: winkelwagen.php"); // Terug naar winkelwagen
exit;
