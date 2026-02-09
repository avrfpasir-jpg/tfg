<?php
// seguridad.php - Núcleo del SIEM y Honeypot

function obtener_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function registrar_evento($conexion, $tipo, $detalle, $nivel = 1)
{
    $ip = obtener_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

    try {
        // 1. Gestionar IP en tabla direcciones_ip
        $stmt_ip = $conexion->prepare("SELECT id FROM direcciones_ip WHERE ip = :ip");
        $stmt_ip->execute([':ip' => $ip]);
        $ip_row = $stmt_ip->fetch();

        if ($ip_row) {
            $ip_id = $ip_row['id'];
            // Actualizar timestamp implícito por ON UPDATE CURRENT_TIMESTAMP
            $conexion->query("UPDATE direcciones_ip SET ultima_actividad = NOW() WHERE id = $ip_id");
        } else {
            $stmt_new_ip = $conexion->prepare("INSERT INTO direcciones_ip (ip, pais) VALUES (:ip, 'Desconocido')");
            $stmt_new_ip->execute([':ip' => $ip]);
            $ip_id = $conexion->lastInsertId();
        }

        // 2. Insertar Log
        $stmt_log = $conexion->prepare("INSERT INTO logs_seguridad (ip_id, evento_tipo, detalle, user_agent, nivel_alerta) VALUES (:ip_id, :tipo, :detalle, :ua, :nivel)");
        $stmt_log->execute([
            ':ip_id' => $ip_id,
            ':tipo' => $tipo,
            ':detalle' => $detalle,
            ':ua' => $user_agent,
            ':nivel' => $nivel
        ]);

    } catch (Exception $e) {
        // Fallo silencioso del logger para no alertar al atacante
        // error_log("Fallo en SIEM: " . $e->getMessage());
    }
}

function detectar_sqli($input)
{
    // Patrones básicos de SQL Injection
    $patrones = [
        "/'/",
        "/\s+OR\s+/i",
        "/\s+AND\s+/i",
        "/UNION\s+SELECT/i",
        "/--/",
        "/#/"
    ];

    foreach ($patrones as $patron) {
        if (preg_match($patron, $input)) {
            return true;
        }
    }
    return false;
}
?>