<?php
function voegToeAanWinkelwagen($productNaam) {
    if (!isset($_SESSION['winkelwagen'])) {
        $_SESSION['winkelwagen'] = [];
    }

    if (isset($_SESSION['winkelwagen'][$productNaam])) {
        $_SESSION['winkelwagen'][$productNaam]++;
    } else {
        $_SESSION['winkelwagen'][$productNaam] = 1;
    }

    return "Product '" . htmlspecialchars($productNaam) . "' is toegevoegd aan je winkelwagen!";
}

function telItemsInWinkelwagen() {
    return isset($_SESSION['winkelwagen']) ? array_sum($_SESSION['winkelwagen']) : 0;
}
