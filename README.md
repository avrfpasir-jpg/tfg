# 🛡️ SENTINEL (Tienda Segura)

![AWS Architecture](https://img.shields.io/badge/AWS-Infrastructure-FF9900?logo=amazonaws&logoColor=white)
![Security](https://img.shields.io/badge/Security-Zero%20Trust-red)
![Observability](https://img.shields.io/badge/Observability-WPG_Stack-blue)
![Status](https://img.shields.io/badge/Status-100%25_Completed-success)

## 1. Executive Summary

**SENTINEL** es una infraestructura cloud de grado industrial desplegada en Amazon Web Services (AWS), operando bajo los paradigmas de **Defensa en Profundidad (Defense in Depth)** y **Zero Trust**. 

Este proyecto implementa una solución *e-commerce* ("Psicopompo") altamente resiliente donde la seguridad y la observabilidad no son ideas de último momento, sino pilares fundacionales. Cada capa del despliegue (red, computación, aplicación y datos) ha sido aislada, monitorizada y asegurada proactivamente para garantizar la máxima disponibilidad tecnológica con un índice de exposición minimizado.

---

## 2. Infraestructura como Servicio (IaaS) y Topología de Red

La arquitectura descansa sobre una nube privada virtual (**VPC `10.0.0.0/16`**) segmentada estrictamente bajo un modelo **TIER-2**:

*   **Public Tier (Ingress):** Un único punto de exposición hacia internet. Aloja únicamente el Elastic Load Balancer (AWS ALB).
*   **Private Tier (Compute & Data):** Nodos completamente aislados sin visibilidad externa enrutable.

### Tabla de Direccionamiento y Componentes Core

| Nodo / Servicio | Rol | IP Privada (CIDR VPC) | Estado y Exposición |
| :--- | :--- | :--- | :--- |
| **Sentinel-ALB** | Balanceador L7 & SSL Termination | Dinámica | Público (`psicopompo.duckdns.org`) |
| **Squid Proxy** | NAT Gateway / Pasarela de Salida | N/A | Público (Outbound traffic only) |
| **Web-Node-1** | Aplicación: Apache/PHP 8.2 | `10.0.1.250` | Privado |
| **Sentinel-DB** | Base de Datos: Amazon RDS (MariaDB) | `10.0.0.242` | Privado |
| **Wazuh-Manager**| SIEM Security Hub | `10.0.1.170` | Privado |
| **Sentinel-Mon**| Stack Observabilidad (WPG) | `10.0.1.233` | Privado/Público (`statuspsicopompo...`) |

### Evolución de la Capa de Datos: El salto a Amazon RDS

> [!NOTE]
> **EC2 vs RDS: Justificación Estratégica**

| Métrica Analizada | Estado Anterior (EC2 Nativo) | Estado Actual (Amazon RDS) | Impacto / Justificación del Diseño |
| :--- | :--- | :--- | :--- |
| **Resiliencia (SPOF)** | Nodo único. Su caída implicaba Downtime Total. | Multi-AZ Ready. Arquitectura Desacoplada. | *Eliminación del Single Point of Failure en la capa de datos.* |
| **Gestión (Overhead)** | Requiere parcheo de SO y afino de MariaDB manual. | AWS Fully Managed, parches automatizados. | *Reducción drástica de carga cognitiva y administrativa (Toil operations).* |

---

## 3. Análisis de Configuración y Código

### 3.1. Orquestación DNS Dinámica (Desacoplando el Ingress)

En entornos educativos o elásticos donde las Elastic IPs son limitadas, dependemos de IPs dinámicas. Para garantizar siempre la resolución del dominio frente a caídas o reinicios del balanceador, se ha diseñado este script de actualización persistente acoplado vía `cron`:

```bash
#!/bin/bash
# Update DuckDNS for ALB dynamically
ALB_DNS="Sentinel-ALB-891619477.us-east-1.elb.amazonaws.com"
IPS=$(nslookup $ALB_DNS | grep "Address" | awk '{print $2}')
WORKING_IP=$(echo $IPS | awk '{print $1}')
curl -s "https://www.duckdns.org/update?domains=$DOMAIN&token=$TOKEN&ip=$WORKING_IP"
```
*Justificación:* Esto transforma un entorno inestable de laboratorio en una arquitectura determinista y confiable a nivel de red pública.

### 3.2. Despliegue Elástico del Sistema de Observabilidad

El stack Docker que procesa las métricas es agnóstico del direccionamiento gracias al uso de inyección de configuración temporal pre-reinicio (`start.sh`):

```bash
#!/bin/bash
# 1. Cargar variables del entorno .env
export $(grep -v '^#' .env | xargs)

# 2. Generar prometheus.yml inyectando la topología actual
envsubst < prometheus/prometheus.yml.template > prometheus/prometheus.yml

# 3. Levantar stack
docker compose up -d
```
*Justificación:* Permite reciclar o destruir servidores EC2 sin afectar el pipeline de CI/CD del clúster de monitorización; la infraestructura se declara en el `.env` y el stack se auto-adapta en cada *restart*.

### 3.3. Políticas de Hardening (Capa de Aplicación)
Se han aplicado severas restricciones al stack LAMP:
*   **Permisos base:** `chown -R ubuntu:www-data`, `chmod 755` para directorios, `chmod 644` para ficheros.
*   **Aseguramiento Web:** Ocultación de tokens y firmas (`ServerTokens Prod`, `ServerSignature Off`), mitigaciones contra XSS y Clickjacking incorporadas nativamente en la configuración de Apache.

---

## 4. Capa de Seguridad y Respuesta Autómata (SIEM)

En vez de una monitorización pasiva, SENTINEL aplica un modelo predictivo-reactivo usando **Wazuh SIEM**. 

*   **Mitigación de OWASP Top 10:** Monitorización de trazas e intrusiones de red mediante análisis en tiempo real de los logs de Apache y autenticación de sistemas (SQLi, LFI, Código arbitrario, Path Traversal).
*   **Active Response (IPS - Sistema de Prevención de Intrusiones):** Flujo de seguridad automatizado de tres fases sin intervención humana (Zero-touch mitigation):
    1.  *Detección:* El agente de Wazuh detecta un patrón anómalo de ataques masivos, p.ej. Fuerza Bruta SSH / HTTP.
    2.  *Análisis:* El Manager coteja la severidad con sus reglas de correlación e hilos de MITRE ATT&CK.
    3.  *Ejecución:* Envío de orden de remediación (`firewall-drop`) contra la IP atacante, generando bloqueos por `IPTABLES` directamente en el nodo Edge.

> [!WARNING]
> **Gestión de Security Groups**
> Para que el flujo de remediación y monitoreo sea resiliente, el mapeo de **Security Groups** es fundamental. Puerto `1514/TCP/UDP` y `1515/TCP` abiertos explícitamente y delimitados para que Wazuh interactúe únicamente entre la capa privada de computación.

---

## 5. Telemetría, Métricas y Alerting

> [!TIP]
> **Resolución de Alta Concurrencia**
> Durante los tests de carga controlada (*Apache Benchmark*: `ab -n 5000 -c 50`), la infraestructura mantuvo un rendimiento constante de **~60 Requests Per Second (RPS)** con una saturación de CPU menor al 2%. La topología basada en **AWS ALB** absorbió el tráfico de forma efectiva, previniendo congestiones del servicio web interno.

El vector de **Observabilidad (WPG)** procesa los datos en tiempo real:
*   **Prometheus** recolecta series temporales del host (`node_exporter`) y CloudWatch (`yace`).
*   **Grafana** transforma la telemetría métrica en dashboards ejecutivos.

La proactividad recae sobre el **Alertmanager**. Ante cualquier pérdida de "Health status" (`up == 0`), degradación inusual de conectividad o limitación de almacenamiento, se interceptan las criticidades y se cursan mediante un Bot de **Telegram**. Esto asegura una **drástica reducción del MTTR (Mean Time to Repair)** para operaciones 24/7.

---

## 6. Disaster Recovery: Almacenamiento Inmutable Segregado

Se confía la estrategia de Continuidad de Negocio a volcados lógicos sobre **Amazon S3** (`tfg-sentinel-backups-alex`). Un pipeline de copias automatizadas vuelca los `data-dumps` en formatos ligeros (`.sql.gz`).

*   **Justificación Arquitectónica:** Aislar por completo la persistencia del estado en un servicio S3 prevé corrupciones sistémicas o *Ransomware* en bloque de recursos computacionales EC2. Respalda la capa TIER-Data cumpliendo el estándar de industria de alta durabilidad AWS (`99.999999999%`).

---

## 7. Evolución de Arquitectura: Migración a Terraform (IaC)

> [!NOTE]
> **Roadmap Futuro (Fase 6)**: Actualmente, la instanciación de red y computación se ha desplegado y provisionado combinando bash scripting y la consola de AWS (Click-Ops). La inmediata evolución técnica del proyecto contempla migrar todo el ciclo de vida a **Infraestructura como Código (IaC)** usando HashiCorp Terraform.

Adoptar Terraform eliminará la configuración manual, permitirá el control de versiones de la arquitectura y facilitará la replicabilidad casi instantánea de entornos (Dev/Staging/Prod).

A continuación se muestra un **ejemplo (Draft)** de cómo se estructurará la creación del recurso *Web Node* y el *Security Group* del ALB en el futuro repositorio de estado:

```hcl
# main.tf (Draft - Sentinel Future Iteration)
resource "aws_security_group" "alb_sg" {
  name        = "sentinel-alb-sg"
  description = "Permite trafico HTTPS entrante de usuarios"
  vpc_id      = aws_vpc.sentinel_vpc.id

  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

resource "aws_instance" "web_node" {
  ami           = "ami-0c55b159cbfafe1f0" # Amazon Linux 2023
  instance_type = "t3.micro"
  subnet_id     = aws_subnet.private_subnet.id
  
  # Aprovisionamiento automatizado via Cloud-init
  user_data = file("infrastructure/automation/scripts/provision_web.sh")

  tags = {
    Name = "Web-Node-1"
    Role = "Application"
  }
}
```

---
*Desplegado y orquestado con estándares empresariales en 2026. Proyecto defendido para Ciclo Superior ASIR por Alex Vidal Ródenas.*
