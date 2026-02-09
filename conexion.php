<?php
$host = 'localhost';
$usuario = 'root';
$password = '';
$bd = 'tienda_segura';

try {
    $conexion = new PDO("mysql:host=$host;dbname=$bd;charset=utf8", $usuario, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En producción no mostraríamos el error real, pero en dev sí
    die("Error crítico de conexión: " . $e->getMessage());
}
?>