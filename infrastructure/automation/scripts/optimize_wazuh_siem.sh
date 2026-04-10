#!/bin/bash
# ==============================================================================
# Script de Optimización y Mejora del SIEM (Wazuh/OpenSearch)
# ==============================================================================
# Requisitos: Ejecutar como root en el servidor del SIEM Wazuh
# ==============================================================================

echo "[*] Iniciando optimización del SIEM Wazuh..."

# 1. Ajustar memoria RAM de OpenSearch (JVM) a 1GB
JVM_OPTIONS_FILE="/etc/wazuh-indexer/jvm.options"

if [ -f "$JVM_OPTIONS_FILE" ]; then
    echo "[-] Archivo jvm.options encontrado. Ajustando RAM a 1GB..."
    # Reemplazar -Xms y -Xmx
    sed -i -e 's/^-Xms.*/-Xms1g/g' $JVM_OPTIONS_FILE
    sed -i -e 's/^-Xmx.*/-Xmx1g/g' $JVM_OPTIONS_FILE
    echo "[+] Parámetros JVM ajustados a 1GB (-Xms1g / -Xmx1g)."
else
    echo "[!] ATENCIÓN: Archivo $JVM_OPTIONS_FILE no encontrado. Omitiendo optimización de RAM."
fi

# 2. Mejorar el formato del script de Telegram
INTEGRATION_SCRIPT="/var/ossec/integrations/custom-telegram"
INTEGRATION_DIR="/var/ossec/integrations"

if [ -d "$INTEGRATION_DIR" ]; then
    echo "[-] Generando script de Telegram con formato mejorado..."
    
    cat << 'EOF' > $INTEGRATION_SCRIPT
#!/usr/bin/env python3
# Integración de Wazuh con Telegram - Formato Mejorado
import sys
import json
import urllib.request
import urllib.parse

# Leer argumentos de Wazuh
# sys.argv[1] = Ruta temporal del archivo de alerta json
# sys.argv[2] = user o hook_url (depende de la configuración, asumiremos bot endpoint completo incluyendo chat_id u ossec lo pasa)
alert_file_path = sys.argv[1]

# Cargar la alerta
try:
    with open(alert_file_path) as f:
        alert = json.load(f)
except Exception as e:
    sys.exit(0)

# Extraer datos relevantes
rule_id = alert.get("rule", {}).get("id", "N/A")
level = alert.get("rule", {}).get("level", "N/A")
description = alert.get("rule", {}).get("description", "N/A")
agent_name = alert.get("agent", {}).get("name", "N/A")
agent_ip = alert.get("agent", {}).get("ip", "N/A")
srcip = alert.get("data", {}).get("srcip", "N/A")

if srcip == "N/A":
    srcip = alert.get("srcip", "N/A")

# Extraer detalles específicos
full_log = alert.get("full_log", "N/A")
location = alert.get("location", "N/A")

# Determinar severidad e icono
level_int = int(level) if level != "N/A" else 0
if level_int >= 12:
    severity = "🔴 CRÍTICO"
    icon = "🔥"
elif level_int >= 7:
    severity = "🟠 ALTA"
    icon = "🚨"
else:
    severity = "🟡 MEDIA"
    icon = "⚠️"

# Si es una alerta de Active Response (Ban)
is_active_response = "active_response" in alert.get("rule", {}).get("groups", [])
if "Active response:" in description or "firewall-drop" in description:
    severity = "🛡️ BLOQUEO"
    icon = "🚫"
    title = "<b>SENTINEL ACTIVE RESPONSE</b>"
    action_info = f"<b>🚫 Acción:</b> <code>Firewall-Drop (Ban IP)</code>\n<b>🎯 IP Atacante:</b> <code>{srcip}</code>"
else:
    title = "<b>SENTINEL SECURITY ALERT</b>"
    action_info = f"<b>🎯 IP Origen:</b> <code>{srcip}</code>"

# Construir el mensaje formateado en HTML
message = f"""
{icon} {title} {icon}
━━━━━━━━━━━━━━━━━━
📊 <b>Severidad:</b> <code>{severity} (Nivel {level})</code>
📌 <b>Regla:</b> <code>{rule_id}</code>
🖥️ <b>Agente:</b> <code>{agent_name}</code> (<code>{agent_ip}</code>)

📝 <b>Descripción:</b>
<i>{description}</i>

{action_info}
📍 <b>Origen:</b> <code>{location}</code>
━━━━━━━━━━━━━━━━━━
"""

# Leer hook_url (Ej: https://api.telegram.org/botTOKEN/sendMessage?chat_id=YOUR_ID)
# Generalmente el hook_url configurado en ossec.conf contiene el endpoint con el chat_id
hook_url = sys.argv[2] if len(sys.argv) > 2 else ""

if not hook_url:
    sys.exit(0)

headers = {'Content-Type': 'application/json'}
payload = {
    'text': message,
    'parse_mode': 'HTML'
}

data = json.dumps(payload).encode('utf-8')
req = urllib.request.Request(hook_url, data=data, headers=headers)

# Log manual para debug si falla
try:
    with urllib.request.urlopen(req) as response:
        # success
        pass
except Exception as e:
    # Escribir el error en un log temporal para diagnóstico
    with open("/tmp/telegram_error.log", "a") as log:
        log.write(f"Error enviando a Telegram: {str(e)}\n")
    pass
EOF

    # Aplicar permisos
    chmod 750 $INTEGRATION_SCRIPT
    chown root:wazuh $INTEGRATION_SCRIPT
    echo "[+] Script de Telegram actualizado y permisos establecidos en $INTEGRATION_SCRIPT"
else
    echo "[!] Directorio $INTEGRATION_DIR no encontrado. Omitiendo script de Telegram."
fi

# 3. Reiniciar servicios si existen
if systemctl list-unit-files | grep -q wazuh-indexer; then
    echo "[-] Reiniciando wazuh-indexer para aplicar cambios de RAM..."
    systemctl restart wazuh-indexer
fi

if systemctl list-unit-files | grep -q wazuh-manager; then
    echo "[-] Reiniciando wazuh-manager para aplicar integración de Telegram..."
    systemctl restart wazuh-manager
fi

echo "[✓] ¡Proceso de optimización y actualización completado!"

