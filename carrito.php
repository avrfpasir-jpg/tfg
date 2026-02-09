<?php
session_start();
include 'conexion.php';

$productos_carrito = [];
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';

    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $productos_db = $stmt->fetchAll();

    foreach ($productos_db as $p) {
        $cantidad = $_SESSION['cart'][$p['id']];
        $subtotal = $p['precio'] * $cantidad;
        $total += $subtotal;

        $productos_carrito[] = [
            'id' => $p['id'],
            'nombre' => $p['nombre'],
            'precio' => $p['precio'],
            'cantidad' => $cantidad,
            'subtotal' => $subtotal,
            'imagen' => $p['imagen']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tu Carrito - Tienda Segura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">← Volver a la Tienda</a>
        </div>
    </nav>

    <div class="container">
        <h2>🛒 Tu Carrito</h2>

        <?php if (empty($productos_carrito)): ?>
            <div class="alert alert-warning">Tu carrito está vacío.</div>
            <a href="index.php" class="btn btn-primary">Ir a comprar</a>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos_carrito as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($item['imagen']): ?>
                                                <img src="uploads/<?= $item['imagen'] ?>" width="50" class="me-3 rounded">
                                            <?php endif; ?>
                                            <?= htmlspecialchars($item['nombre']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?= number_format($item['precio'], 2) ?> €
                                    </td>
                                    <td>
                                        <?= $item['cantidad'] ?>
                                    </td>
                                    <td>
                                        <?= number_format($item['subtotal'], 2) ?> €
                                    </td>
                                    <td>
                                        <a href="actions/cart_remove.php?id=<?= $item['id'] ?>"
                                            class="btn btn-outline-danger btn-sm">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end h4">Total:</th>
                                <th colspan="2" class="h4 text-primary">
                                    <?= number_format($total, 2) ?> €
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php" class="btn btn-secondary">Seguir Comprando</a>
                        <a href="finalizar_pedido.php" class="btn btn-success px-4">Finalizar Pedido</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>