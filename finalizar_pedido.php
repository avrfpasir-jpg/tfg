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

$error_msg = '';
$pedido_id = 0;

try {
    $conexion->beginTransaction();

    // 1. Calcular total y verificar stock
    $total = 0;
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $conexion->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id IN ($placeholders) FOR UPDATE");
    $stmt->execute($ids);
    $productos_db = $stmt->fetchAll(PDO::FETCH_UNIQUE);

    foreach ($_SESSION['cart'] as $id => $qty) {
        if (!isset($productos_db[$id])) {
            throw new Exception("El producto con ID $id ya no existe.");
        }
        if ($productos_db[$id]['stock'] < $qty) {
            throw new Exception("No hay suficiente stock para: " . $productos_db[$id]['nombre']);
        }
        $total += $productos_db[$id]['precio'] * $qty;
    }

    // 2. Crear pedido
    $stmt = $conexion->prepare("INSERT INTO pedidos (usuario_id, total) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $total]);
    $pedido_id = $conexion->lastInsertId();

    // 3. Detalles y Actualizar Stock
    $stmt_detalle = $conexion->prepare("INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    $stmt_stock = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt_detalle->execute([$pedido_id, $id, $qty, $productos_db[$id]['precio']]);
        $stmt_stock->execute([$qty, $id]);
    }

    $conexion->commit();

    // Registrar el pedido en los logs
    include_once 'includes/seguridad.php';
    registrar_evento($conexion, 'NUEVO_PEDIDO', "Orden #$pedido_id | Usuario: " . $_SESSION['username'] . " | Total: " . number_format($total, 2) . "€", 1);

    unset($_SESSION['cart']);
    $_SESSION['mensaje'] = "¡Pedido #$pedido_id realizado con éxito!";
    $_SESSION['mensaje_tipo'] = "success";
    $exito = true;

} catch (Exception $e) {
    if ($conexion && $conexion->inTransaction()) {
        $conexion->rollBack();
    }

    include_once 'includes/seguridad.php';
    registrar_evento($conexion, 'ERROR_PEDIDO', "Error: " . $e->getMessage() . " | Usuario: " . ($_SESSION['username'] ?? 'invitado'), 3);

    $error_msg = $e->getMessage();
    $exito = false;
}
?>

<div class="text-center py-5">
    <?php if ($exito): ?>
        <div class="card border-0 shadow-sm p-5 bg-white">
            <h1 class="display-3 fw-black mb-4 text-success">✓ ÉXITO</h1>
            <p class="lead mb-5">Gracias por tu confianza. Tu pedido <strong>#<?= $pedido_id ?></strong> está en camino.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="index.php" class="btn btn-primary btn-lg px-5">Volver al inicio</a>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm p-5 bg-white">
            <h1 class="display-3 fw-black text-danger mb-4">✕ ERROR</h1>
            <p class="lead mb-4"><?= htmlspecialchars($error_msg) ?></p>
            <p class="text-muted">No se ha realizado ningún cargo ni modificación en tu selección.</p>
            <a href="carrito.php" class="btn btn-primary btn-lg mt-4 px-5">Volver al Carrito</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>