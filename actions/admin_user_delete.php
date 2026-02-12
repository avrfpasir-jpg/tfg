<?php
session_start();
include '../conexion.php';
include '../includes/seguridad.php';

// Verificar permisos de admin
if (!isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'] ?? null;

if ($id) {
    // 1. No permitir que el admin se borre a sí mismo
    if ($id == $_SESSION['user_id']) {
        $_SESSION['mensaje'] = "Error: No puedes eliminar tu propia cuenta.";
        $_SESSION['mensaje_tipo'] = "danger";
        header("Location: ../admin_usuarios.php");
        exit();
    }

    try {
        // 2. Obtener info del usuario para el log
        $stmt = $conexion->prepare("SELECT username FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if ($user) {
            // 3. Eliminar usuario
            $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);

            registrar_evento($conexion, 'USUARIO_ELIMINADO', "Admin (" . $_SESSION['username'] . ") eliminó al usuario: " . $user['username'], 4);

            $_SESSION['mensaje'] = "Usuario '" . htmlspecialchars($user['username']) . "' eliminado correctamente. Sus pedidos se han mantenido como anónimos.";
            $_SESSION['mensaje_tipo'] = "success";
        } else {
            $_SESSION['mensaje'] = "Error: El usuario no existe.";
            $_SESSION['mensaje_tipo'] = "danger";
        }
    } catch (Exception $e) {
        $_SESSION['mensaje'] = "Error técnico al eliminar el usuario: " . $e->getMessage();
        $_SESSION['mensaje_tipo'] = "danger";
    }
}

header("Location: ../admin_usuarios.php");
exit();
?>