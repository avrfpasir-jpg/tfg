<?php
$pageTitle = "Registro";
include 'includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $conexion->prepare("INSERT INTO usuarios (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        header("Location: login.php");
        exit();
    } catch (Exception $e) {
        $error = "El nombre de usuario o email ya existe.";
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-4">
        <div class="card p-4">
            <h2 class="fw-black mb-4">REGISTRO</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Usuario</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3">CREAR CUENTA</button>
                <div class="text-center mt-3">
                    <a href="login.php" class="text-dark small">Ya tengo cuenta</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/header.php'; ?>