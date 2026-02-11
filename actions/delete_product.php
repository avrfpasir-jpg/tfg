<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$usuario_id = $_SESSION['user_id'];
$producto_id = $_GET['id'] ?? 0;

// Verificar que el producto pertenece al usuario
$stmt = $conexion->prepare("SELECT imagen FROM productos WHERE id = ? AND usuario_id = ?");
$stmt->execute([$producto_id, $usuario_id]);
$producto = $stmt->fetch();

if ($producto) {
    // Borrar imagen si existe
    if ($producto['imagen'] && file_exists("../uploads/" . $producto['imagen'])) {
        unlink("../uploads/" . $producto['imagen']);
    }

    $stmt_del = $conexion->prepare("DELETE FROM productos WHERE id = ? AND usuario_id = ?");
    $stmt_del->execute([$producto_id, $usuario_id]);
    $_SESSION['flash_success'] = "Producto eliminado correctamente.";
}

header("Location: ../mis_productos.php");
exit();
