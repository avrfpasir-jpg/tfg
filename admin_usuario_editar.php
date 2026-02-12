<?php
include 'includes/header.php';

if (!isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin_usuarios.php");
    exit();
}

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$u = $stmt->fetch();

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $es_admin = isset($_POST['es_admin']) ? 1 : 0;
    $email = trim($_POST['email']);

    $stmt = $conexion->prepare("UPDATE usuarios SET es_admin = ?, email = ? WHERE id = ?");
    $stmt->execute([$es_admin, $email, $id]);

    $_SESSION['mensaje'] = "Usuario actualizado correctamente.";
    header("Location: admin_usuarios.php");
    exit();
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm p-4">
            <h2 class="fw-black mb-4">EDITAR USUARIO</h2>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase">Nombre de Usuario</label>
                    <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($u['username']) ?>"
                        readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($u['email']) ?>">
                </div>
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="es_admin" id="adminCheck"
                            <?= $u['es_admin'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold small text-uppercase" for="adminCheck">Permisos de
                            Administrador</label>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary px-4">GUARDAR</button>
                    <a href="admin_usuarios.php" class="btn btn-outline-dark">CANCELAR</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>