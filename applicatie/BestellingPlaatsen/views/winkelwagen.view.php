<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Winkelwagen - Pizzaria</title>
    <link rel="stylesheet" href="../CSS/winkelwagen.css" />
</head>
<body>
<main>
    <section class="header">
        <h1>Winkelwagen</h1>
        <form action="/../Hoofdpagina.php" method="get">
            <input type="submit" value="Hoofdpagina">
        </form>
    </section>

    <?php if (!empty($winkelwagen)): ?>
        <form action="update_winkelwagen.php" method="post">
            <section>
                <?php foreach ($winkelwagen as $productNaam => $aantal): ?>
                    <?php
                    $product = $productenData[$productNaam] ?? null;
                    if (!$product) continue;
                    $prijs = $product['price'];
                    $type = $product['type_id'];
                    ?>
                    <div class="foto-met-caption">
                        <img src="<?= strtolower($productNaam) ?>.jpg" alt="<?= htmlspecialchars($productNaam) ?>">
                        <p><?= htmlspecialchars($productNaam) ?> (<?= $type ?>)</p>
                        <p>€<?= number_format($prijs, 2) ?></p>
                        <label for="aantal_<?= htmlspecialchars($productNaam) ?>">Aantal:</label>
                        <input type="number" name="aantallen[<?= htmlspecialchars($productNaam) ?>]" id="aantal_<?= htmlspecialchars($productNaam) ?>" value="<?= $aantal ?>" min="0">
                    </div>
                <?php endforeach; ?>
            </section>

            <input type="submit" value="Hoeveelheden bijwerken">
        </form>

        <form action="../BestellingPlaatsen/bestelling/Controllers/BestellingController.php" method="post">
            <section class="Afronden">
                <p>Totaal: €<?= number_format($totaal, 2) ?></p>

                <label for="adres">Bezorgadres:</label>
                <input type="text" name="adres" required placeholder="Vul je adres in">

                <?php foreach ($winkelwagen as $productNaam => $aantal): ?>
                    <input type="hidden" name="producten[<?= htmlspecialchars($productNaam) ?>]" value="<?= $aantal ?>">
                <?php endforeach; ?>

                <input type="submit" value="Bevestigen">
            </section>
        </form>
    <?php else: ?>
        <p style="text-align:center;">Je winkelwagen is leeg.</p>
    <?php endif; ?>
</main>

<footer>
    <p><a href="privacyverklaring.html">Privacy verklaring!</a></p>
</footer>
</body>
</html>