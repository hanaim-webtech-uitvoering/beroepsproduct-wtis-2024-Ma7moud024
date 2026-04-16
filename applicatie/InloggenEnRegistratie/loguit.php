<?php
session_start();

// Vernietig alle sessie data
session_unset();
session_destroy();

// Redirect terug naar hoofdpagina
header("Location: /../hoofdpagina.php");
exit();
?>