# 🚀 Contexto de Evolución: SENTINEL V2.0 (Alta Disponibilidad en la Nube)

Este documento define la hoja de ruta técnica para transformar la infraestructura actual de **SENTINEL (Single-Node)** en una arquitectura de **Alta Disponibilidad (Multi-AZ)** real utilizando servicios gestionados de Amazon Web Services (AWS) para la entrega final de Junio de 2026.

---

## 🏗️ 1. Visión del Proyecto (V1.0 vs V2.0)

Actualmente, **SENTINEL V1.0** es un prototipo funcional diseñado para demostrar habilidades de administración de sistemas manuales (HAProxy, MariaDB, Wazuh Agentes). 

La evolución hacia **V2.0** busca la resiliencia industrial eliminando todos los **Puntos Únicos de Fallo (SPOF)** detectados en el análisis crítico inicial.

| Componente | Estado Actual (V1.0) | Evolución Objetivo (V2.0) | Beneficio Clave |
| :--- | :--- | :--- | :--- |
| **Balanceador** | HAProxy (EC2 Single-Node) | **AWS Application Load Balancer (ALB)** | Redundancia nativa en múltiples AZ. |
| **Computación** | Servidor Web único (Apache) | **Auto Scaling Group (ASG)** | Escalabilidad horizontal automática. |
| **Base de Datos** | MariaDB 10.5 (EC2 Local) | **Amazon RDS (Multi-AZ)** | Replicación en tiempo real y backups automáticos. |
| **Certificados** | SSL vía Certbot/Let's Encrypt | **AWS Certificate Manager (ACM)** | Renovación automática sin intervención. |
| **Almacenamiento** | Disco Local (EBS) | **Amazon Elastic File System (EFS)** | Almacenamiento web compartido entre nodos. |

---

## 🛠️ 2. Fases de Implementación Técnica

### Fase 1: Datos y Resiliencia (RDS Multi-AZ) [COMPLETADA ✅]
Migrar la base de datos `tienda_segura` de la instancia EC2 actual a un clúster gestionado de RDS. 
*   **Acción:** Volcado SQL (`mysqldump`) -> Importación en RDS. Certificado de AWS descargado (`global-bundle.pem`) y cargado en el `config_sentinel_db.php`.
*   **Impacto:** RPO (Recovery Point Objective) reducido a milisegundos gracias a la replicación síncrona de AWS. El punto único de fallo de la base de datos ha quedado oficialmente eliminado.

### Fase 2: Capa de Computación y Shared Storage (ASG + EFS)
Preparar los servidores web para que sean "stateless" (sin estado).
*   **Acción:** Montar las carpetas de imágenes y código de la tienda en **Amazon EFS**. Esto permite que si se lanzan 5 servidores nuevos, todos vean los mismos productos y pedidos.
*   **Impacto:** Capacidad de respuesta elástica ante picos de tráfico inesperados.

### Fase 3: Gestión de Tráfico y Seguridad (ALB + ACM)
Reemplazar el nodo HAProxy por el balanceador nativo de AWS.
*   **Acción:** Configurar el ALB para distribuir el tráfico entre las instancias del ASG. Integrar el certificado SSL en el propio ALB.
*   **Impacto:** Eliminación total del SPOF en la entrada de tráfico.

---

## 🛡️ 3. Monitoreo y Seguridad (Sentinel 2.0)

El stack de monitoreo seguirá siendo el corazón del proyecto, pero se adaptará a la nueva escala:
1.  **Wazuh:** Cada nueva instancia que el ASG despliegue tendrá el agente Wazuh preinstalado, registrándose automáticamente en el SIEM.
2.  **Prometheus/Grafana:** Se monitorearán métricas del **Cloudwatch** del ALB y el RDS para tener una visión holística de la salud de la nube.
3.  **Seguridad de Red:** Se utilizarán **Security Groups** más estrictos, permitiendo tráfico HTTP/S solo desde el ALB hacia las instancias privadas.

---

## 🎯 4. Objetivos para Junio
*   **Defensa del TFG:** Demostrar cómo el sistema se auto-repara ante la caída de un nodo.
*   **Presupuesto:** Mantener la infraestructura dentro de los límites de **AWS Academy / Free Tier** siempre que sea posible.
*   **Entregable:** Un panel de Grafana final que muestre el tráfico balanceado y la salud de los múltiples nodos.

---
**Firmado:**
Álex Vidal Ródenas  
*Responsable de Infraestuctura e IA - Proyecto SENTINEL*
