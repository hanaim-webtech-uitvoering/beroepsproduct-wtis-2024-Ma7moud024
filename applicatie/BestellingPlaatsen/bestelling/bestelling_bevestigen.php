<!-- views/bestelling_bevestigen.php -->
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Bestelling</title>
    <link rel="stylesheet" href="../../CSS/bestelling_bevestigen.css">
</head>

<body>
    <header></header>
    <main>
        <div class="message"><?= htmlspecialchars($message ?? '') ?></div>
        <form action="/../Hoofdpagina.php" method="get">
            <input type="submit" value="Hoofdpagina" />
        </form>
    </main>
    <footer>&copy; 2025 Pizzeria</footer>
</body>

</html>