<?php
include '../includes/admin_auth.php';
include '../conexion.php';

$stmt = $conexion->query("SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id");
$productos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Admin - Tienda Segura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Panel de Administración</h1>
            <div>
                <a href="../index.php" class="btn btn-secondary">Ver Tienda</a>
                <a href="../logout.php" class="btn btn-danger">Salir</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Productos</h5>
                <a href="agregar_producto.php" class="btn btn-success btn-sm">Nuevo Producto</a>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p): ?>
                            <tr>
                                <td>
                                    <?= $p['id'] ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($p['nombre']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($p['categoria_nombre']) ?>
                                </td>
                                <td>
                                    <?= $p['precio'] ?> €
                                </td>
                                <td>
                                    <?= $p['stock'] ?>
                                </td>
                                <td>
                                    <a href="editar_producto.php?id=<?= $p['id'] ?>"
                                        class="btn btn-primary btn-sm">Editar</a>
                                    <a href="eliminar_producto.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Seguro?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>