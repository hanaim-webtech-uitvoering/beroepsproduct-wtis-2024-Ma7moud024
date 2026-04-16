<?php
// controllers/BestellingController.php
session_start();

// Correct path to db_connectie.php based on file structure
require_once __DIR__ . '/../../../db_connectie.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/UserModel.php';

$pdo = maakVerbinding();
$orderModel = new OrderModel($pdo);
$userModel = new UserModel($pdo);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producten'])) {
    $producten = $_POST['producten'];

    // Verwerk het gekozen adres
    if (isset($_POST['adres_keuze'])) {
        if ($_POST['adres_keuze'] === 'geregistreerd') {
            $adres = $_POST['adres_geregistreerd'] ?? '';
        } elseif ($_POST['adres_keuze'] === 'nieuw') {
            $adres = $_POST['adres_nieuw'] ?? '';
        } else {
            $adres = '';
        }
    } else {
        $adres = '';
    }

    if (empty(trim($adres))) {
        $message = "Geen geldig adres opgegeven.";
    } else {
        try {
            $pdo->beginTransaction();

            $personnel = $userModel->getPersonnelUsername();
            if (!$personnel) {
                throw new Exception("Geen medewerker beschikbaar.");
            }

            $client_username = $_SESSION['username'] ?? 'gast';
            $client_name = isset($_SESSION['username'])
                ? $userModel->getClientName($_SESSION['username'])
                : 'gast';

            $order_id = $orderModel->createOrder($client_username, $client_name, $personnel, $adres);

            foreach ($producten as $product => $aantal) {
                $orderModel->addProductToOrder($order_id, $product, $aantal);
            }

            $pdo->commit();
            unset($_SESSION['winkelwagen']);

            $message = "Bestelling is geplaatst! Bedankt.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Fout: " . htmlspecialchars($e->getMessage());
        }
    }
} else {
    $message = "Geen producten gevonden om te bestellen.";
}

// Toon view
require_once __DIR__ . '/../../bestelling/bestelling_bevestigen.php';
