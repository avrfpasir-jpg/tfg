<?php
session_start();

$id = $_GET['id'] ?? null;

if ($id) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Límite de seguridad: máximo 100 items distintos o cantidad exagerada
    if (count($_SESSION['cart']) >= 50 && !isset($_SESSION['cart'][$id])) {
        // Bloquear nuevos items si ya hay 50 distintos
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?: '../index.php'));
        exit();
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