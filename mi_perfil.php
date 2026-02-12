<?php
include 'includes/header.php';
include 'includes/seguridad.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        if (!empty($password)) {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("UPDATE usuarios SET email = ?, password_hash = ? WHERE id = ?");
            $stmt->execute([$email, $pass_hash, $user_id]);
            registrar_evento($conexion, 'PERFIL_ACTUALIZADO', "Usuario: " . $user['username'] . " (Cambio email + pass)", 2);
        } else {
            $stmt = $conexion->prepare("UPDATE usuarios SET email = ? WHERE id = ?");
            $stmt->execute([$email, $user_id]);
            registrar_evento($conexion, 'PERFIL_ACTUALIZADO', "Usuario: " . $user['username'] . " (Solo email)", 1);
        }
        $success = "Perfil actualizado correctamente.";
        // Refresh local user data
        $user['email'] = $email;
    } catch (Exception $e) {
        $error = "Ese correo ya está en uso.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm p-4">
            <h2 class="fw-black mb-4">MI PERFIL</h2>

            <?php if ($success): ?>
                <div class="alert alert-success small px-3 py-2">
                    <?= $success ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger small px-3 py-2">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase">Nombre de Usuario</label>
                    <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['username']) ?>"
                        readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-uppercase">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase">Nueva Contraseña</label>
                    <input type="password" name="password" class="form-control"
                        placeholder="Dejar en blanco para no cambiar">
                    <div class="form-text small">Mínimo 8 caracteres recomendado.</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3">GUARDAR CAMBIOS</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>