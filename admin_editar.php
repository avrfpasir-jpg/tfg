<?php
include 'includes/header.php';

if (!isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'] ?? null;
$p = null;

if ($id) {
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen = $p ? $p['imagen'] : '';

    // Gestión de imagen
    if (!empty($_FILES['imagen']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir))
            mkdir($target_dir, 0777, true);

        $file_ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $new_name = uniqid() . "." . $file_ext;
        $target_file = $target_dir . $new_name;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
            $imagen = $new_name;
        }
    }

    if ($id) {
        $stmt = $conexion->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen = ? WHERE id = ?");
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $imagen, $id]);
        $_SESSION['mensaje'] = "Producto actualizado correctamente.";
    } else {
        $stmt = $conexion->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $imagen]);
        $_SESSION['mensaje'] = "Nuevo producto añadido.";
    }

    $_SESSION['mensaje_tipo'] = "success";
    header("Location: admin_productos.php");
    exit();
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm p-4">
            <h2 class="fw-black mb-4">
                <?= $id ? 'EDITAR PRODUCTO' : 'NUEVO PRODUCTO' ?>
            </h2>

            <form method="POST" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control" value="<?= $p['nombre'] ?? '' ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="5"
                                required><?= $p['descripcion'] ?? '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase">Precio (€)</label>
                            <input type="number" step="0.01" name="precio" class="form-control"
                                value="<?= $p['precio'] ?? '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase">Stock Disponible</label>
                            <input type="number" name="stock" class="form-control" value="<?= $p['stock'] ?? '' ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase">Imagen del Producto</label>
                            <input type="file" name="imagen" class="form-control mb-2">
                            <?php if ($p && $p['imagen']): ?>
                                <img src="uploads/<?= $p['imagen'] ?>" class="img-thumbnail w-100">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-top mt-4 d-flex gap-3">
                    <button type="submit" class="btn btn-primary px-5">GUARDAR PRODUCTO</button>
                    <a href="admin_productos.php" class="btn btn-outline-dark">CANCELAR</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>