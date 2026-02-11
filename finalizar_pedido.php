<?php
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Lógica de pedido simplificada
try {
    $conexion->beginTransaction();

    // Calcular total
    $total = 0;
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $conexion->prepare("SELECT id, precio FROM productos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $productos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    foreach ($_SESSION['cart'] as $id => $qty) {
        $total += $productos[$id] * $qty;
    }

    // Crear pedido
    $stmt = $conexion->prepare("INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, 'completado')");
    $stmt->execute([$_SESSION['user_id'], $total]);
    $pedido_id = $conexion->lastInsertId();

    // Detalles del pedido
    $stmt = $conexion->prepare("INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt->execute([$pedido_id, $id, $qty, $productos[$id]]);
    }

    $conexion->commit();
    unset($_SESSION['cart']);
    $exito = true;
} catch (Exception $e) {
    $conexion->rollBack();
    $exito = false;
}
?>

<div class="text-center py-5">
    <?php if ($exito): ?>
        <h1 class="display-3 fw-black mb-4">¡GRACIAS POR TU COMPRA!</h1>
        <p class="lead mb-5">Tu pedido #<?= $pedido_id ?> ha sido procesado con éxito.</p>
        <a href="index.php" class="btn btn-primary btn-lg px-5">VOLVER AL INICIO</a>
    <?php else: ?>
        <h1 class="display-3 fw-black text-danger">ERROR</h1>
        <p class="lead">No pudimos procesar tu compra. Por favor, inténtalo de nuevo.</p>
        <a href="carrito.php" class="btn btn-primary mt-4">VOLVER AL CARRITO</a>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>