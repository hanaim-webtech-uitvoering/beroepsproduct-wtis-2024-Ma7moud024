<?php
// Start sessie met veilige configuratie
require_once 'db_connectie.php';

// Configureer veilige sessie en beveiligingsheaders
configureSecureSession();
session_start();
setSecurityHeaders();

// Controleer of gebruiker is ingelogd en personeel is
$isPersonnel = false;
if (isset($_SESSION['role']) && $_SESSION['role'] === 'personnel') {
    $isPersoneel = true;
} else {
    // Redirect naar login als geen personeel
    header("Location: ../InloggenEnRegistratie/Inlog_pagina.php");
    exit();
}

// CSRF token genereren
$csrf_token = generateCSRFToken();

try {
    $pdo = maakVerbinding();
    
    // Functie om status nummer te vertalen naar tekst
    function getStatusText($status) {
        $statuses = [
            1 => "Wordt bereid",
            2 => "Bezorger onderweg", 
            3 => "Afgeleverd"
        ];
        return $statuses[$status] ?? "Onbekend";
    }
    
    // Functie om bestelde producten op te halen
    function getOrderProducts($pdo, $order_id) {
        $sql = "SELECT product_name, quantity FROM Pizza_Order_product WHERE order_id = :order_id";
        return executeQuery($pdo, $sql, [':order_id' => $order_id])->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Verwerk status update als formulier is ingediend
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
        // CSRF token verificatie
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            die('Beveiligingsfout: Ongeldig token');
        }
        
        // Valideer input
        $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
        $new_status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
        
        if ($order_id && $new_status && in_array($new_status, [1, 2, 3])) {
            $personnel_username = sanitizeInput($_SESSION['username']);
            
            $update_sql = "UPDATE Pizza_order SET status = :status, personnel_username = :personnel_username WHERE order_id = :order_id";
            
            try {
                executeQuery($pdo, $update_sql, [
                    ':status' => $new_status,
                    ':personnel_username' => $personnel_username,
                    ':order_id' => $order_id
                ]);
                $success_message = "Status succesvol bijgewerkt!";
            } catch (Exception $e) {
                $error_message = "Fout bij bijwerken van status.";
                error_log('Status update error: ' . $e->getMessage());
            }
        } else {
            $error_message = "Ongeldige invoer.";
        }
    }
    
    // Haal specifieke bestelling op als order_id is opgegeven
    $selected_order = null;
    $selected_order_products = [];
    if (isset($_GET['order_id'])) {
        $order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
        if ($order_id) {
            $sql_order = "SELECT * FROM Pizza_order WHERE order_id = :order_id";
            $selected_order = executeQuery($pdo, $sql_order, [':order_id' => $order_id])->fetch(PDO::FETCH_ASSOC);
            
            // Haal producten op voor geselecteerde bestelling
            if ($selected_order) {
                $selected_order_products = getOrderProducts($pdo, $order_id);
            }
        }
    }
    
    // Haal alle bestellingen op voor overzicht
    $sql_all = "SELECT * FROM Pizza_order ORDER BY datetime DESC";
    $alle_bestellingen = executeQuery($pdo, $sql_all)->fetchAll(PDO::FETCH_ASSOC);
    
    // Haal producten op voor alle bestellingen (voor overzicht)
    $alle_bestelling_producten = [];
    foreach ($alle_bestellingen as $bestelling) {
        $alle_bestelling_producten[$bestelling['order_id']] = getOrderProducts($pdo, $bestelling['order_id']);
    }
    
} catch (Exception $e) {
    error_log('Database error in alle-bestellingen.php: ' . $e->getMessage());
    die("Er is een technische fout opgetreden. Probeer het later opnieuw.");
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/Bestelling-details.css" />
    <title>Pizzaria - Bestelling Beheer</title>
    <style>
        .order-list {
            margin: 20px 0;
        }
        .order-item {
            border: 1px solid #ddd;
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .order-header {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .status-1 { background-color: #fff3cd; }
        .status-2 { background-color: #d1ecf1; }
        .status-3 { background-color: #d4edda; }
        .status-form {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .detail-view {
            border: 2px solid #007bff;
            background-color: #e3f2fd;
        }
        .products-section {
            margin: 15px 0;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        .products-section h4 {
            margin-top: 0;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .product-item {
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }
        .product-item:last-child {
            border-bottom: none;
        }
        .product-name {
            font-weight: bold;
            color: #2c3e50;
        }
        .product-quantity {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        .products-summary {
            font-size: 0.9em;
            color: #666;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <header>
        <h1>Bestelling Beheer - Personeel</h1>
        <nav>
            <form action="../hoofdpagina.php" method="get" style="display: inline;">
                <input type="submit" value="← Terug naar hoofdpagina">
            </form>
        </nav>
    </header>
    
    <main>
        <?php if (isset($success_message)): ?>
            <div class="success"><?= sanitizeInput($success_message) ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error"><?= sanitizeInput($error_message) ?></div>
        <?php endif; ?>

        <?php if ($selected_order): ?>
            <!-- Gedetailleerde weergave van geselecteerde bestelling -->
            <section class="header">
                <h2>Bestelling Details - Order #<?= sanitizeInput($selected_order['order_id']) ?></h2>
                <form action="?" method="get">
                    <input type="submit" value="← Terug naar alle bestellingen">
                </form>
            </section>
            
            <section>
                <div class="order-item detail-view status-<?= (int)$selected_order['status'] ?>">
                    <div class="order-header">
                        Bestelling #<?= sanitizeInput($selected_order['order_id']) ?> - 
                        Status: <?= getStatusText($selected_order['status']) ?>
                    </div>
                    
                    <p><strong>Klantgegevens:</strong></p>
                    <ul>
                        <li><strong>Naam:</strong> <?= sanitizeInput($selected_order['client_name'] ?? 'Niet beschikbaar') ?></li>
                        <li><strong>Adres:</strong> <?= sanitizeInput($selected_order['address'] ?? 'Niet beschikbaar') ?></li>
                        <li><strong>Besteldatum:</strong> <?= sanitizeInput($selected_order['datetime'] ?? 'Niet beschikbaar') ?></li>
                    </ul>
                    
                    <?php if ($selected_order['personnel_username']): ?>
                        <p><strong>Toegewezen personeel:</strong> <?= sanitizeInput($selected_order['personnel_username']) ?></p>
                    <?php endif; ?>
                    
                    <!-- Bestelde producten sectie -->
                    <div class="products-section">
                        <h4>Bestelde Producten:</h4>
                        <?php if (!empty($selected_order_products)): ?>
                            <?php foreach ($selected_order_products as $product): ?>
                                <div class="product-item">
                                    <span class="product-name"><?= sanitizeInput($product['product_name']) ?></span>
                                    <span class="product-quantity"> - Aantal: <?= (int)$product['quantity'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p><em>Geen producten gevonden voor deze bestelling.</em></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Status update formulier -->
                    <div class="status-form">
                        <h3>Status bijwerken:</h3>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= (int)$selected_order['order_id'] ?>">
                            <input type="hidden" name="update_status" value="1">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            
                            <label>
                                <input type="radio" name="status" value="1" <?= $selected_order['status'] == 1 ? 'checked' : '' ?>>
                                Wordt bereid
                            </label><br>
                            
                            <label>
                                <input type="radio" name="status" value="2" <?= $selected_order['status'] == 2 ? 'checked' : '' ?>>
                                Bezorger onderweg
                            </label><br>
                            
                            <label>
                                <input type="radio" name="status" value="3" <?= $selected_order['status'] == 3 ? 'checked' : '' ?>>
                                Afgeleverd
                            </label><br><br>
                            
                            <input type="submit" value="Status bijwerken">
                        </form>
                    </div>
                </div>
            </section>
            
        <?php else: ?>
            <!-- Overzicht van alle bestellingen -->
            <section class="header">
                <h2>Alle Bestellingen</h2>
                <p>Klik op een bestelling voor meer details</p>
            </section>
            
            <section class="order-list">
                <?php if (!empty($alle_bestellingen)): ?>
                    <?php foreach ($alle_bestellingen as $bestelling): ?>
                        <div class="order-item status-<?= (int)$bestelling['status'] ?>">
                            <div class="order-header">
                                <a href="?order_id=<?= (int)$bestelling['order_id'] ?>" style="text-decoration: none; color: inherit;">
                                    Bestelling #<?= sanitizeInput($bestelling['order_id']) ?> - 
                                    <?= sanitizeInput($bestelling['client_name'] ?? 'Onbekende klant') ?>
                                </a>
                            </div>
                            
                            <p><strong>Status:</strong> <?= getStatusText($bestelling['status'] ?? 0) ?></p>
                            <p><strong>Adres:</strong> <?= sanitizeInput($bestelling['address'] ?? 'Niet beschikbaar') ?></p>
                            <p><strong>Besteldatum:</strong> <?= sanitizeInput($bestelling['datetime'] ?? 'Niet beschikbaar') ?></p>
                            
                            <?php if ($bestelling['personnel_username'] ?? false): ?>
                                <p><strong>Personeel:</strong> <?= sanitizeInput($bestelling['personnel_username']) ?></p>
                            <?php endif; ?>
                            
                            <!-- Korte producten weergave -->
                            <?php if (!empty($alle_bestelling_producten[$bestelling['order_id']])): ?>
                                <div class="products-summary">
                                    <strong>Producten:</strong> 
                                    <?php 
                                    $product_names = array_map(function($p) { 
                                        return sanitizeInput($p['product_name']) . ' (' . (int)$p['quantity'] . 'x)'; 
                                    }, $alle_bestelling_producten[$bestelling['order_id']]);
                                    echo implode(', ', $product_names);
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Quick status update -->
                            <div class="status-form">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?= (int)$bestelling['order_id'] ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="1" <?= $bestelling['status'] == 1 ? 'selected' : '' ?>>Wordt bereid</option>
                                        <option value="2" <?= $bestelling['status'] == 2 ? 'selected' : '' ?>>Bezorger onderweg</option>
                                        <option value="3" <?= $bestelling['status'] == 3 ? 'selected' : '' ?>>Afgeleverd</option>
                                    </select>
                                </form>
                                
                                <a href="?order_id=<?= (int)$bestelling['order_id'] ?>" style="margin-left: 10px;">
                                    <button type="button">Details bekijken</button>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Er zijn geen bestellingen gevonden.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>
            <a href="privacyverklaring.php">Privacy verklaring</a>
        </p>
    </footer>
</body>

</html>