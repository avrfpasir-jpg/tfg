<?php
session_start();
include 'conexion.php';
include 'seguridad.php';

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
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro - Tienda Segura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow" style="width: 400px;">
        <h3 class="text-center mb-3">Registro</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Usuario</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Registrarse</button>
        </form>
        <div class="mt-3 text-center">
            <a href="login.php">Volver al Login</a>
        </div>
    </div>
</body>

</html>