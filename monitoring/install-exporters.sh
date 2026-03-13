#!/bin/bash
# ============================================================
#  SENTINEL - Script de instalación de exporters
#  Ejecutar en: Servidor Web y Servidor DB
#  Según el rol, instalará los exporters correspondientes
#  Uso: sudo bash install-exporters.sh [web|db] <LOKI_PRIVATE_IP>
# ============================================================

set -e

ROLE=${1:-"web"}
LOKI_IP=${2:-"10.0.X.X"}   # IP privada de la EC2 de Monitorización
NODE_EXPORTER_VER="1.8.1"
APACHE_EXPORTER_VER="1.0.6"
MYSQL_EXPORTER_VER="0.15.1"
PROMTAIL_VER="2.9.7"

echo "============================================"
echo "  SENTINEL - Instalando exporters (rol: $ROLE)"
echo "============================================"

# ── 1. Node Exporter (TODOS los servidores) ─────────────────
echo "[1/4] Instalando Node Exporter..."
cd /tmp
curl -sLO "https://github.com/prometheus/node_exporter/releases/download/v${NODE_EXPORTER_VER}/node_exporter-${NODE_EXPORTER_VER}.linux-amd64.tar.gz"
tar xzf node_exporter-*.tar.gz
sudo mv node_exporter-*/node_exporter /usr/local/bin/
sudo useradd -rs /bin/false node_exporter 2>/dev/null || true

sudo tee /etc/systemd/system/node_exporter.service > /dev/null <<EOF
[Unit]
Description=Node Exporter - SENTINEL
After=network.target

[Service]
User=node_exporter
ExecStart=/usr/local/bin/node_exporter
Restart=always

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable --now node_exporter
echo "  ✅ Node Exporter activo en :9100"

# ── 2. Apache Exporter (solo WEB) ───────────────────────────
if [ "$ROLE" == "web" ]; then
  echo "[2/4] Instalando Apache Exporter..."
  cd /tmp
  curl -sLO "https://github.com/Lusitaniae/apache_exporter/releases/download/v${APACHE_EXPORTER_VER}/apache_exporter-${APACHE_EXPORTER_VER}.linux-amd64.tar.gz"
  tar xzf apache_exporter-*.tar.gz
  sudo mv apache_exporter-*/apache_exporter /usr/local/bin/

  # Habilitar mod_status en Apache (necesario para el exporter)
  sudo tee /etc/apache2/conf-available/server-status.conf > /dev/null <<EOF
<Location "/server-status">
    SetHandler server-status
    Require local
</Location>
EOF
  sudo a2enconf server-status 2>/dev/null || true
  sudo apachectl graceful 2>/dev/null || sudo systemctl reload httpd 2>/dev/null || true

  sudo tee /etc/systemd/system/apache_exporter.service > /dev/null <<EOF
[Unit]
Description=Apache Exporter - SENTINEL
After=network.target

[Service]
ExecStart=/usr/local/bin/apache_exporter --scrape_uri=http://localhost/server-status?auto
Restart=always

[Install]
WantedBy=multi-user.target
EOF
  sudo systemctl daemon-reload
  sudo systemctl enable --now apache_exporter
  echo "  ✅ Apache Exporter activo en :9117"
fi

# ── 3. MySQL/MariaDB Exporter (solo DB) ─────────────────────
if [ "$ROLE" == "db" ]; then
  echo "[2/4] Instalando MariaDB Exporter..."
  cd /tmp
  curl -sLO "https://github.com/prometheus/mysqld_exporter/releases/download/v${MYSQL_EXPORTER_VER}/mysqld_exporter-${MYSQL_EXPORTER_VER}.linux-amd64.tar.gz"
  tar xzf mysqld_exporter-*.tar.gz
  sudo mv mysqld_exporter-*/mysqld_exporter /usr/local/bin/

  # Usuario de MariaDB para el exporter (ejecutar en MariaDB como root)
  echo "  ⚠️  Ejecuta esto en MariaDB como root:"
  echo "  CREATE USER 'exporter'@'localhost' IDENTIFIED BY 'ExporterPass123!';"
  echo "  GRANT PROCESS, REPLICATION CLIENT, SELECT ON *.* TO 'exporter'@'localhost';"
  echo "  FLUSH PRIVILEGES;"

  sudo tee /etc/.mysqld_exporter.cnf > /dev/null <<EOF
[client]
user=exporter
password=ExporterPass123!
EOF
  sudo chmod 400 /etc/.mysqld_exporter.cnf

  sudo tee /etc/systemd/system/mysqld_exporter.service > /dev/null <<EOF
[Unit]
Description=MariaDB Exporter - SENTINEL
After=network.target

[Service]
ExecStart=/usr/local/bin/mysqld_exporter --config.my-cnf=/etc/.mysqld_exporter.cnf
Restart=always

[Install]
WantedBy=multi-user.target
EOF
  sudo systemctl daemon-reload
  sudo systemctl enable --now mysqld_exporter
  echo "  ✅ MariaDB Exporter activo en :9104"
fi

# ── 4. Promtail (TODOS - envía logs a Loki) ─────────────────
echo "[4/4] Instalando Promtail (logs → Loki)..."
cd /tmp
curl -sLO "https://github.com/grafana/loki/releases/download/v${PROMTAIL_VER}/promtail-linux-amd64.zip"
unzip -o promtail-linux-amd64.zip
sudo mv promtail-linux-amd64 /usr/local/bin/promtail
sudo chmod +x /usr/local/bin/promtail

HOSTNAME=$(hostname)
sudo mkdir -p /etc/promtail

sudo tee /etc/promtail/promtail-config.yml > /dev/null <<EOF
server:
  http_listen_port: 9080
  grpc_listen_port: 0
positions:
  filename: /tmp/positions.yaml
clients:
  - url: http://${LOKI_IP}:3100/loki/api/v1/push
scrape_configs:
  - job_name: '${HOSTNAME}-logs'
    static_configs:
      - targets: [localhost]
        labels:
          job: '${ROLE}'
          host: '${HOSTNAME}'
          __path__: /var/log/{secure,messages,httpd/*,mysql/*,syslog,auth.log}
EOF

sudo tee /etc/systemd/system/promtail.service > /dev/null <<EOF
[Unit]
Description=Promtail - SENTINEL Log Agent
After=network.target

[Service]
ExecStart=/usr/local/bin/promtail -config.file=/etc/promtail/promtail-config.yml
Restart=always

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable --now promtail
echo "  ✅ Promtail activo, enviando logs a Loki en ${LOKI_IP}:3100"

echo ""
echo "============================================"
echo "  ✅ Instalación completada (rol: $ROLE)"
echo "  Verifica en Prometheus: http://<MON_IP>:9090/targets"
echo "============================================"
