# SENTINEL - Stack de Monitorización
## Guía de Despliegue

### Arquitectura

```
[Web EC2 :9100 :9117]  [DB EC2 :9100 :9104]  [Wazuh :9200]
         │                      │                    │
         └──────────────────────┴────────────────────┘
                                │ IPs Privadas (estables)
                    [Monitoring EC2 - esta máquina]
                    ┌─────────────────────────────┐
                    │  Prometheus  :9090           │
                    │  Grafana     :3000           │
                    │  Alertmanager :9093          │
                    │  Loki        :3100           │
                    └─────────────────────────────┘
```

### ⚠️ Sobre las IPs en AWS

- Las **IPs privadas** (10.0.x.x) son **estables** → úsalas siempre para comunicación interna
- Las **IPs públicas** cambian en cada reinicio → asigna una **Elastic IP** a la EC2 de monitoring para el acceso externo a Grafana

---

### Paso 1 — Preparar la EC2 de Monitorización

```bash
# Instalar Docker
sudo apt update && sudo apt install -y docker.io docker-compose-plugin gettext-base
sudo usermod -aG docker ubuntu
newgrp docker

# Clonar/copiar el directorio monitoring/
# Editar el .env con las IPs privadas reales
nano .env
```

### Paso 2 — Configurar el .env

```bash
WEB_PRIVATE_IP=10.0.1.250      # IP privada del Web Server (no cambia)
DB_PRIVATE_IP=10.0.2.61        # IP privada del DB Server (no cambia)
WAZUH_PRIVATE_IP=10.0.X.X      # IP privada del Wazuh Manager
WAZUH_INDEXER_USER=admin
WAZUH_INDEXER_PASSWORD=<contraseña del indexer de Wazuh>
```

**¿Cómo saber la IP privada del Wazuh Manager?**
```bash
ssh -i llaves-debian13.pem ubuntu@<IP_PUBLICA_WAZUH> "ip addr show eth0 | grep 'inet '"
```

### Paso 3 — Arrancar el stack

```bash
chmod +x start.sh
./start.sh
```

### Paso 4 — Instalar exporters en Web y DB

**En el servidor Web** (Amazon Linux 2023):
```bash
scp -i llaves-debian13.pem install-exporters.sh ec2-user@<IP_PUBLICA_WEB>:~/
ssh -i llaves-debian13.pem ec2-user@<IP_PUBLICA_WEB>
sudo bash install-exporters.sh web <MON_PRIVATE_IP>
```

**En el servidor DB** (via salto desde Web):
```bash
# Desde el servidor web:
scp install-exporters.sh ec2-user@10.0.2.61:~/
ssh ec2-user@10.0.2.61
sudo bash install-exporters.sh db <MON_PRIVATE_IP>
```

### Paso 5 — Security Groups necesarios

| Regla | Puerto | Origen | Destino |
|---|---|---|---|
| Scraping | 9100 | SG-MON | SG-WEB + SG-DB |
| Apache Exporter | 9117 | SG-MON | SG-WEB |
| MariaDB Exporter | 9104 | SG-MON | SG-DB |
| Wazuh Indexer | 9200 | SG-MON | SG-WAZUH |
| Grafana | 3000 | Tu IP | SG-MON |
| Loki (inbound) | 3100 | SG-WEB + SG-DB | SG-MON |

### Paso 6 — Acceder a Grafana

1. Abre `http://<IP_PUBLICA_MON>:3000`
2. Login: `admin` / `Sentinel@2025!`
3. Los datasources (Prometheus, Loki, Wazuh-OpenSearch) ya están preconfigurados
4. Importa dashboards desde https://grafana.com/grafana/dashboards/:
   - **Node Exporter Full**: ID `1860`
   - **Apache**: ID `3894`  
   - **MariaDB**: ID `7362`
   - **Wazuh**: busca en la comunidad o usa el datasource OpenSearch manualmente
