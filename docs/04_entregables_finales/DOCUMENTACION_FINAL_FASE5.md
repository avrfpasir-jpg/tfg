# 🚀 Informe de Ejecución y Validación Final - Fase 5
**Proyecto:** SENTINEL (Infraestructura Web Segura)  
**Fecha:** 28 de abril de 2026  
**Responsable:** Alex Vidal Ródenas  

---

## 1. Inventario Final de Infraestructura (As-Built)

Tras la consolidación del despliegue en AWS, la infraestructura operativa se compone de los siguientes elementos:

| Componente | Rol | IP Privada | IP Pública / DNS | Estado |
| :--- | :--- | :--- | :--- | :--- |
| **Sentinel-ALB** | Balanceador L7 | N/A | `psicopompo.duckdns.org` | ✅ Operativo (HTTP/HTTPS) |
| **Web-Node-1** | Apache/PHP 8.5 | `10.0.1.250` | `13.220.133.172` | ✅ Operativo |
| **Sentinel-Monitoring** | Grafana/Prometheus | `10.0.1.233` | `statuspsicopompo.duckdns.org` | ✅ Operativo |
| **Sentinel-DB (RDS)** | MariaDB (Multi-AZ) | `10.0.0.242` | `database-sentinel...` | ✅ Operativo |
| **Wazuh-Manager** | SIEM / Seguridad | `10.0.1.170` | `54.145.224.168` | ✅ Operativo |

---

## 2. Resultados de la Validación Técnica

### 2.1 Conectividad y Resolución DNS
Se ha validado la resolución externa de los dominios DuckDNS vinculados a la infraestructura:
*   **Dominio Principal:** `psicopompo.duckdns.org` -> Resuelve correctamente a las IPs dinámicas del ALB de AWS (`100.26.73.248`).
*   **Dominio de Estado:** `statuspsicopompo.duckdns.org` -> Resuelve correctamente a la IP fija del servidor de monitorización (`3.95.206.104`).

### 2.2 Validación del Stack de Monitorización (Fix Aplicado)
Se detectó un fallo crítico en el despliegue inicial donde el contenedor `alertmanager` no podía arrancar por falta de interpolación de variables de entorno.
*   **Acción:** Se implementó un sistema de plantillas (`.template`) y se actualizó `start.sh` para usar `envsubst`.
*   **Estado Final:** **UP**. Grafana muestra métricas en tiempo real de CPU, RAM y tráfico de Apache procedentes de los nodos.

### 2.3 Validación de la Capa de Datos
*   El servidor web se conecta exitosamente al endpoint de **Amazon RDS** utilizando el driver PDO y las credenciales seguras almacenadas en `secrets/`.
*   Se ha verificado la integridad de las tablas y la capacidad de servir productos dinámicamente en la tienda.

### 2.4 Validación SSL/TLS (Seguridad en Tránsito)
Se ha corregido el problema del Listener del ALB. 
*   **Acción:** Se ha vinculado correctamente el certificado ACM (Amazon Certificate Manager) y se ha forzado la redirección de HTTP (80) a HTTPS (443).
*   **Resultado:** Conexión segura validada con cifrado TLS 1.3.

### 2.5 Validación del SIEM (Wazuh)
El nodo manager ya es alcanzable y está procesando eventos de los agentes.
*   **Acción:** Se corrigió la regla del Security Group que bloqueaba el tráfico entrante al puerto 1514/1515.
*   **Resultado:** Detección de intrusiones operativa y visualización confirmada en el dashboard de Grafana.

---

## 3. Control de Incidencias y Mejoras Pendientes

## 3. Cierre de Incidencias y Mejoras Realizadas

### 3.1 Resolución: Wazuh SIEM Online
La instancia ha sido reiniciada y se ha verificado que la IP pública es persistente. Los agentes han establecido conexión de forma satisfactoria.

### 3.2 Resolución: Configuración HTTPS
El ALB ya sirve contenido seguro. Los Health Checks en el puerto 443 pasan a estado "Healthy", permitiendo el flujo de tráfico cifrado de extremo a extremo.

---

## 4. Conclusión de la Fase 5
La infraestructura se encuentra en un estado funcional de **"Lanzamiento Consolidado" (100% completado)**. Todos los servicios críticos (Web, DB, Monitorización y Seguridad) están plenamente operativos, validados y documentados para la entrega final del proyecto.

---
**Firmado:**  
Alex Vidal Ródenas  
*Arquitecto de Sistemas Cloud*
