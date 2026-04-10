# 🛡️ Configuración de Grupos de Seguridad (Firewall) - AWS

Esta guía detalla las reglas de entrada (Inbound Rules) aplicadas en el entorno AWS para el proyecto SENTINEL, garantizando el principio de mínimo privilegio.

---

## 🏗️ 1. SEGURIDAD DE LA BASE DE DATOS (SG-RDS)
*Objetivo: Proteger los datos y permitir acceso solo a la aplicación y monitorización.*

| Puerto | Protocolo | Origen | Descripción |
| :--- | :--- | :--- | :--- |
| **3306** | TCP | `10.0.1.250/32` | Acceso SQL desde el Servidor Web Sentinel |
| **9104** | TCP | `10.0.1.233/32` | Métricas MySQL para Grafana (MySQL Exporter) |
| **9100** | TCP | `10.0.1.233/32` | Métricas de Sistema (Node Exporter) |
| **22** | TCP | `10.0.1.250/32` | Administración vía SSH (Jump Host) |

---

## 🌐 2. SEGURIDAD WEB (SG-WEB)
*Objetivo: Recibir tráfico del ALB y permitir administración segura.*

| Puerto | Protocolo | Origen | Descripción |
| :--- | :--- | :--- | :--- |
| **80** | TCP | `SG-ALB` / `10.0.1.117` | Tráfico HTTP proveniente del Balanceador |
| **443** | TCP | `SG-ALB` / `10.0.1.117` | Tráfico HTTPS proveniente del Balanceador |
| **22** | TCP | `0.0.0.0/0`* | Gestión remota SSH (Recomendado: Restringir a Mi IP) |
| **9100** | TCP | `10.0.1.233/32` | Métricas de Sistema para Monitorización |
| **9117** | TCP | `10.0.1.233/32` | Exportador de logs/métricas específico |

---

## ⚖️ 3. SEGURIDAD DEL BALANCEADOR (SG-ALB)
*Promueve el punto de entrada único desde Internet.*

| Puerto | Protocolo | Origen | Descripción |
| :--- | :--- | :--- | :--- |
| **80** | TCP | `0.0.0.0/0` | Entrada web pública (Redirige a HTTPS) |
| **443** | TCP | `0.0.0.0/0` | Entrada web segura para usuarios finales |
| **8404** | TCP | `81.0.53.214/32` | Panel de estadísticas (Solo Admin) |
| **22** | TCP | `81.0.53.214/32` | Gestión SSH del nodo de balanceo |

---

## 📊 4. SEGURIDAD MONITORIZACIÓN (SG-GRAFANA)
*Panel de control visual del estado del sistema.*

| Puerto | Protocolo | Origen | Descripción |
| :--- | :--- | :--- | :--- |
| **3000** | TCP | `0.0.0.0/0` | Acceso visual a Paneles de Grafana |
| **22** | TCP | `0.0.0.0/0`* | SSH (Se recomienda restringir a Mi IP) |

---

## 🦅 5. SEGURIDAD WAZUH (SG-SIEM)
*Comunicación interna con los agentes y externa con el administrador.*

| Puerto | Protocolo | Origen | Descripción |
| :--- | :--- | :--- | :--- |
| **1514** | TCP/UDP | `10.0.0.0/16` | Comunicación de agentes (Logs) |
| **1515** | TCP | `10.0.0.0/16` | Registro de nuevos agentes |
| **55000** | TCP | `10.0.0.0/16` | API de Wazuh para el Dashboard |
| **443** | TCP | `81.0.53.214/32` | Acceso Web a la consola de Wazuh |
| **9200** | TCP | `10.0.1.233/32` | Consulta de índices desde Grafana |

> [!IMPORTANT]
> Se ha verificado la coherencia entre las IPs privadas declaradas en `conexiones.txt` y las reglas de los Grupos de Seguridad. El entorno está blindado contra accesos externos directos a la Base de Datos y a los servicios internos.
