<?php
include 'includes/header.php';

$id = $_GET['id'] ?? 0;
$stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    header("Location: index.php");
    exit();
}

$pageTitle = $p['nombre'];
?>

<div class="row mt-5">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm p-3">
            <div class="product-img-wrapper" style="height: 500px; border-radius: 4px;">
                <?php if ($p['imagen']): ?>
                    <img src="uploads/<?= $p['imagen'] ?>" class="img-fluid w-100 h-100" style="object-fit: contain;">
                <?php else: ?>
                    <div class="text-muted d-flex flex-column align-items-center">
                        <span style="font-size: 3rem;">📷</span>
                        <span class="fw-bold">IMAGEN NO DISPONIBLE</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6 ps-md-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-dark">Inicio</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($p['nombre']) ?></li>
            </ol>
        </nav>
        <h1 class="display-4 fw-black mb-2 text-uppercase"><?= htmlspecialchars($p['nombre']) ?></h1>
        <p class="display-6 price-tag mb-4"><?= number_format($p['precio'], 2) ?> €</p>

        <div class="mb-5 pb-4 border-bottom">
            <h6 class="text-uppercase fw-bold text-muted mb-3">Descripción</h6>
            <p class="lead" style="color: #444;"><?= nl2br(htmlspecialchars($p['descripcion'])) ?></p>
        </div>

        <div class="d-grid">
            <a href="actions/cart_add.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-lg px-5 py-3 shadow-sm">
                Añadir al Carrito
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>