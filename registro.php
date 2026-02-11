<?php
session_start();
include 'conexion.php';
include 'seguridad.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';

    if (detectar_sqli($username) || detectar_sqli($email)) {
        registrar_evento($conexion, 'SQL_INJECTION_REGISTRO', "Intento SQLi registro: $username", 8);
        $error = "Error en el registro";
    } else {
        // Verificar duplicados
        $stmtCheck = $conexion->prepare("SELECT id FROM usuarios WHERE username = :u");
        $stmtCheck->execute([':u' => $username]);
        if ($stmtCheck->fetch()) {
            $error = "El usuario ya existe";
        } else {
            // Crear usuario
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO usuarios (username, password_hash, email, rol_id, es_honeytoken) VALUES (:u, :p, :e, 2, 0)"); // Rol 2 = Cliente
            if ($stmt->execute([':u' => $username, ':p' => $hash, ':e' => $email])) {
                $_SESSION['flash_success'] = "Usuario registrado. Ya puedes iniciar sesión.";
                registrar_evento($conexion, 'REGISTER_SUCCESS', "Nuevo usuario: $username", 1);
                header("Location: login.php");
                exit;
            } else {
                $error = "Error al guardar";
            }
        }
    }
}

$pageTitle = "Registro";
include 'includes/header.php';
?>

<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-4">
        <div class="card p-5">
            <h3 class="fw-black mb-4 text-uppercase">// NEW_NODE_REG</h3>
            <?php if ($error): ?>
                <div class="bg-danger text-white p-3 mb-4 fw-bold">[FAIL] <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="bg-success text-white p-3 mb-4 fw-bold">[OK] <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="small fw-bold mb-2 text-uppercase">USR_TAG //</label>
                    <input type="text" name="username" class="form-control" required placeholder="...">
                </div>
                <div class="mb-4">
                    <label class="small fw-bold mb-2 text-uppercase">EMAIL_ADDR //</label>
                    <input type="email" name="email" class="form-control" required placeholder="...">
                </div>
                <div class="mb-5">
                    <label class="small fw-bold mb-2 text-uppercase">PASS_CODE //</label>
                    <input type="password" name="password" class="form-control" required placeholder="...">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3">EXEC_REGISTER</button>
            </form>
            <div class="mt-3 text-center">
                <a href="login.php">Volver al Login</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>