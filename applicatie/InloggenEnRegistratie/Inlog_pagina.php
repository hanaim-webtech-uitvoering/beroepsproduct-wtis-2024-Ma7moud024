<?php
session_start();
require_once __DIR__ . '/../db_connectie.php';
require_once __DIR__ . '/Login_controllers/LoginController.php';

$db = maakVerbinding();
$controller = new LoginController($db);

$foutmelding = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';
    $foutmelding = $controller->login($username, $wachtwoord);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/Pizzaria.css" />
    <title>Pizzaria - Inloggen</title>
</head>
<body>
    <header></header>
    <main>
        <h1>Login</h1>
        <section>
            <?php if ($foutmelding): ?>
                <p style="color:red;"><?php echo htmlspecialchars($foutmelding); ?></p>
            <?php endif; ?>
            <form action="" method="post">
                <div>
                    <label for="username">Gebruikersnaam:</label>
                    <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($username); ?>"><br><br>
                </div>
                <div>
                    <label for="wachtwoord">Wachtwoord:</label>
                    <input type="password" id="wachtwoord" name="wachtwoord" required><br><br>
                </div>
                <input type="submit" value="Inloggen">
            </form>
            <form action="Registreren.php" method="get" style="margin-top:10px;">
                <input type="submit" value="Registreren">
            </form>
            <p>Wachtwoord vergeten?</p>
        </section>
    </main>
    <footer>
        <p>
            <a href="privacyverklaring.html">Privacy verklaring!</a>
        </p>
    </footer>
</body>
</html>
