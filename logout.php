<?php
// Commencez la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Détruisez la session
session_unset(); // Supprime toutes les variables de session
session_destroy(); // Détruit la session actuelle

// Redirigez vers la page index.php
header("Location: index.php");
exit();
?>
