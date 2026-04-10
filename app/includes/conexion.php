<?php
// conexion.php - Conexión endurecida con secretos aislados (ASIR-HARDENING)
// El archivo con los datos reales está en una ruta NO ACCESIBLE desde el navegador (fuera de root web).

// Cargo la configuración de una ruta segura (ajustar según despliegue real)
$configPath = __DIR__ . '/../../secrets/config_sentinel_db.php';

if (!file_exists($configPath)) {
    error_log("CRITICAL: El archivo de secretos $configPath no existe.");
    die("Error crítico de sistema. Contacte con soporte técnico.");
}

$c = include $configPath;

$dsn = "mysql:host={$c['host']};dbname={$c['db']};charset={$c['charset']}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false, // Prevención SQL Injection Activa
];

// Si Amazon nos obliga a usar SSL (como en RDS), añadimos el certificado
if (!empty($c['ssl_ca'])) {
    $options[PDO::MYSQL_ATTR_SSL_CA] = $c['ssl_ca'];
}

try {
    $conexion = new PDO($dsn, $c['user'], $c['pass'], $options);
}
catch (\PDOException $e) {
    // Seguridad por Oscuridad: No enseñamos datos técnicos al usuario real
    error_log($e->getMessage());
    die("Error de conexión interno. Por favor, contacte con el administrador.");
}
?>