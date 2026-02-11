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
            --pitch-black: #1a1a1a;
            --pure-white: #ffffff;
            --gray-light: #f8f9fa;
            --gray-medium: #e9ecef;
            --accent-color: #000000;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gray-light);
            color: var(--pitch-black);
            line-height: 1.6;
        }

        .navbar {
            background-color: var(--pure-white);
            border-bottom: 2px solid var(--gray-medium);
            padding: 1.5rem 0;
        }

        .navbar-brand {
            color: var(--pitch-black) !important;
            font-weight: 900;
            letter-spacing: -1px;
            font-size: 1.5rem;
            text-transform: uppercase;
        }

        .nav-link {
            color: var(--pitch-black) !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            margin-left: 1rem;
        }

        .card {
            border: 1px solid var(--gray-medium);
            border-radius: 8px;
            overflow: hidden;
            background: var(--pure-white);
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
            border-color: var(--pitch-black);
        }

        .btn-primary {
            background-color: var(--pitch-black);
            border: none;
            border-radius: 6px;
            padding: 0.8rem 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: #333;
            transform: scale(1.02);
        }

        .price-tag {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--pitch-black);
        }

        .product-img-wrapper {
            background-color: var(--gray-medium);
            aspect-ratio: 1 / 1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
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