<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mi_id = $_SESSION['user_id'];

$sql = "SELECT pd.*, p.nombre, p.imagen, ped.fecha, u.username as comprador, u.email as comprador_email
        FROM pedido_detalles pd
        JOIN productos p ON pd.producto_id = p.id
        JOIN pedidos ped ON pd.pedido_id = ped.id
        JOIN usuarios u ON ped.usuario_id = u.id
        WHERE p.usuario_id = ?
        ORDER BY ped.fecha DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute([$mi_id]);
$ventas = $stmt->fetchAll();

$total_ingresos = 0;
foreach ($ventas as $v) {
    $total_ingresos += ($v['cantidad'] * $v['precio_unitario']);
}

$pageTitle = "REVENUE_TRACKER";
include 'includes/header.php';
?>

<div class="row mb-5">
    <div class="col-md-12">
        <div class="bg-black text-white p-5 border border-4 border-dark shadow-lg position-relative"
            style="box-shadow: 10px 10px 0px var(--acid-green);">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="text-uppercase small fw-bold text-warning mb-2">// BANKROLL_STDOUT</h5>
                    <h2 class="display-4 fw-black mb-0"><?= number_format($total_ingresos, 2) ?> €</h2>
                </div>
                <div class="col-auto text-end">
                    <span class="display-3 fw-black d-block mb-0 text-white"><?= count($ventas) ?></span>
                    <span class="small fw-bold text-uppercase opacity-75">T_SALES_LOG</span>
                </div>
            </div>
        </div>
    </div>
</div>

<h4 class="fw-bold mb-4 text-uppercase border-bottom border-4 border-dark d-inline-block pb-2">📂 TRANSACTION_FEED</h4>

<?php if (empty($ventas)): ?>
    <div class="bg-white border border-4 border-dark p-5 text-center shadow">
        <h3 class="fw-bold mb-3">NO_DATA_LOGGED</h3>
        <p class="text-muted">Todavía no has registrado transacciones en la red. Tu stock sigue intacto.</p>
        <a href="mis_productos.php" class="btn btn-dark mt-3">GESTION_CATALOG</a>
    </div>
<?php else: ?>
    <div class="table-responsive bg-white border border-4 border-dark shadow">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="p-3">PRODUCT_ID</th>
                    <th class="p-3">TIMESTAMP</th>
                    <th class="p-3">BUYER_USR</th>
                    <th class="p-3 text-center">QTY</th>
                    <th class="p-3 text-end">TOTAL</th>
                </tr>
            </thead>
            <tbody class="fw-bold">
                <?php foreach ($ventas as $v): ?>
                    <tr>
                        <td class="p-3">
                            <div class="d-flex align-items-center gap-3">
                                <?php if ($v['imagen']): ?>
                                    <span class="badge bg-dark p-1"><img src="uploads/<?= $v['imagen'] ?>" width="30" height="30"
                                            style="object-fit: cover;"></span>
                                <?php endif; ?>
                                <span class="text-uppercase"><?= htmlspecialchars($v['nombre']) ?></span>
                            </div>
                        </td>
                        <td class="p-3 small font-monospace">
                            <?= date("Y-m-d H:i", strtotime($v['fecha'])) ?>
                        </td>
                        <td class="p-3">
                            <div class="lh-1">
                                <span
                                    class="d-block small text-primary"><?= htmlspecialchars(strtoupper($v['comprador'])) ?></span>
                                <span class="text-muted small"
                                    style="font-size: 0.65rem;"><?= htmlspecialchars($v['comprador_email']) ?></span>
                            </div>
                        </td>
                        <td class="p-3 text-center">
                            ×<?= $v['cantidad'] ?>
                        </td>
                        <td class="p-3 text-end text-success">
                            +<?= number_format($v['cantidad'] * $v['precio_unitario'], 2) ?> €
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>