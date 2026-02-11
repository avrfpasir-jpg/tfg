<?php
session_start();
include 'conexion.php';
include 'seguridad.php';

// Manejo de Filtros
$categoria_id = $_GET['cat'] ?? null;
$busqueda = $_GET['q'] ?? '';
$marca_filt = $_GET['marca'] ?? '';

// Consulta de Categorías sugeridas
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll();
$marcas = $conexion->query("SELECT DISTINCT marca FROM productos WHERE marca IS NOT NULL AND marca != ''")->fetchAll();

// Consulta de Productos
$sql = "SELECT p.*, c.nombre as categoria_nombre, u.username as vendedor 
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        LEFT JOIN usuarios u ON p.usuario_id = u.id
        WHERE 1=1";

$params = [];

if ($categoria_id) {
    $sql .= " AND p.categoria_id = :cat_id";
    $params[':cat_id'] = $categoria_id;
}

if ($busqueda) {
    $sql .= " AND (p.nombre LIKE :q OR p.descripcion LIKE :q)";
    $params[':q'] = "%$busqueda%";
}

if ($marca_filt) {
    $sql .= " AND p.marca = :marca";
    $params[':marca'] = $marca_filt;
}

$sql .= " ORDER BY p.id DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll();

$pageTitle = "RAW_CATALOG";
include 'includes/header.php';
?>

<!-- Hero Underground -->
<div class="hero-section p-5 mb-5 shadow-lg position-relative overflow-hidden"
    style="background-image: url('https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&q=60&w=1200'); background-size: cover; background-position: center; border: 10px solid black;">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-75"></div>
    <div class="row align-items-center py-4 position-relative z-1">
        <div class="col-lg-8">
            <h1 class="display-3 fw-black mb-3">SYSTEM_OVERLOAD<br><span
                    style="color: var(--acid-green);">#INDIE_MODE</span></h1>
            <p class="fs-5 mb-4 border-start border-4 border-warning ps-3">No somos una tienda. Somos el punto de
                encuentro de marcas que operan fuera del radar corporativo. Moda cruda, real y directa.</p>
            <div class="d-flex gap-3 mt-5">
                <a href="subir_producto.php" class="btn btn-primary btn-lg px-4">EMPEZAR_VEND</a>
                <a href="#feed" class="btn btn-outline-light btn-lg px-4">EXPLORAR_FEED</a>
            </div>
        </div>
    </div>
</div>

<div id="feed" class="row">
    <!-- Sidebar Brutalista -->
    <div class="col-lg-3 mb-4">
        <div class="p-4 bg-white border border-2 border-dark shadow-sm sticky-top" style="top: 110px;">
            <form method="GET" action="index.php">
                <h5 class="fw-bold mb-4 border-bottom border-2 border-dark pb-2 text-uppercase">
                    Filtros_Config
                </h5>

                <div class="mb-4">
                    <label class="small fw-bold mb-2">BUSCAR //</label>
                    <input type="text" name="q" class="form-control" placeholder="KEYWORD..."
                        value="<?= htmlspecialchars($busqueda) ?>">
                </div>

                <div class="mb-4">
                    <label class="small fw-bold mb-2">CATEGORIA //</label>
                    <select name="cat" class="form-select" onchange="this.form.submit()">
                        <option value="">ALL_TYPES</option>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $categoria_id == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars(strtoupper($c['nombre'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="small fw-bold mb-2">DROP_VEND //</label>
                    <select name="marca" class="form-select" onchange="this.form.submit()">
                        <option value="">ALL_BRANDS</option>
                        <?php foreach ($marcas as $m): ?>
                            <option value="<?= htmlspecialchars($m['marca']) ?>" <?= $marca_filt == $m['marca'] ? 'selected' : '' ?>><?= htmlspecialchars(strtoupper($m['marca'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <a href="index.php" class="btn btn-dark w-100 btn-sm">RESET_FILTER</a>
            </form>
        </div>
    </div>

    <!-- Resultados en Formato Poster -->
    <div class="col-lg-9">
        <?php if (empty($productos)): ?>
            <div class="text-center py-5 border border-4 border-dark bg-white shadow">
                <div class="display-1 mb-4">❌</div>
                <h3 class="fw-bold">DATA_NOT_FOUND</h3>
                <p class="text-muted">No existen drops con estos parámetros en el sistema.</p>
                <a href="index.php" class="btn btn-primary mt-3">RELOAD_CATALOG</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                <?php foreach ($productos as $prod): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="position-relative overflow-hidden bg-black" style="height: 350px;">
                                <?php if ($prod['imagen']): ?>
                                    <img src="uploads/<?= $prod['imagen'] ?>" class="card-img-top h-100 w-100"
                                        style="object-fit: cover; opacity: 0.9;" alt="<?= htmlspecialchars($prod['nombre']) ?>">
                                <?php else: ?>
                                    <div class="h-100 w-100 d-flex align-items-center justify-content-center text-white">
                                        <span class="small">[NO_IMAGE_DATA]</span>
                                    </div>
                                <?php endif; ?>
                                <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-black text-white bg-opacity-75">
                                    <span
                                        class="small fw-bold text-uppercase"><?= htmlspecialchars($prod['categoria_nombre']) ?></span>
                                </div>
                            </div>

                            <div class="card-body p-4 d-flex flex-column">
                                <div class="mb-3">
                                    <a href="perfil_vendedor.php?id=<?= $prod['usuario_id'] ?>" class="text-decoration-none">
                                        <span
                                            class="badge-brand">@<?= htmlspecialchars(strtoupper($prod['marca'] ?? 'INDIE')) ?></span>
                                    </a>
                                </div>
                                <h4 class="mb-2 fs-5"><?= htmlspecialchars(strtoupper($prod['nombre'])) ?></h4>
                                <p class="small text-muted mb-4"><?= htmlspecialchars($prod['descripcion']) ?></p>

                                <div
                                    class="mt-auto pt-3 border-top border-dark d-flex justify-content-between align-items-center">
                                    <span class="price-tag"><?= number_format($prod['precio'], 2) ?> €</span>
                                    <a href="actions/cart_add.php?id=<?= $prod['id'] ?>" class="btn btn-primary btn-sm">
                                        ADD_TO_BAG
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>