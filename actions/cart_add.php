<?php
session_start();
include '../conexion.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // 1. Validar que el producto existe
    $stmt = $conexion->prepare("SELECT id, nombre FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();

    if ($prod) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++;
        } else {
            $_SESSION['cart'][$id] = 1;
        }

        $_SESSION['mensaje'] = "¡" . htmlspecialchars($prod['nombre']) . " añadido al carrito!";
        $_SESSION['mensaje_tipo'] = "success";
    } else {
        $_SESSION['mensaje'] = "Error: El producto no existe.";
        $_SESSION['mensaje_tipo'] = "danger";
    }
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?: '../index.php'));
exit();
?>