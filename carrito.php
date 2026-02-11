<?php
$pageTitle = "Tu Carrito";
include 'includes/header.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo '<div class="text-center py-5"><h3>Tu carrito está vacío</h3><a href="index.php" class="btn btn-primary mt-3">Volver al catálogo</a></div>';
} else {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $items = $stmt->fetchAll();

    $total = 0;
    ?>
    <h2 class="fw-black mb-4">TU SELECCIÓN</h2>
    <div class="table-responsive">
        <table class="table table-bordered border-dark">
            <thead class="table-dark">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item):
                    $qty = $_SESSION['cart'][$item['id']];
                    $subtotal = $item['precio'] * $qty;
                    $total += $subtotal;
                    ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($item['nombre']) ?></td>
                        <td><?= $qty ?></td>
                        <td><?= number_format($subtotal, 2) ?> €</td>
                    </tr>
                <?php endforeach; ?>
                <tr class="table-light">
                    <td colspan="2" class="text-end fw-black">TOTAL</td>
                    <td class="fw-black fs-4"><?= number_format($total, 2) ?> €</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="text-end mt-4">
        <a href="finalizar_pedido.php" class="btn btn-primary btn-lg">FINALIZAR COMPRA</a>
    </div>
    <?php
}
include 'includes/footer.php';
?>