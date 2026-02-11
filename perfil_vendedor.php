<?php
session_start();
include 'conexion.php';

$vendedor_id = $_GET['id'] ?? null;

if (!$vendedor_id) {
    header("Location: index.php");
    exit();
}

$stmt_u = $conexion->prepare("SELECT username, bio, ciudad, imagen_perfil FROM usuarios WHERE id = ?");
$stmt_u->execute([$vendedor_id]);
$vendedor = $stmt_u->fetch();

if (!$vendedor) {
    header("Location: index.php");
    exit();
}

$stmt_p = $conexion->prepare("SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.usuario_id = ?");
$stmt_p->execute([$vendedor_id]);
$productos = $stmt_p->fetchAll();

$pageTitle = "PROFILE//" . strtoupper($vendedor['username']);
include 'includes/header.php';
?>

<div class="row mt-4">
    <!-- Perfil Vendedor Raw -->
    <div class="col-lg-12 mb-5">
        <div class="bg-black text-white p-5 border border-4 border-dark shadow-lg position-relative"
            style="outline: 2px solid var(--acid-green); margin-top: 20px;">
            <div class="position-absolute top-0 end-0 p-4 opacity-25 d-none d-md-block">
                <span class="display-1 fw-black">BRAND_ID</span>
            </div>
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="bg-warning text-black d-flex align-items-center justify-content-center"
                        style="width: 120px; height: 120px; border: 4px solid white; font-size: 3rem; font-weight: 800; transform: rotate(-3deg);">
                        <?= strtoupper(substr($vendedor['username'], 0, 1)) ?>
                    </div>
                </div>
                <div class="col">
                    <h1 class="display-4 fw-black mb-1 text-uppercase"><?= htmlspecialchars($vendedor['username']) ?>
                    </h1>
                    <p class="text-warning small d-flex align-items-center gap-2 mb-0 fw-bold">
                        LOC: [ <?= htmlspecialchars(strtoupper($vendedor['ciudad'] ?? 'UNKNOWN')) ?> ]
                    </p>
                </div>
                <div class="col-auto text-end">
                    <div class="bg-white text-black p-3 border border-dark d-inline-block"
                        style="box-shadow: 6px 6px 0px var(--acid-green);">
                        <span class="d-block h2 fw-black mb-0"><?= count($productos) ?></span>
                        <span class="small fw-bold">LOGS_ACTIVOS</span>
                    </div>
                </div>
            </div>
            <div class="mt-5 pt-4 border-top border-secondary">
                <h5 class="fw-bold mb-3 text-warning">// STATEMENT_MANIFESTO</h5>
                <p class="fs-5 opacity-75" style="font-family: 'Space Mono', monospace;">
                    <?= !empty($vendedor['bio']) ? nl2br(htmlspecialchars($vendedor['bio'])) : 'Este creador todavía no ha definido su línea de diseño.' ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Feed del Vendedor -->
    <div class="col-lg-12">
        <h4 class="fw-bold mb-4 text-uppercase border-bottom border-4 border-dark d-inline-block pb-2">
            🔥 DROP_HISTORY
        </h4>
        <div class="row row-cols-1 row-cols-md-3 row-cols-xl-4 g-4 mt-2">
            <?php foreach ($productos as $prod): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="position-relative overflow-hidden bg-black" style="height: 300px;">
                            <?php if ($prod['imagen']): ?>
                                <img src="uploads/<?= $prod['imagen'] ?>" class="card-img-top h-100 w-100"
                                    style="object-fit: cover;" alt="<?= htmlspecialchars($prod['nombre']) ?>">
                            <?php endif; ?>
                            <div class="position-absolute top-0 end-0 p-3">
                                <span
                                    class="bg-white text-black px-2 py-1 small fw-bold border border-dark"><?= htmlspecialchars(strtoupper($prod['categoria_nombre'])) ?></span>
                            </div>
                        </div>
                        <div class="card-body p-4 bg-white">
                            <h5 class="fw-bold mb-2 text-uppercase"><?= htmlspecialchars($prod['nombre']) ?></h5>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <span class="price-tag"><?= number_format($prod['precio'], 2) ?> €</span>
                                <a href="actions/cart_add.php?id=<?= $prod['id'] ?>"
                                    class="btn btn-primary btn-sm">GET_IT</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>