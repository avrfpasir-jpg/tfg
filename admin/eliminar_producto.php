<?php
include '../includes/admin_auth.php';
include '../conexion.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit();
?>