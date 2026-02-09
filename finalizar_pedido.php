<?php
session_start();
include 'conexion.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success = false;
$error = '';

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    try {
        $conexion->beginTransaction();

        $usuario_id = $_SESSION['user_id'];
        $total = 0;
        $items_to_process = [];

        // 1. Fetch current prices and check stock
        foreach ($_SESSION['cart'] as $producto_id => $cantidad) {
            $stmt = $conexion->prepare("SELECT nombre, precio, stock FROM productos WHERE id = ? FOR UPDATE");
            $stmt->execute([$producto_id]);
            $p = $stmt->fetch();

            if (!$p) {
                throw new Exception("Producto no encontrado.");
            }

            if ($p['stock'] < $cantidad) {
                throw new Exception("Stock insuficiente para: " . $p['nombre']);
            }

            $subtotal = $p['precio'] * $cantidad;
            $total += $subtotal;
            $items_to_process[] = [
                'id' => $producto_id,
                'cantidad' => $cantidad,
                'precio' => $p['precio']
            ];
        }

        // 2. Create the Order
        $stmt_pedido = $conexion->prepare("INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, 'completado')");
        $stmt_pedido->execute([$usuario_id, $total]);
        $pedido_id = $conexion->lastInsertId();

        // 3. Save Details and Update Stock
        $stmt_detalle = $conexion->prepare("INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
        $stmt_update_stock = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

        foreach ($items_to_process as $item) {
            $stmt_detalle->execute([$pedido_id, $item['id'], $item['cantidad'], $item['precio']]);
            $stmt_update_stock->execute([$item['cantidad'], $item['id']]);
        }

        $conexion->commit();
        unset($_SESSION['cart']);
        $success = true;

    } catch (Exception $e) {
        $conexion->rollBack();
        $error = "Error al procesar el pedido: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pedido - Tienda Segura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center vh-100">
    <div class="container text-center">
        <?php if ($success): ?>
            <div class="card p-5 shadow mx-auto" style="max-width: 500px;">
                <h1 class="display-1 text-success">✅</h1>
                <h2 class="mb-3">¡Gracias por tu compra!</h2>
                <p class="text-muted">Tu pedido #<?= $pedido_id ?> ha sido procesado correctamente y el stock actualizado.
                </p>
                <a href="index.php" class="btn btn-primary mt-4">Volver al inicio</a>
            </div>
        <?php elseif ($error): ?>
            <div class="card p-5 shadow mx-auto" style="max-width: 500px;">
                <h1 class="display-1 text-danger">⚠️</h1>
                <h2 class="mb-3">Ups... Algo salió mal</h2>
                <div class="alert alert-danger"><?= $error ?></div>
                <a href="carrito.php" class="btn btn-outline-primary mt-2">Volver al carrito</a>
            </div>
        <?php else: ?>
            <div class="alert alert-danger d-inline-block">No hay un pedido activo.</div>
            <br>
            <a href="index.php" class="btn btn-secondary mt-2">Volver</a>
        <?php endif; ?>
    </div>
</body>

</html>