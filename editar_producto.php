<?php
session_start();
include 'conexion.php';
include 'seguridad.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['user_id'];
$es_admin = ($_SESSION['rol_id'] == 1);
$producto_id = $_GET['id'] ?? 0;

// Verificar que el producto existe
$stmt = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$producto_id]);
$producto = $stmt->fetch();

// Regla de Seguridad: Admin edita todo, Usuario solo lo suyo
if (!$producto || (!$es_admin && $producto['usuario_id'] != $usuario_id)) {
    header("Location: mis_productos.php");
    exit();
}

$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll();
$error = '';

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
                if ($imagen && file_exists("uploads/" . $imagen)) {
                    unlink("uploads/" . $imagen);
                }
                $imagen = $filename;
            }
        }
    }

    $stmt = $conexion->prepare("UPDATE productos SET nombre = ?, marca = ?, descripcion = ?, precio = ?, stock = ?, categoria_id = ?, imagen = ? WHERE id = ?");
    if ($stmt->execute([$nombre, $marca, $descripcion, $precio, $stock, $categoria_id, $imagen, $producto_id])) {
        // Si el producto no tenía dueño, el admin se lo queda (o se mantiene oficial)
        if ($es_admin && is_null($producto['usuario_id'])) {
            $conexion->prepare("UPDATE productos SET usuario_id = ? WHERE id = ?")->execute([$usuario_id, $producto_id]);
        }

        $_SESSION['flash_success'] = "SYSTEM_NODE_UPDATED: ID_" . $producto_id;
        header("Location: mis_productos.php");
        exit();
    } else {
        $error = "DB_WRITE_FAILURE";
    }
}

$pageTitle = "EDIT_MODE // ID_" . $producto_id;
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-0 overflow-hidden">
            <div class="card-header bg-black text-acid py-3 border-bottom border-4 border-dark">
                <h3 class="m-0 fw-black text-uppercase">// EDIT_NODE: <?= htmlspecialchars($producto['nombre']) ?></h3>
            </div>
            <div class="card-body p-5">
                <?php if ($error): ?>
                    <div class="bg-danger text-white p-3 mb-4 fw-bold shadow">[FAIL] <?= $error ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="small fw-bold mb-2 text-uppercase">NODE_NAME //</label>
                            <input type="text" name="nombre" class="form-control" required
                                value="<?= htmlspecialchars($producto['nombre']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold mb-2 text-uppercase">BRAND_TAG //</label>
                            <input type="text" name="marca" class="form-control"
                                value="<?= htmlspecialchars($producto['marca'] ?? '') ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="small fw-bold mb-2 text-uppercase">CATEGORY_ID //</label>
                            <select name="categoria_id" class="form-select" required>
                                <?php foreach ($categorias as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $c['id'] == $producto['categoria_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(strtoupper($c['nombre'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold mb-2 text-uppercase">DESCRIPTION_LOG //</label>
                        <textarea name="descripcion" class="form-control"
                            rows="4"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="small fw-bold mb-2 text-uppercase">PRICE_EUR //</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="precio" class="form-control" required
                                    value="<?= $producto['precio'] ?>">
                                <span class="input-group-text bg-black text-white">€</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold mb-2 text-uppercase">STOCK_VAL //</label>
                            <input type="number" name="stock" class="form-control" required min="0"
                                value="<?= $producto['stock'] ?>">
                        </div>
                    </div>

                    <div class="mb-5 p-4 bg-light border border-2 border-dark">
                        <label class="small fw-bold mb-3 d-block text-uppercase">MEDIA_ASSET //</label>
                        <div class="d-flex align-items-center gap-4">
                            <?php if ($producto['imagen']): ?>
                                <div class="bg-black p-1 border border-dark">
                                    <img src="uploads/<?= $producto['imagen'] ?>" width="100" height="100"
                                        style="object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            <div class="flex-grow-1">
                                <input type="file" name="imagen" class="form-control" accept="image/*">
                                <p class="small text-muted mt-2 mb-0">UPDATE_OR_KEEP_CURRENT</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="mis_productos.php" class="text-decoration-none fw-bold text-dark">[ CANCEL_RETURN ]</a>
                        <button type="submit" class="btn btn-primary px-5 py-3">SAVE_CHANGES_EXEC</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>