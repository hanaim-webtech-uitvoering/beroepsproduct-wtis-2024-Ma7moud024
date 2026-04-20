<?php

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoofdpagina</title>
    <link rel="stylesheet" href="CSS/Hoofdpagina.css">
</head>

<body>
    <header>
        <nav>
            <a href="menu.php">Menu</a>
            <a href="profiel.php">Profiel</a>
            <a href="registratie_login.php">Inloggen</a>

            <form action="../loguit.php" method="post">
                <button type="submit">Uitloggen</button>
            </form>

            <a href="winkelmandje.php">Winkelwagen</a>
            <a href="mijnBestellingen.php">Mijn bestelling</a>
            <a href="bestellingoverzicht_personeel.php">Personeel Bestellingen Overzicht</a>
        </nav>
    </header>

    <main>
        <h1>Welkom op Mahmoud pizzaria!</h1>
        <p>Hier kunt u mijn heerlijke pizza's bestellen en genieten van een geweldige eetervaring!</p>
        <img src="../afbeeldingen/hoofdpagina-header-foto.jpg" alt="Pizza" width="500">

    </main>

    <footer>
        <a href="privacyverklaring.php">Privacy Verklaring</a>
    </footer>
</body>

</html>