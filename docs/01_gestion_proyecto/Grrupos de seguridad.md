# 🛡️ Configuración de Security Groups - AWS Project

A continuación se detallan las reglas de entrada y salida organizadas por función de servidor.

---

## 🌐 1. Servidor Web
Gestiona el tráfico público y la administración básica del servidor.

| ID de Regla | Tipo | Protocolo | Puerto | Origen | Descripción |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `sgr-07d207b6704ee54a1` | TCP Personalizado | TCP | 9117 | 10.0.1.233/32 | Monitoreo |
| `sgr-00b07b88475f3131b` | TCP Personalizado | TCP | 9100 | 10.0.1.233/32 | Node Exporter |
| `sgr-07eaf678fbb94a132` | HTTPS | TCP | 443 | 0.0.0.0/0 | Acceso Web Seguro |
| `sgr-00e457caf1dd4a857` | Todo el tráfico | Todo | Todo | 10.0.2.0/24 | Comunicación VPC |
| `sgr-0022419661dab26ca` | HTTP | TCP | 80 | 0.0.0.0/0 | Acceso Web |
| `sgr-0839e7014af984551` | SSH | TCP | 22 | 0.0.0.0/0 | Administración |

---

## 🛡️ 2. SG-WAZUH (Seguridad SIEM)
Reglas para la arquitectura de Wazuh (Manager, Indexer y Agentes).

| ID de Regla | Puerto | Protocolo | Origen | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `sgr-0362e00361d76298b` | 22 | TCP | 81.0.53.214/32 | SSH (IP específica) |
| `sgr-0170a3444e63a7a10` | 9200 | TCP | 10.0.1.233/32 | Wazuh Indexer |
| `sgr-0630a7e8400217a6e` | 80 | TCP | 10.0.2.61/32 | Acceso Web Interno |
| `sgr-0a09ae7c7f806a46d` | 1515 | TCP | 10.0.0.0/16 | Registro de Agentes |
| `sgr-0adabe497b46ee39a` | 55000 | TCP | 10.0.0.0/16 | API Wazuh |
| `sgr-0f97662c8a04b7754` | 9100 | TCP | 10.0.1.233/32 | Monitoreo |
| `sgr-08205faef4b9656a4` | 443 | TCP | 81.0.53.214/32 | Dashboard (HTTPS) |
| `sgr-0977afd5e31bac077` | 1514 | TCP | 10.0.0.0/16 | Comunicación de Agentes |

---

## 📊 3. SG-MON (Prometheus & Grafana)
Servidor de monitoreo y visualización de datos.

| ID de Regla | Puerto | Protocolo | Origen | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `sgr-02bf759a74eab836b` | 22 | TCP | 0.0.0.0/0 | SSH Acceso |
| `sgr-0e57af46c5e052e0b` | 3000 | TCP | 0.0.0.0/0 | Interfaz Grafana |

---

## 🗄️ 4. RDS (Bases de Datos)
Configuración de persistencia de datos.

| Nombre/ID del Grupo | Dirección | Protocolo | Puerto | Origen / Destino |
| :--- | :--- | :--- | :--- | :--- |
| `database-sentinel-sg` | Inbound | - | - | 95.169.225.155/32 |
| `database-sentinel-sg` | Outbound | - | - | 0.0.0.0/0 |
| `rds-ec2-1 (sg-018db0...)`| Inbound | TCP | 3306/5432* | `sg-0be5d0e6dd0eddf2d` |

---

## ⚖️ 5. ALB (Application Load Balancer)
Punto de entrada principal para el tráfico de la aplicación.

| ID de Regla | Tipo | Protocolo | Puerto | Origen |
| :--- | :--- | :--- | :--- | :--- |
| `sgr-05d8ebb41be4b0982` | HTTP | TCP | 80 | 0.0.0.0/0 |
| `sgr-0d876aa649fd4bdb3` | HTTPS | TCP | 443 | 0.0.0.0/0 |

---
> **Nota de Seguridad:** Se recomienda revisar las reglas SSH (`Puerto 22`) con origen `0.0.0.0/0` y restringirlas a IPs conocidas para mejorar la postura de seguridad.