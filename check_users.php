<?php
include 'conexion.php';
$stmt = $conexion->query("SELECT id, username, es_admin FROM usuarios");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
?>