<?php
/**
 * seguridad.php - Minimal security logging system
 */

function registrar_evento($conexion, $evento, $detalles, $riesgo = 1)
{
    try {
        $stmt = $conexion->prepare("INSERT INTO logs_seguridad (evento, detalles, nivel_riesgo) VALUES (?, ?, ?)");
        $stmt->execute([$evento, $detalles, $riesgo]);
    } catch (Exception $e) {
        // Silently fail if logging errors to prevent breaking the flow
    }
}
?>