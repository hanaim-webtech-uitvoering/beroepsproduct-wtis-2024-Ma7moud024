<?php
session_start();
require_once __DIR__ . '/models/ProductModel.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/controllers/WinkelwagenController.php';

$toegevoegd_bericht = null;

// Winkelwagenactie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toevoegen_winkelwagen') {
    if (!empty($_POST['product_name'])) {
        $toegevoegd_bericht = voegToeAanWinkelwagen($_POST['product_name']);
    }
}

// Gebruikersgegevens
$isLoggedIn = isset($_SESSION['username']);
$isPersonnel = false;

if ($isLoggedIn) {
    $rol = haalGebruikersRol($_SESSION['username']) ?? $_SESSION['role'] ?? null;
    if ($rol === 'personnel') {
        $isPersonnel = true;
    }
}

$producten = haalAlleProductenMetCategorieen();
$ingredientenPerProduct = haalIngredientenPerProduct();
$winkelwagen_aantal = telItemsInWinkelwagen();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8" />
    <title>Productenlijst</title>
    <link rel="stylesheet" href="../CSS/hoofdpagina.css" />
</head>
<body>
    <header>
        <?php if ($isLoggedIn): ?>
            <div class="user-info">
                <span>Welkom, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
            </div>
        <?php endif; ?>
    </header>

    <nav>
        <div class="foto-met-caption">
            <img src="hoofdpagina-header-foto.jpg" alt="Eerste foto">
        </div>
        <form action="../bestellingPlaatsen/winkelwagen.php" method="post">
            <input type="submit" value="🛒 (<?= $winkelwagen_aantal ?>)">
        </form>
        <?php if ($isLoggedIn): ?>
            <form action="../InloggenEnRegistratie/loguit.php" method="post">
                <input type="submit" value="🚪 Uitloggen">
            </form>
        <?php else: ?>
            <form action="../InloggenEnRegistratie/Inlog_pagina.php" method="post">
                <input type="submit" value="👤 Inloggen">
            </form>
        <?php endif; ?>
        <form action="bestelling-status.php" method="get">
            <input type="submit" value="Status">
        </form>
        <?php if ($isPersonnel): ?>
            <form action="alle-bestellingen.php" method="get">
                <input type="submit" value="📋 Alle Bestellingen">
            </form>
        <?php endif; ?>
    </nav>

    <section class="pagina-titel">
        <h1>Overzicht van producten</h1>
        <p>Bekijk onze heerlijke producten</p>
    </section>

    <main>
        <?php if ($toegevoegd_bericht): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin: 10px 0;">
                <?= $toegevoegd_bericht ?>
            </div>
        <?php endif; ?>

        <section>
            <?php if (!empty($producten)): ?>
                <?php foreach ($producten as $product): ?>
                    <div class="product">
                        <div class="naam"><?= htmlspecialchars($product['product_name']) ?></div>
                        <div class="prijs">Prijs: €<?= number_format($product['price'], 2, ',', '.') ?></div>
                        <div class="type">Categorie: <?= htmlspecialchars($product['category_name']) ?></div>

                        <?php if (!empty($ingredientenPerProduct[$product['product_name']] ?? [])): ?>
                            <div class="ingredienten">
                                Ingrediënten:
                                <ul>
                                    <?php foreach ($ingredientenPerProduct[$product['product_name']] as $ingredient): ?>
                                        <li><?= htmlspecialchars($ingredient) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <input type="hidden" name="action" value="toevoegen_winkelwagen">
                            <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>">
                            <input type="submit" value="Toevoegen aan winkelwagen">
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Er zijn geen producten gevonden.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
          <p>
            Privacy verklaring!
            <a href="privacyverklaring.php">Privacy verklaring!</a>
        </p>
    </footer>
</body>
</html>
