<?php
session_start();
include 'conexion.php';

$productos_carrito = [];
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';

    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $productos_db = $stmt->fetchAll();

    foreach ($productos_db as $p) {
        $cantidad = $_SESSION['cart'][$p['id']];
        $subtotal = $p['precio'] * $cantidad;
        $total += $subtotal;

        $productos_carrito[] = [
            'id' => $p['id'],
            'nombre' => $p['nombre'],
            'precio' => $p['precio'],
            'cantidad' => $cantidad,
            'subtotal' => $subtotal,
            'imagen' => $p['imagen']
        ];
    }
}

$pageTitle = "Tu Carrito";
include 'includes/header.php';
?>

<h2 class="fw-black mb-4 border-bottom border-4 border-dark d-inline-block pb-2 text-uppercase">// CART_RECAP</h2>

<?php if (empty($productos_carrito)): ?>
    <div class="alert alert-warning">Tu carrito está vacío.</div>
    <a href="index.php" class="btn btn-primary">Ir a comprar</a>
<?php else: ?>
    <div class="bg-white border border-4 border-dark p-4 shadow mb-5">
        <div class="card-body p-0">
            <table class="table align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="p-3">DATA_PRODUCT</th>
                        <th class="p-3 text-center">UNIT_PRICE</th>
                        <th class="p-3 text-center">QTY</th>
                        <th class="p-3 text-center">SUBTOTAL</th>
                        <th class="p-3 text-end">EXEC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos_carrito as $item): ?>
                        <tr>
                            <td class="p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <?php if ($item['imagen']): ?>
                                        <div class="bg-dark p-1 border border-dark">
                                            <img src="uploads/<?= $item['imagen'] ?>" width="50" height="50"
                                                style="object-fit: cover; opacity: 0.8;">
                                        </div>
                                    <?php endif; ?>
                                    <span class="fw-bold text-uppercase"><?= htmlspecialchars($item['nombre']) ?></span>
                                </div>
                            </td>
                            <td class="p-3 text-center font-monospace">
                                <?= number_format($item['precio'], 2) ?> €
                            </td>
                            <td class="p-3 text-center fw-black">
                                [ <?= $item['cantidad'] ?> ]
                            </td>
                            <td class="p-3 text-center fw-black text-primary">
                                <?= number_format($item['subtotal'], 2) ?> €
                            </td>
                            <td class="p-3 text-end">
                                <a href="actions/cart_remove.php?id=<?= $item['id'] ?>" class="btn btn-dark btn-sm">REMOVE</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end h3 fw-black p-4 text-uppercase">TOTAL_AMOUNT:</th>
                        <th colspan="2" class="h3 fw-black p-4 text-black border-start border-4 border-dark text-end"
                            style="background-color: var(--acid-green);">
                            <?= number_format($total, 2) ?> €
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-between gap-2 mt-4 mt-5">
        <a href="index.php" class="btn btn-dark btn-lg px-4">KEEP_BROWSING</a>
        <a href="finalizar_pedido.php" class="btn btn-primary btn-lg px-5 py-3">CHECK_OUT_ORDER</a>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>