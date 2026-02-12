<?php
session_start();
include '../conexion.php';
include '../includes/seguridad.php';

// 1. Seguridad: Solo administradores
if (!isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // 2. Verificar si el producto tiene ventas (está en pedido_detalles)
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM pedido_detalles WHERE producto_id = ?");
        $stmt->execute([$id]);
        $ventas = $stmt->fetchColumn();

        if ($ventas > 0) {
            // No podemos borrarlo físicamente
            $_SESSION['mensaje'] = "No se puede eliminar un producto que ya ha sido vendido. Debes mantenerlo para la integridad de los pedidos antiguos.";
            $_SESSION['mensaje_tipo'] = "warning";
        } else {
            // Podemos borrarlo
            // Obtener info para borrar la imagen y registrar el log
            $stmt = $conexion->prepare("SELECT nombre, imagen FROM productos WHERE id = ?");
            $stmt->execute([$id]);
            $p = $stmt->fetch();

            if ($p) {
                // Borrar archivo de imagen si existe
                if ($p['imagen']) {
                    $img_path = "../uploads/" . $p['imagen'];
                    if (file_exists($img_path)) {
                        unlink($img_path);
                    }
                }

                // Borrar de la base de datos
                $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
                $stmt->execute([$id]);

                registrar_evento($conexion, 'PRODUCTO_ELIMINADO', "Admin (" . $_SESSION['username'] . ") eliminó el producto: " . $p['nombre'], 3);

                $_SESSION['mensaje'] = "Producto '" . htmlspecialchars($p['nombre']) . "' eliminado correctamente.";
                $_SESSION['mensaje_tipo'] = "success";
            } else {
                $_SESSION['mensaje'] = "Error: El producto no existe.";
                $_SESSION['mensaje_tipo'] = "danger";
            }
        }
    } catch (Exception $e) {
        $_SESSION['mensaje'] = "Error técnico: " . $e->getMessage();
        $_SESSION['mensaje_tipo'] = "danger";
    }
}

header("Location: ../admin_productos.php");
exit();
?>