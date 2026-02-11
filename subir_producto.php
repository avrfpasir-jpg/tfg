<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    $usuario_id = $_SESSION['user_id'];
    $imagen = null;

    // Manejo de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array(strtolower($ext), $allowed)) {
            $filename = uniqid() . "." . $ext;
            if (!is_dir("uploads")) {
                mkdir("uploads", 0777, true);
            }
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], "uploads/" . $filename)) {
                $imagen = $filename;
            }
        }
    }

    $stmt = $conexion->prepare("INSERT INTO productos (nombre, marca, descripcion, precio, stock, categoria_id, imagen, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$nombre, $marca, $descripcion, $precio, $stock, $categoria_id, $imagen, $usuario_id])) {
        $_SESSION['flash_success'] = "Producto subido con éxito.";
        header("Location: mis_productos.php");
        exit();
    }
}

$pageTitle = "Subir Producto";
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">Subir Nuevo Producto</h3>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control" required
                                placeholder="Ej: Sudadera Oversized">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marca / Creador</label>
                            <input type="text" name="marca" class="form-control" placeholder="Ej: Brand Independiente">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría</label>
                            <select name="categoria_id" class="form-select" required>
                                <option value="">Selecciona una categoría</option>
                                <?php foreach ($categorias as $c): ?>
                                    <option value="<?= $c['id'] ?>">
                                        <?= htmlspecialchars($c['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"
                            placeholder="Describe brevemente tu producto..."></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Precio (€)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="precio" class="form-control" required>
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stock inicial</label>
                            <input type="number" name="stock" class="form-control" required min="1">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Imagen del Producto</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                        <div class="form-text">Formatos permitidos: JPG, PNG, WEBP.</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="mis_productos.php" class="btn btn-outline-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5">Publicar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>