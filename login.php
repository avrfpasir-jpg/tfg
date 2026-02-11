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
        $error = 'Credenciales incorrectas';
    } else {
        // 2. Consulta de usuario
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            // 3. Verificar si es una trampa (Honeytoken)
            if ($user['es_honeytoken']) {
                registrar_evento($conexion, 'HONEYTOKEN_TRIGGERED', "Intento de acceso a cuenta cebo: $username", 15);
                $error = 'Credenciales incorrectas';
            }
            // 4. Verificación Real
            elseif (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['rol_id'] = $user['rol_id'];

                registrar_evento($conexion, 'LOGIN_SUCCESS', "Usuario logueado: $username", 1);

                header('Location: index.php');
                exit();
            } else {
                registrar_evento($conexion, 'LOGIN_FAILED', "Fallo de password para usuario: $username", 2);
                $error = 'Credenciales incorrectas';
            }
        } else {
            registrar_evento($conexion, 'LOGIN_UNKNOWN_USER', "Intento con usuario desconocido: $username", 3);
            $error = 'Credenciales incorrectas';
        }
    }
}

$pageTitle = "Login";
include 'includes/header.php';
?>

<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-4">
        <div class="card p-5">
            <h3 class="fw-black mb-4 text-uppercase">// ACCESS_GATES</h3>
            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['flash_success'];
                    unset($_SESSION['flash_success']);
                    ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-danger text-white p-3 mb-4 fw-bold">[FAIL] <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-4">
                    <label class="small fw-bold mb-2 text-uppercase">USR_ID //</label>
                    <input type="text" name="username" class="form-control" required placeholder="...">
                </div>
                <div class="mb-5">
                    <label class="small fw-bold mb-2 text-uppercase">PASS_CODE //</label>
                    <input type="password" name="password" class="form-control" required placeholder="...">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3">LOGIN_TO_SYSTEM</button>
            </form>
            <div class="mt-3 text-center">
                <a href="registro.php">¿No tienes cuenta? Regístrate</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>