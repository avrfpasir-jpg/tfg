<?php
include_once __DIR__ . '/../includes/conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    header("Location: ../index.php");
    exit();
}

include '../includes/header.php';

$productos = $conexion->query("SELECT * FROM productos ORDER BY id DESC")->fetchAll();
?>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['mensaje_tipo'] ?? 'info'?> alert-dismissible fade show mb-4" role="alert">
        <?= $_SESSION['mensaje']?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
    unset($_SESSION['mensaje']);
    unset($_SESSION['mensaje_tipo']);
?>
<?php
endif; ?>

<div class="d-flex justify-content-between align-items-center mb-5">
    <h2 class="fw-black m-0">GESTIÓN DE PRODUCTOS</h2>
    <a href="producto_editar.php" class="btn btn-primary">+ NUEVO PRODUCTO</a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr class="small fw-bold text-uppercase">
                <th class="px-4 py-3">ID</th>
                <th class="py-3">Producto</th>
                <th class="py-3">Precio</th>
                <th class="py-3">Stock</th>
                <th class="text-end px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $p): ?>
                <tr>
                    <td class="px-4 text-muted small">#
                        <?= $p['id']?>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="rounded bg-light me-3" style="width: 40px; height: 40px; overflow: hidden;">
                                <?php if ($p['imagen']): ?>
                                    <img src="../uploads/<?= $p['imagen']?>" class="w-100 h-100" style="object-fit: cover;">
                                <?php
    else: ?>
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted small">
                                        📷</div>
                                <?php
    endif; ?>
                            </div>
                            <span class="fw-bold">
                                <?= htmlspecialchars($p['nombre'])?>
                            </span>
                        </div>
                    </td>
                    <td class="fw-bold">
                        <?= number_format($p['precio'], 2)?> €
                    </td>
                    <td>
                        <span class="badge <?= $p['stock'] > 0 ? 'bg-success' : 'bg-danger'?> px-3">
                            <?= $p['stock']?> uds
                        </span>
                    </td>
                    <td class="text-end px-4">
                        <div class="btn-group">
                            <a href="producto_editar.php?id=<?= $p['id']?>"
                                class="btn btn-sm btn-outline-dark fw-bold">EDITAR</a>
                            <a href="../actions/admin_product_delete.php?id=<?= $p['id']?>"
                                class="btn btn-sm btn-outline-danger fw-bold"
                                onclick="return confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer si el producto no tiene ventas.')">ELIMINAR</a>
                        </div>
                    </td>
                </tr>
            <?php
endforeach; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <a href="../index.php" class="text-muted small text-decoration-none">← Volver a la tienda</a>
</div>

<?php include '../includes/footer.php'; ?>