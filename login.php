<?php
session_start();
include 'conexion.php';
include 'seguridad.php';


if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 1. Detección de ataques previos al login
    if (detectar_sqli($username) || detectar_sqli($password)) {
        registrar_evento($conexion, 'SQL_INJECTION_ATTEMPT', "Intento de SQLi en login. User: $username - Pass: $password", 10);
        $error = 'Credenciales incorrectas'; // Mensaje genérico para no dar pistas
    } else {
        // 2. Consulta de usuario
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            // 3. Verificar si es una trampa (Honeytoken)
            if ($user['es_honeytoken']) {
                // ¡ALERTA MÁXIMA! Alguien intentó usar una cuenta trampa
                registrar_evento($conexion, 'HONEYTOKEN_TRIGGERED', "Intento de acceso a cuenta cebo: $username", 15);
                // Simulamos fallo de contraseña
                $error = 'Credenciales incorrectas';
            }
            // 4. Verificación Real
            elseif (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['rol_id'] = $user['rol_id'];

                // Log de acceso legítimo (Nivel bajo)
                registrar_evento($conexion, 'LOGIN_SUCCESS', "Usuario logueado: $username", 1);

                header('Location: index.php');
                exit();
            } else {
                // Contraseña incorrecta (Usuario real)
                registrar_evento($conexion, 'LOGIN_FAILED', "Fallo de password para usuario: $username", 2);
                $error = 'Credenciales incorrectas';
            }
        } else {
            // Usuario no existente (Brute force potential)
            registrar_evento($conexion, 'LOGIN_UNKNOWN_USER', "Intento con usuario desconocido: $username", 3);
            $error = 'Credenciales incorrectas';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login - Tienda Segura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow" style="width: 400px;">
        <h3 class="text-center mb-3">Iniciar Sesión</h3>
        <?php
        if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success">
                <?php
                echo $_SESSION['flash_success'];
                unset($_SESSION['flash_success']);
                ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Usuario</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <div class="mt-3 text-center">
            <a href="registro.php">¿No tienes cuenta? Regístrate</a>
        </div>
    </div>
</body>

</html>