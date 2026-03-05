#!/bin/bash
# -----------------------------------------------------------------------------
# SENTINEL - Permisos de Orquestación (Hardening Tier 1)
# -----------------------------------------------------------------------------

WEB_ROOT="/var/www/html"

echo "[*] Aplicando Hardening de Permisos en $WEB_ROOT..."

# 1. Asegurar propiedad al usuario de despliegue y grupo web
sudo chown -R ubuntu:www-data "$WEB_ROOT"

# 2. Permisos base: 755 para directorios (lectura/ejecución)
sudo find "$WEB_ROOT" -type d -exec chmod 755 {} \;

# 3. Permisos base: 644 para archivos (lectura)
sudo find "$WEB_ROOT" -type f -exec chmod 644 {} \;

# 4. Endurecimiento de archivos de configuración (Solo lectura para el dueño, lectura para el grupo si es necesario)
if [ -f "$WEB_ROOT/includes/conexion.php" ]; then
    sudo chmod 640 "$WEB_ROOT/includes/conexion.php"
    echo "[+] Archivo de conexión asegurado (640)."
fi

# 5. Permisos especiales para carpetas de carga (Escritura para Apache)
if [ -d "$WEB_ROOT/uploads" ]; then
    sudo chown -R www-data:www-data "$WEB_ROOT/uploads"
    sudo chmod -R 775 "$WEB_ROOT/uploads"
    echo "[+] Directorio /uploads configurado para escritura controlada."
fi

# 6. Protección de archivos ocultos y GIT
sudo find "$WEB_ROOT/.git" -type d -exec chmod 700 {} \; 2>/dev/null

echo "[✓] Orquestación de permisos completada satisfactoriamente."
