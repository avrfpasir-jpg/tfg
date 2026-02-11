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
    <div class="row">
        <div class="col-lg-8">
            <h2 class="fw-black mb-5">MI SELECCIÓN</h2>
            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-uppercase small fw-bold">
                                <th class="px-4 py-3">Producto</th>
                                <th class="text-center py-3">Cantidad</th>
                                <th class="text-end px-4 py-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item):
                                $qty = $_SESSION['cart'][$item['id']];
                                $subtotal = $item['precio'] * $qty;
                                $total += $subtotal;
                                ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded bg-light me-3"
                                                style="width: 50px; height: 50px; overflow: hidden;">
                                                <?php if ($item['imagen']): ?>
                                                    <img src="uploads/<?= $item['imagen'] ?>" class="w-100 h-100"
                                                        style="object-fit: cover;">
                                                <?php else: ?>
                                                    <div
                                                        class="w-100 h-100 d-flex align-items-center justify-content-center text-muted small">
                                                        📷</div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <span class="fw-bold d-block"><?= htmlspecialchars($item['nombre']) ?></span>
                                                <span class="text-muted small"><?= number_format($item['precio'], 2) ?> € /
                                                    ud.</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="badge bg-light text-dark border p-2 px-3 fw-bold"><?= $qty ?></span>
                                    </td>
                                    <td class="text-end px-4 py-3 fw-bold">
                                        <?= number_format($subtotal, 2) ?> €
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <a href="index.php" class="btn btn-link text-dark fw-bold text-decoration-none p-0">← Seguir comprando</a>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 mt-5 mt-lg-0 sticky-top" style="top: 2rem;">
                <h4 class="fw-black mb-4">RESUMEN</h4>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span><?= number_format($total, 2) ?> €</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted">Envío</span>
                    <span class="text-success fw-bold">GRATIS</span>
                </div>
                <div class="border-top pt-3 d-flex justify-content-between align-items-center mb-5">
                    <span class="fw-bold fs-5">TOTAL</span>
                    <span class="price-tag fs-3"><?= number_format($total, 2) ?> €</span>
                </div>
                <a href="finalizar_pedido.php" class="btn btn-primary btn-lg w-100 py-3 shadow">FINALIZAR COMPRA</a>
                <p class="text-muted small text-center mt-3">Pago 100% seguro garantizado</p>
            </div>
        </div>
    </div>
    <?php
}
include 'includes/footer.php';
?>