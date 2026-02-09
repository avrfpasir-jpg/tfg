<?php
session_start();
include 'conexion.php';
include 'seguridad.php';

// Seguridad de Sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 1. Detección en Búsqueda (SIEM Trigger)
$busqueda = '';
if (isset($_GET['q'])) {
    $busqueda = $_GET['q'];
    if (detectar_sqli($busqueda)) {
        registrar_evento($conexion, 'SQL_INJECTION_SEARCH', "Intento SQLi en buscador: $busqueda", 8);
        // No bloqueamos, dejamos que busque "basura" o nada, pero ya está logueado
    }
}

// Consulta de Productos
$sql = "SELECT p.*, c.nombre as categoria 
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE 1=1";

$params = [];
if ($busqueda) {
    $sql .= " AND p.nombre LIKE :q";
    $params[':q'] = "%$busqueda%";
}

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tienda Segura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Tienda Segura (TFG)</a>
            <div class="d-flex align-items-center">
                <a href="carrito.php" class="btn btn-outline-light btn-sm me-3 position-relative">
                    🛒 Carrito
                    <?php 
                    $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                    if ($cart_count > 0): 
                    ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $cart_count ?>
                        </span>
                    <?php endif; ?>
                </a>
                <span class="navbar-text me-3 text-light">Hola,
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Salir</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Buscador (Honeypot Vector) -->
        <div class="row mb-4">
            <div class="col-md-6">
                <form method="GET" class="d-flex">
                    <input class="form-control me-2" type="search" name="q" placeholder="Buscar productos..."
                        value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button class="btn btn-outline-success" type="submit">Buscar</button>
                </form>
            </div>
        </div>

        <!-- Feedback de Seguridad (Solo para demo, en real esto sería oculto) -->
        <!-- <div class="alert alert-info">Monitorización activa: Cualquier intento de ataque será registrado.</div> -->

        <div class="row">
            <?php foreach ($productos as $prod): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <?php if ($prod['imagen']): ?>
                            <img src="uploads/<?= $prod['imagen'] ?>" class="card-img-top"
                                alt="<?= htmlspecialchars($prod['nombre']) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                style="height: 200px;">
                                <span>Sin imagen</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($prod['nombre']); ?>
                            </h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                <?php echo htmlspecialchars($prod['categoria']); ?>
                            </h6>
                            <p class="card-text">
                                <?php echo htmlspecialchars($prod['descripcion']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0 text-primary">
                                    <?php echo number_format($prod['precio'], 2); ?> €
                                </span>
                                <a href="actions/cart_add.php?id=<?= $prod['id'] ?>" class="btn btn-primary btn-sm">🛒
                                    Añadir</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>

</html>