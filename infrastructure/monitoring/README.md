# SENTINEL - Stack de Monitorización
## Guía de Despliegue

### Arquitectura

```
[Web-Node-1 :9100 :9117]   [Wazuh-Manager :9200]
  10.0.1.250                 10.0.1.170
         │                        │
         └────────────────────────┘
                      │ IPs Privadas estables
          [Sentinel-Monitoring — esta máquina]
          10.0.1.233
          ┌──────────────────────────────────┐
          │  Prometheus    :9090             │
          │  Grafana       :3000             │
          │  Alertmanager  :9093             │
          │  Loki          :3100             │
          └──────────────────────────────────┘
                      │
               [Telegram Bot API]
               Alertas críticas < 30s
```

> **Nota sobre la BD:** Sentinel-DB corre en **Amazon RDS MariaDB 10.5** (`10.0.0.242`).
> Al ser un servicio gestionado no es posible instalar exporters directamente. El exporter
> MariaDB (`mysqld_exporter`) se despliega en Web-Node-1 apuntando al endpoint RDS vía TCP 3306.

---

### Paso 1 — Preparar la EC2 de Monitorización

```bash
# Instalar Docker y dependencias
sudo apt update && sudo apt install -y docker.io docker-compose-plugin gettext-base
sudo usermod -aG docker ubuntu
newgrp docker

# Copiar el directorio monitoring/ al servidor
# Editar el .env con los valores reales antes de arrancar
nano .env
```

---

### Paso 2 — Configurar el .env

```bash
# IPs privadas (estables, no cambian con reinicios)
WEB_PRIVATE_IP=10.0.1.250          # Web-Node-1 — Apache + PHP 8.4
RDS_ENDPOINT=10.0.0.242            # Sentinel-DB — RDS MariaDB 10.5 (no EC2)
WAZUH_PRIVATE_IP=10.0.1.170        # Wazuh-Manager
MONITORING_PRIVATE_IP=10.0.1.233   # Esta máquina

# Credenciales Wazuh Indexer (OpenSearch)
WAZUH_INDEXER_USER=admin
WAZUH_INDEXER_PASSWORD=<contraseña del indexer de Wazuh>

# Telegram — alertas críticas
# IMPORTANTE: nunca commitear valores reales. Usar .env excluido en .gitignore
TELEGRAM_BOT_TOKEN=<token del bot obtenido desde @BotFather>
TELEGRAM_CHAT_ID=<chat_id del canal o usuario destino>
```

> El sistema usa `envsubst` en `start.sh` para inyectar estas variables en las
> plantillas de configuración (`.template`) antes de arrancar los contenedores.
> Los archivos `.yml` finales se generan en tiempo de ejecución y nunca se commitean.

---

### Paso 3 — Arrancar el stack

```bash
chmod +x start.sh
./start.sh
```

`start.sh` realiza en orden:
1. Carga el `.env`
2. Aplica `envsubst` sobre las plantillas de Alertmanager y Prometheus
3. Levanta todos los contenedores con `docker compose up -d`

---

### Paso 4 — Instalar exporters en Web-Node-1

Los exporters del nodo web (node_exporter, apache_exporter) y el mysqld_exporter
apuntando a RDS se instalan **únicamente en Web-Node-1**:

```bash
# Desde la máquina de monitoring o con acceso directo
scp -i <clave.pem> install-exporters.sh ec2-user@10.0.1.250:~/
ssh -i <clave.pem> ec2-user@10.0.1.250
sudo bash install-exporters.sh web 10.0.1.233
```

> **RDS no necesita exporter en el servidor.** El `mysqld_exporter` en Web-Node-1
> se conecta al endpoint RDS `10.0.0.242:3306` con credenciales de solo lectura.

---

### Paso 5 — Security Groups necesarios

| Regla | Puerto | Origen | Destino |
|---|---|---|---|
| node_exporter | 9100 | SG-MON | SG-WEB |
| apache_exporter | 9117 | SG-MON | SG-WEB |
| mysqld_exporter | 9104 | SG-MON | SG-WEB |
| Wazuh Indexer | 9200 | SG-MON | SG-WAZUH |
| Wazuh Agent | 1514/UDP | SG-WEB | SG-WAZUH |
| Wazuh Enrollment | 1515/TCP | SG-WEB | SG-WAZUH |
| Grafana | 3000 | Tu IP / ALB | SG-MON |
| Loki (inbound) | 3100 | SG-WEB | SG-MON |
| Prometheus | 9090 | Tu IP | SG-MON |
| Alertmanager | 9093 | SG-MON (interno) | SG-MON |

> Los puertos 1514/1515 de Wazuh deben estar abiertos o los agentes no enviarán
> eventos al Manager — error silencioso que deja el SIEM sin datos.

---

### Paso 6 — Acceder a Grafana

1. Abre `https://statuspsicopompo.duckdns.org` (o `http://10.0.1.233:3000` en red interna)
2. Login: `admin` / (contraseña configurada en despliegue)
3. Los datasources (Prometheus, Loki, Wazuh-OpenSearch) ya están preconfigurados
4. Dashboards recomendados de Grafana.com:
   - **Node Exporter Full**: ID `1860`
   - **Apache**: ID `3894`
   - **MariaDB**: ID `7362`

---

### Solución de problemas frecuentes

| Síntoma | Causa probable | Solución |
|---|---|---|
| Alertmanager no arranca | Variables no interpoladas en YAML | Verificar que `envsubst` está instalado y ejecutar `./start.sh` completo |
| Wazuh sin eventos en dashboard | SG bloqueando 1514/1515 | Abrir puertos UDP 1514 y TCP 1515 en SG-WAZUH desde SG-WEB |
| Prometheus scrape timeout | node_exporter no instalado o SG cerrado | Verificar que el exporter corre en el nodo y puerto 9100 abierto |
| Monitoring bloqueado por Wazuh | Active Response falso positivo (scraping = anomalía) | Añadir `10.0.1.233` a la `white_list` de Active Response en `ossec.conf` |
| Telegram sin alertas | Token o chat_id incorrectos en .env | Revisar `.env`, regenerar token en @BotFather si fue comprometido |
