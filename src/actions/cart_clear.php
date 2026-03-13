<?php
session_start();
if (isset($_GET['id'])) {
    unset($_SESSION['cart'][$_GET['id']]);
} else {
    unset($_SESSION['cart']);
}
$_SESSION['mensaje'] = "Carrito actualizado.";
$_SESSION['mensaje_tipo'] = "info";
header("Location: ../carrito.php");
exit();
?>