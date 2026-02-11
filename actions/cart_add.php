<?php
session_start();

$id = $_GET['id'] ?? null;

if ($id) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Añadir o incrementar
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
}

// Volver a la página anterior o al inicio
header("Location: " . ($_SERVER['HTTP_REFERER'] ?: '../index.php'));
exit();
?>