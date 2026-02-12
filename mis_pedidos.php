<?php
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conexion->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha DESC");
$stmt->execute([$user_id]);
$pedidos = $stmt->fetchAll();
?>

<h2 class="fw-black mb-5 text-uppercase">MIS PEDIDOS</h2>

<?php if (empty($pedidos)): ?>
    <div class="card border-0 shadow-sm p-5 text-center">
        <p class="lead text-muted mb-4">Aún no has realizado ninguna compra.</p>
        <a href="index.php" class="btn btn-primary d-inline-block px-5 mx-auto">Ir a la tienda</a>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm overflow-hidden">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="small fw-bold text-uppercase">
                    <th class="px-4 py-3">Referencia</th>
                    <th class="py-3">Fecha</th>
                    <th class="py-3">Importe Total</th>
                    <th class="text-end px-4 py-3">Detalle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td class="px-4 fw-bold">#ORD-
                            <?= $p['id'] ?>
                        </td>
                        <td class="text-muted small">
                            <?= $p['fecha'] ?>
                        </td>
                        <td class="fw-black fs-5">
                            <?= number_format($p['total'], 2) ?> €
                        </td>
                        <td class="text-end px-4">
                            <span class="badge bg-success px-3">Completado</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>