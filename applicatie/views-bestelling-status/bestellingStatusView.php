<?php
function vertaalStatus($statusNum) {
    switch ($statusNum) {
        case 1: return "Order in behandeling";
        case 2: return "Wordt bezorgd";
        case 3: return "Afgeleverd";
        default: return "Onbekende status";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../CSS/bestelling-status.css" />
    <title>Pizzaria</title>
</head>

<body>
    <header></header>
    <main>
        <section class="header">
            <h1>Bestelling status</h1>
            <form action="../Hoofdpagina.php" method="get">
                <input type="submit" value="Hoofdpagina" />
            </form>
        </section>
        <section>
            <?php if (!$orders): ?>
                <p>Er zijn geen bestellingen gevonden voor deze gebruiker.</p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div>
                        <p>Bestellingnr: #<?= htmlspecialchars($order['order_id']) ?></p>
                        <p>Datum: <?= htmlspecialchars(date("d-m-Y", strtotime($order['datetime']))) ?></p>
                        <p>Status: <?= vertaalStatus($order['status']) ?></p>
                        <p>Adres: <?= htmlspecialchars($order['address']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>
            Privacy verklaring!
            <a href="../privacyverklaring.html">Privacy verklaring!</a>
        </p>
    </footer>
</body>

</html>
