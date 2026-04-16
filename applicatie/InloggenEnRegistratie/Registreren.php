<?php
require_once __DIR__ . '/../InloggenEnRegistratie/Registratie_controllers/RegisterController.php';

$controller = new RegisterController();
$error = $controller->handleForm();

require_once __DIR__ . '/../InloggenEnRegistratie/Registratie_view/register_form.php';

if ($error) {
    echo "<p style='color:red;'>$error</p>";
}
