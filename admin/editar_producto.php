<?php
include '../includes/admin_auth.php';
include '../conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

$categorias = $conexion->query("SELECT * FROM categorias")->fetchAll();
$stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    die("Producto no encontrado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria_id = $_POST['categoria_id'];
    $imagen = $producto['imagen']; // Por defecto mantener la actual

    // Manejo de nueva imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array(strtolower($ext), $allowed)) {
            $filename = uniqid() . "." . $ext;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], "../uploads/" . $filename)) {
                // Opcional: Borrar imagen anterior si existe
                if ($producto['imagen'] && file_exists("../uploads/" . $producto['imagen'])) {
                    @unlink("../uploads/" . $producto['imagen']);
                }
                $imagen = $filename;
            }
        }
    }

    $stmt = $conexion->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, categoria_id=?, imagen=? WHERE id=?");
    if ($stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria_id, $imagen, $id])) {
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Editar Producto - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card">
            <div class="card-header">
                <h3>Editar Producto</h3>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control"
                            value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="descripcion"
                            class="form-control"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Precio</label>
                        <input type="number" step="0.01" name="precio" class="form-control"
                            value="<?= $producto['precio'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Stock</label>
                        <input type="number" name="stock" class="form-control" value="<?= $producto['stock'] ?>"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>Categoría</label>
                        <select name="categoria_id" class="form-select">
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $c['id'] == $producto['categoria_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Imagen Actual</label><br>
                        <?php if ($producto['imagen']): ?>
                            <img src="../uploads/<?= $producto['imagen'] ?>" width="100" class="mb-2 rounded shadow-sm">
                        <?php else: ?>
                            <span class="text-muted">Sin imagen</span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label>Cambiar Imagen</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>