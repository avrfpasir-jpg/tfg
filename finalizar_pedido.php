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

$pageTitle = "Pedido Finalizado";
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <?php if ($success): ?>
            <div class="bg-white border border-4 border-dark p-5 shadow-lg position-relative"
                style="box-shadow: 10px 10px 0px var(--acid-green);">
                <div class="display-1 mb-4">🆗</div>
                <h2 class="fw-black text-uppercase mb-3">#ORDER_SUCCESSFUL</h2>
                <p class="fs-5 mb-4 font-monospace">Tu pedido #<?= $pedido_id ?> ha sido inyectado en la red de envíos
                    correctamente.</p>
                <div class="border-top border-dark pt-4 mt-4">
                    <a href="index.php" class="btn btn-primary btn-lg px-5">RETURN_TO_BASE</a>
                </div>
            </div>
        <?php elseif ($error): ?>
            <div class="bg-black text-white border border-4 border-danger p-5 shadow-lg">
                <div class="display-1 mb-4 text-danger">⚠️</div>
                <h2 class="fw-black text-uppercase mb-3">#SYSTEM_FAILURE</h2>
                <div class="bg-danger text-white p-3 mb-4 fw-bold font-monospace"><?= $error ?></div>
                <a href="carrito.php" class="btn btn-outline-light mt-2">RELOAD_CART</a>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">No hay un pedido activo.</div>
            <br>
            <a href="index.php" class="btn btn-secondary mt-2">Volver</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>