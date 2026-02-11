<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($pageTitle) ? $pageTitle . " - IndieBrand Market" : "IndieBrand Market - Moda Independiente"; ?>
    </title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Unbounded:wght@200..900&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --acid-green: #d1fa1e;
            --pitch-black: #000000;
            --raw-white: #ffffff;
            --brutal-border: 2px solid #000;
            --brutal-shadow: 4px 4px 0px #000;
            --brutal-shadow-hover: 8px 8px 0px #000;
        }

        body {
            font-family: 'Space Mono', monospace;
            background-color: #f0f0f0;
            color: var(--pitch-black);
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .navbar-brand {
            font-family: 'Unbounded', sans-serif;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: -1px;
        }

        .navbar {
            background-color: var(--pitch-black) !important;
            border-bottom: 4px solid var(--acid-green);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            color: var(--acid-green) !important;
        }

        .nav-link {
            font-weight: 700;
            color: var(--raw-white) !important;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .nav-link:hover {
            color: var(--acid-green) !important;
        }

        /* Neubrutalism Elements */
        .card {
            background: var(--raw-white);
            border: var(--brutal-border);
            border-radius: 0;
            box-shadow: var(--brutal-shadow);
            transition: all 0.2s ease;
        }

        .card:hover {
            transform: translate(-4px, -4px);
            box-shadow: var(--brutal-shadow-hover);
        }

        .btn {
            border-radius: 0;
            border: var(--brutal-border);
            font-family: 'Unbounded', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 12px 20px;
            box-shadow: 3px 3px 0px #000;
            transition: all 0.1s ease;
        }

        .btn:active {
            transform: translate(2px, 2px);
            box-shadow: 1px 1px 0px #000;
        }

        .btn-primary {
            background-color: var(--acid-green);
            color: var(--pitch-black);
        }

        .btn-primary:hover {
            background-color: var(--pitch-black);
            color: var(--acid-green);
            border-color: var(--acid-green);
        }

        .btn-outline-light {
            border-color: var(--raw-white);
            color: var(--raw-white);
        }

        .badge-brand {
            background-color: var(--pitch-black);
            color: var(--acid-green);
            font-family: 'Space Mono', monospace;
            font-weight: 700;
            padding: 5px 10px;
            border: 1px solid var(--acid-green);
        }

        .form-control,
        .form-select {
            border-radius: 0;
            border: var(--brutal-border);
            background-color: var(--raw-white);
            font-weight: 700;
        }

        .form-control:focus {
            box-shadow: 4px 4px 0px var(--acid-green);
            border-color: var(--pitch-black);
        }

        /* Hero Underground */
        .hero-section {
            background-color: var(--pitch-black) !important;
            color: var(--acid-green);
            border: 6px solid var(--pitch-black);
            outline: 2px solid var(--acid-green);
            margin-bottom: 3rem;
        }

        .display-3 {
            line-height: 0.9;
            margin-bottom: 2rem;
        }

        /* Label effect */
        .price-tag {
            background: var(--pitch-black);
            color: var(--acid-green);
            padding: 5px 15px;
            font-weight: 700;
            display: inline-block;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                INDIEBRAND_MARKET
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">EXPLORAR</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="mis_productos.php">MI_STOCK</a></li>
                        <li class="nav-item"><a class="nav-link" href="mis_ventas.php">LOGURAS_VENTAS</a></li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="carrito.php" class="btn btn-primary btn-sm pt-2">
                            CARRITO [<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>]
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                USR: <?= htmlspecialchars(strtoupper($_SESSION['username'])) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-0 border-2 border-dark rounded-0">
                                <li><a class="dropdown-item fw-bold py-2 bg-white text-dark"
                                        href="mi_perfil.php">CONFIG_PERFIL</a></li>
                                <li>
                                    <hr class="dropdown-divider m-0 border-2">
                                </li>
                                <li><a class="dropdown-item fw-bold py-2 bg-danger text-white"
                                        href="logout.php">EXIT_SYSTEM</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="nav-link">LOGIN</a>
                        <a href="registro.php" class="btn btn-primary btn-sm">JOIN_THE_CULT</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mt-5">