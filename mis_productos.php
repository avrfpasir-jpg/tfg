<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['user_id'];

// Obtener productos del usuario
$stmt = $conexion->prepare("SELECT p.*, c.nombre as categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.usuario_id = ?");
$stmt->execute([$usuario_id]);
$mis_productos = $stmt->fetchAll();

$pageTitle = "INVENTORY_LOG";
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-5 border-bottom border-4 border-dark pb-3">
    <h2 class="fw-black mb-0">📦 STOCK_MANAGER</h2>
    <a href="subir_producto.php" class="btn btn-primary px-4">ADD_NEW_ITEM</a>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="bg-success text-white p-3 mb-4 fw-bold shadow">[OK] <?= $_SESSION['flash_success'] ?></div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (empty($mis_productos)): ?>
    <div class="bg-white border border-4 border-dark p-5 text-center shadow">
        <h3 class="fw-bold mb-3">LIST_EMPTY</h3>
        <p class="text-muted">No has indexado ningún producto en el sistema todavía.</p>
        <a href="subir_producto.php" class="btn btn-dark mt-3">EMPEZAR_LOG</a>
    </div>
<?php else: ?>
    <div class="table-responsive bg-white border border-2 border-dark shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="p-3">MED-IMG</th>
                    <th class="p-3">NAME_ID</th>
                    <th class="p-3">BRAND</th>
                    <th class="p-3">CAT</th>
                    <th class="p-3 text-end">PRICE</th>
                    <th class="p-3 text-center">STOCK</th>
                    <th class="p-3 text-center">EXEC</th>
                </tr>
            </thead>
            <tbody class="fw-bold small">
                <?php foreach ($mis_productos as $p): ?>
                    <tr>
                        <td class="p-3">
                            <?php if ($p['imagen']): ?>
                                <div class="bg-dark p-1 d-inline-block border border-dark shadow-sm">
                                    <img src="uploads/<?= $p['imagen'] ?>" width="50" height="50"
                                        style="object-fit: cover; opacity: 0.8;">
                                </div>
                            <?php else: ?>
                                <div class="bg-light border border-dark text-center"
                                    style="width: 50px; height: 50px; line-height: 50px; font-size: 8px;">[X]</div>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-uppercase"><?= htmlspecialchars($p['nombre']) ?></td>
                        <td class="p-3"><span
                                class="text-primary"><?= htmlspecialchars(strtoupper($p['marca'] ?? 'INDIE')) ?></span></td>
                        <td class="p-3"><span
                                class="badge bg-white text-dark border border-dark"><?= htmlspecialchars(strtoupper($p['categoria'] ?? 'N/A')) ?></span>
                        </td>
                        <td class="p-3 text-end"><?= number_format($p['precio'], 2) ?> €</td>
                        <td class="p-3 text-center"><?= $p['stock'] ?></td>
                        <td class="p-3 text-center">
                            <div class="btn-group">
                                <a href="editar_producto.php?id=<?= $p['id'] ?>"
                                    class="btn btn-warning btn-sm border-2">EDIT</a>
                                <a href="actions/delete_product.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm border-2"
                                    onclick="return confirm('CONFIRM_DELETE?')">DEL</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>