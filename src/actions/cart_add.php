<?php
session_start();
include '../includes/conexion.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // 1. Validar que el producto existe y tiene stock
    $stmt = $conexion->prepare("SELECT id, nombre, stock FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();

    if ($prod) {
        // 2. Comprobar stock (Seguridad Backend)
        if ($prod['stock'] <= 0) {
            $_SESSION['mensaje'] = "Error: Lo sentimos, el producto '" . htmlspecialchars($prod['nombre']) . "' ya no tiene stock.";
            $_SESSION['mensaje_tipo'] = "danger";
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?: '../index.php'));
            exit();
        }

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