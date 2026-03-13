#!/bin/bash
# ============================================================
#  SENTINEL - Script de arranque del stack de monitorización
#  Sustituye las variables en los configs y lanza Docker Compose
# ============================================================

set -e

echo "🛡️  SENTINEL - Iniciando stack de monitorización..."

# Cargar variables del .env
if [ ! -f .env ]; then
  echo "❌ ERROR: No se encuentra el fichero .env"
  echo "   Copia .env.example como .env y configura las IPs privadas"
  exit 1
fi

source .env

# Verificar que las IPs privadas están configuradas
if [[ "$WEB_PRIVATE_IP" == *"X"* ]] || [[ "$DB_PRIVATE_IP" == *"X"* ]]; then
  echo "❌ ERROR: Configura las IPs privadas en el fichero .env antes de continuar"
  exit 1
fi

echo "📋 Configuración encontrada:"
echo "   Web Server IP:   $WEB_PRIVATE_IP"
echo "   DB Server IP:    $DB_PRIVATE_IP"
echo "   Wazuh IP:        $WAZUH_PRIVATE_IP"

# Generar prometheus.yml final sustituyendo variables
echo ""
echo "⚙️  Generando prometheus.yml..."
envsubst < prometheus/prometheus.yml.template > prometheus/prometheus.yml
echo "   ✅ prometheus.yml generado"

# Generar alertmanager.yml final
echo "⚙️  Generando alertmanager.yml..."
envsubst < alertmanager/alertmanager.yml > /tmp/alertmanager_rendered.yml
cp /tmp/alertmanager_rendered.yml alertmanager/alertmanager.yml
echo "   ✅ alertmanager.yml generado"

# Generar grafana datasources con IPs reales
echo "⚙️  Generando datasources de Grafana..."
envsubst < grafana/provisioning/datasources/datasources.yml > /tmp/datasources_rendered.yml
cp /tmp/datasources_rendered.yml grafana/provisioning/datasources/datasources.yml
echo "   ✅ datasources.yml generado"

# Arrancar el stack
echo ""
echo "🐳 Arrancando contenedores..."
docker compose up -d

echo ""
echo "✅ Stack arrancado. Accede a:"
echo "   📊 Grafana:      http://$(curl -s http://169.254.169.254/latest/meta-data/public-ipv4 2>/dev/null || echo '<IP-PUBLICA>'):3000"
echo "   📈 Prometheus:   http://localhost:9090"
echo "   🔔 Alertmanager: http://localhost:9093"
echo ""
echo "⚠️  Recuerda instalar los exporters en Web y DB:"
echo "   En Web Server: sudo bash install-exporters.sh web <ESTA_IP_PRIVADA>"
echo "   En DB Server:  sudo bash install-exporters.sh db  <ESTA_IP_PRIVADA>"
