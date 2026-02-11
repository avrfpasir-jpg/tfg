<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Tienda Segura' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --pitch-black: #000000;
            --pure-white: #ffffff;
            --gray-light: #f4f4f4;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gray-light);
            color: var(--pitch-black);
        }

        .navbar {
            background-color: var(--pitch-black);
            border-bottom: 4px solid var(--pitch-black);
        }

        .navbar-brand,
        .nav-link {
            color: var(--pure-white) !important;
            font-weight: 900;
            text-transform: uppercase;
        }

        .nav-link:hover {
            text-decoration: underline;
        }

        .card {
            border: 3px solid var(--pitch-black);
            border-radius: 0;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translate(-5px, -5px);
            box-shadow: 8px 8px 0px var(--pitch-black);
        }

        .btn-primary {
            background-color: var(--pitch-black);
            border: 3px solid var(--pitch-black);
            border-radius: 0;
            font-weight: 700;
            text-transform: uppercase;
        }

        .btn-primary:hover {
            background-color: var(--pure-white);
            color: var(--pitch-black);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg mb-5">
        <div class="container text-center">
            <a class="navbar-brand mx-auto" href="index.php">Tienda Segura</a>
            <div class="d-flex gap-3">
                <a href="index.php" class="nav-link">Catálogo</a>
                <a href="carrito.php" class="nav-link">Carrito</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="nav-link text-danger">Salir</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Entrar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container pb-5">