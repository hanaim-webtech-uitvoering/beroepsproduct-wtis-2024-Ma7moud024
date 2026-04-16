<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registreren</title>
    <link rel="stylesheet" href="../CSS/Registreren.css">
</head>
<body>
    <main>
        <h1>Registreren</h1>
        <form method="post">
            <label for="Username">Gebruikersnaam:</label>
            <input type="text" id="Username" name="Username" required><br>

            <label for="naam">Voornaam:</label>
            <input type="text" id="naam" name="naam" required><br>

            <label for="achternaam">Achternaam:</label>
            <input type="text" id="achternaam" name="achternaam" required><br>

            <label for="Adres">Adres:</label>
            <input type="text" id="Adres" name="Adres" required><br>

            <label for="wachtwoord">Wachtwoord:</label>
            <input type="password" id="wachtwoord" name="wachtwoord" required><br>

            <label for="role">Rol:</label>
            <select id="role" name="role" required>
                <option value="" disabled selected>Kies een rol</option>
                <option value="personnel">Personnel</option>
                <option value="client">Client</option>
            </select><br>

            <input type="submit" value="Registreren">
        </form>
    </main>
</body>
</html>
