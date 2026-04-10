#!/bin/bash
# ============================================================
#  update_duckdns_status.sh
#  Actualiza statuspsicopompo.duckdns.org → IP del servidor Grafana
#  Ejecutar en cron desde el servidor de monitorización:
#    */5 * * * * /opt/scripts/update_duckdns_status.sh >> /var/log/duckdns_status.log 2>&1
# ============================================================

DOMAIN="statuspsicopompo"
TOKEN="2256f5ca-d5ce-432e-9bc4-c63827971e3c"

# La IP del servidor de monitorización es estable (Elastic IP o IP fija).
# Detectamos la IP pública actual del servidor automáticamente:
CURRENT_IP=$(curl -s https://api.ipify.org)

if [ -z "$CURRENT_IP" ]; then
    echo "[$(date)] ERROR: No se pudo obtener la IP pública del servidor."
    exit 1
fi

echo "[$(date)] Actualizando $DOMAIN.duckdns.org con IP: $CURRENT_IP"
RESULT=$(curl -s "https://www.duckdns.org/update?domains=${DOMAIN}&token=${TOKEN}&ip=${CURRENT_IP}")
echo "[$(date)] Respuesta DuckDNS: $RESULT"
