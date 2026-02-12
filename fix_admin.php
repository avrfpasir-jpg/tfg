<?php
include 'conexion.php';
$h = password_hash('admin', PASSWORD_DEFAULT);
$stmt = $conexion->prepare("UPDATE usuarios SET password_hash = ?, es_admin = 1 WHERE username = 'admin'");
if ($stmt->execute([$h])) {
    echo "READY: Admin user updated successfully.\n";
} else {
    echo "ERROR: Failed to update admin user.\n";
}
?>