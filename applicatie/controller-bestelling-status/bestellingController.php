<?php
session_start();
require_once __DIR__ . '/../models-bestelling-status/BestellingModel.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
$orders = getBestellingenVanGebruiker($username);

// Laad de view en geef data door
require_once __DIR__ . '/../views-bestelling-status/bestellingStatusView.php';
