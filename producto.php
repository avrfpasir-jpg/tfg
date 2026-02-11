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
    <div class="col-md-6">
        <div class="border border-3 border-dark p-2 bg-white">
            <?php if ($p['imagen']): ?>
                <img src="uploads/<?= $p['imagen'] ?>" class="img-fluid w-100">
            <?php else: ?>
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                    style="height: 400px;">
                    <span>Sin Imagen</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <h1 class="display-4 fw-black"><?= htmlspecialchars($p['nombre']) ?></h1>
        <h3 class="text-muted mb-4"><?= number_format($p['precio'], 2) ?> €</h3>
        <p class="lead mb-5"><?= nl2br(htmlspecialchars($p['descripcion'])) ?></p>

        <a href="actions/cart_add.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-lg px-5 py-3">
            COMPRAR AHORA
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>