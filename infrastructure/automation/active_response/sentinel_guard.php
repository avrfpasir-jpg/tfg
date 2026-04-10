<?php
/**
 * Sentinel - Active Response Daemon (PHP version)
 * Este script debe ejecutarse como servicio o mediante un cron de alta frecuencia
 * Escucha la tabla logs_seguridad y ejecuta bloqueos mediante AWS Security Groups o IPtables.
 */

require_once __DIR__ . '/../../includes/conexion.php';

// Definir umbral de bloqueo
$MAX_RISK_THRESHOLD = 5;

echo "[SENTINEL] Iniciando demonio de Respuesta Activa...\n";

while (true) {
    try {
        // Buscar eventos de alto riesgo no procesados (asumiendo que añadiremos una columna 'procesado' si fuera necesario, 
        // o simplemente actuando sobre los últimos N segundos)
        $stmt = $conexion->prepare("SELECT * FROM logs_seguridad WHERE nivel_riesgo >= ? AND fecha > DATE_SUB(NOW(), INTERVAL 10 SECOND)");
        $stmt->execute([$MAX_RISK_THRESHOLD]);
        $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($eventos as $evento) {
            // Extraer IP de los detalles (Asumiendo que guardamos la IP en 'detalles' o columna dedicada)
            // Lógica de ejemplo: Si el detalle contiene una IP, bloquearla.
            if (preg_match('/(\d{1,3}\.){3}\d{1,3}/', $evento['detalles'], $match)) {
                $ip_to_block = $match[0];
                echo "[!] ALERTA: Detectado evento crítico de $ip_to_block. Ejecutando bloqueo...\n";

                // Opción 1: Bloqueo local vía iptables (Requiere permisos de root)
                exec("sudo iptables -I INPUT -s $ip_to_block -j DROP");

                // Opción 2: Futura integración con AWS CLI para modificar Security Group
                // exec("aws ec2 authorize-security-group-ingress --group-id sg-XXXX --protocol tcp --port 80 --cidr $ip_to_block/32 --type deny");

                error_log("[SENTINEL] Bloqueo ejecutado para IP: $ip_to_block debido a evento: " . $evento['evento']);
            }
        }
    } catch (Exception $e) {
        echo "[ERROR] " . $e->getMessage() . "\n";
    }

    sleep(5); // Ciclo de 5 segundos
}
