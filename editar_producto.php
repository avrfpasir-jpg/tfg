<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['user_id'];
$producto_id = $_GET['id'] ?? 0;

// Verificar que el producto pertenece al usuario
$stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ? AND usuario_id = ?");
$stmt->execute([$producto_id, $usuario_id]);
$producto = $stmt->fetch();

if (!$producto) {
    header("Location: mis_productos.php");
    exit();
}

$categorias = $conexion->query("SELECT * FROM categorias")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $marca = $_POST['marca'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria_id = $_POST['categoria_id'];
    $imagen = $producto['imagen'];

    // Manejo de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array(strtolower($ext), $allowed)) {
            $filename = uniqid() . "." . $ext;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], "uploads/" . $filename)) {
                // Borrar imagen antigua si existe
                if ($imagen && file_exists("uploads/" . $imagen)) {
                    unlink("uploads/" . $imagen);
                }
                $imagen = $filename;
            }
        }
    }

    $stmt = $conexion->prepare("UPDATE productos SET nombre = ?, marca = ?, descripcion = ?, precio = ?, stock = ?, categoria_id = ?, imagen = ? WHERE id = ? AND usuario_id = ?");
    if ($stmt->execute([$nombre, $marca, $descripcion, $precio, $stock, $categoria_id, $imagen, $producto_id, $usuario_id])) {
        $_SESSION['flash_success'] = "Producto actualizado con éxito.";
        header("Location: mis_productos.php");
        exit();
    }
}

$pageTitle = "Editar Producto";
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h3 class="card-title mb-0">Editar Producto</h3>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($producto['nombre']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marca / Creador</label>
                            <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($producto['marca'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría</label>
                            <select name="categoria_id" class="form-select" required>
                                <?php foreach ($categorias as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $c['id'] == $producto['categoria_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control"
                            rows="3"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Precio (€)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="precio" class="form-control" required
                                    value="<?= $producto['precio'] ?>">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" required min="0"
                                value="<?= $producto['stock'] ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Imagen del Producto (Dejar vacío para mantener la actual)</label>
                        <?php if ($producto['imagen']): ?>
                            <div class="mb-2">
                                <img src="uploads/<?= $producto['imagen'] ?>" width="100" class="img-thumbnail">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="mis_productos.php" class="btn btn-outline-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-warning px-5">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>