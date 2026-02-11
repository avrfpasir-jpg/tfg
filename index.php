<?php
$pageTitle = "Catálogo";
include 'includes/header.php';

// Obtener todos los productos
$productos = $conexion->query("SELECT * FROM productos ORDER BY id DESC")->fetchAll();
?>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php foreach ($productos as $p): ?>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <a href="producto.php?id=<?= $p['id'] ?>" class="product-img-wrapper">
                    <?php if ($p['imagen']): ?>
                        <img src="uploads/<?= $p['imagen'] ?>" class="w-100 h-100" style="object-fit: cover;">
                    <?php else: ?>
                        <div class="text-muted d-flex flex-column align-items-center">
                            <span class="mb-2">📷</span>
                            <span class="small fw-bold">SIN IMAGEN</span>
                        </div>
                    <?php endif; ?>
                </a>
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-1"><?= htmlspecialchars($p['nombre']) ?></h5>
                    <p class="price-tag mb-3"><?= number_format($p['precio'], 2) ?> €</p>
                    <a href="actions/cart_add.php?id=<?= $p['id'] ?>" class="btn btn-primary w-100">Añadir al Carrito</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>