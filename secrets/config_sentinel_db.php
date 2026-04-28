<?php
// config_sentinel_db.php - Configuración de base de datos aislada (SECRETO)
// Este archivo NO DEBE estar accesible para el servidor web directamente.
return [
    'host'    => getenv('DB_HOST') ?: 'database-sentinel.cyv3h2yxn0v5.us-east-1.rds.amazonaws.com',
    'db'      => getenv('DB_NAME') ?: 'tienda_segura',
    'user'    => getenv('DB_USER') ?: 'sentinel_web',
    'pass'    => getenv('DB_PASS') ?: 'S3ntin3l_P4ss_2026!',
    'charset' => 'utf8mb4',
    // Añadimos el certificado para la conexión segura obligatoria por Amazon
    'ssl_ca'  => __DIR__ . '/global-bundle.pem'
];
?>
