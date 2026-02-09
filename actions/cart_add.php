<?php
session_start();

$id = $_GET['id'] ?? null;

if ($id) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Incrementar cantidad
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
}

// Redirigir de vuelta (o al carrito)
header("Location: " . ($_SERVER['HTTP_REFERER'] ?: '../index.php'));
exit();
?>