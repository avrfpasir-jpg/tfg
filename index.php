<?php
$pageTitle = "Catálogo";
include 'includes/header.php';

// Obtener todos los productos
$productos = $conexion->query("SELECT * FROM productos ORDER BY id DESC")->fetchAll();
?>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php foreach ($productos as $p): ?>
        <div class="col">
            <div class="card h-100">
                <a href="producto.php?id=<?= $p['id'] ?>">
                    <?php if ($p['imagen']): ?>
                        <img src="uploads/<?= $p['imagen'] ?>" class="card-img-top" style="height: 250px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                            style="height: 250px;">
                            <span>Sin Imagen</span>
                        </div>
                    <?php endif; ?>
                </a>
                <div class="card-body">
                    <h5 class="card-title fw-bold"><?= htmlspecialchars($p['nombre']) ?></h5>
                    <p class="card-text text-muted"><?= number_format($p['precio'], 2) ?> €</p>
                    <a href="actions/cart_add.php?id=<?= $p['id'] ?>" class="btn btn-primary w-100">Añadir</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>