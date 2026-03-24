# MEMORIA FINAL DEL PROYECTO: SENTINEL (Tienda Segura)

## 1. Portada
*   **Nombre del proyecto:** SENTINEL - Infraestructura Web Segura y Monitorizada (Tienda Segura)
*   **Ciclo formativo:** Grado Superior en Administración de Sistemas Informáticos en Red (ASIR)
*   **Módulos involucrados:** Implantación de Sistemas Operativos, Planificación y Administración de Redes, Gestión de Bases de Datos, Seguridad y Alta Disponibilidad, Administración de Sistemas Gestores de Base de Datos, Servicios de Red e Internet.
*   **Integrantes del equipo:** Alex Vidal Ródenas

## 2. Agradecimientos
[Espacio reservado para los agradecimientos personales del autor]

## 3. Resumen
Este proyecto, denominado **SENTINEL**, consiste en el diseño, despliegue y aseguramiento de una infraestructura de comercio electrónico denominada "Tienda Segura". La solución integra un servidor web (Apache/PHP), una base de datos (MariaDB), un balanceador de carga (HAProxy) y un completo stack de monitorización y seguridad (Prometheus, Grafana, Loki y Wazuh) sobre una infraestructura en la nube (AWS). El objetivo principal es garantizar la alta disponibilidad, el rendimiento óptimo y la detección proactiva de incidentes de seguridad en un entorno de producción real.

## 4. Introducción
### 4.1. Contexto y justificación del proyecto
En el entorno actual, la seguridad y la disponibilidad de los servicios web son críticas para cualquier negocio de comercio electrónico. Un fallo en el sistema o una brecha de seguridad pueden suponer grandes pérdidas económicas y de reputación. Este proyecto nace de la necesidad de crear un entorno robusto que no solo sirva una aplicación web, sino que también se proteja activamente y se monitorice en tiempo real.

### 4.2. Objetivos del proyecto
*   Desplegar una arquitectura web escalable y segura en AWS.
*   Implementar un sistema de balanceo de carga para eliminar puntos únicos de fallo.
*   Establecer un stack de monitorización avanzada (Prometheus, Grafana, Loki).
*   Integrar un sistema de detección y respuesta ante incidentes (Wazuh SIEM).
*   Aplicar medidas de endurecimiento (Hardening) en todos los nodos.
*   Garantizar la identidad digital y el cifrado de comunicaciones (SSL/TLS).

### 4.3. Alcance y limitaciones del trabajo
*   **Alcance:** Incluye desde el diseño de la red en AWS (VPC, subredes, SG) hasta la implementación del frontend, backend, base de datos, balanceo, seguridad y monitorización.
*   **Limitaciones:** El proyecto se limita a recursos dentro de la capa gratuita o de bajo coste de AWS (instancias t2/t3). El desarrollo de la aplicación web se centra en la funcionalidad y seguridad, no en un diseño visual extremadamente complejo.

## 5. Análisis y contextualización de empresa/s del sector
### 5.1. Caracterización de empresas del sector
El sector de servicios gestionados de IT (MSP) y ciberseguridad está en constante crecimiento. Las empresas representativas suelen ser proveedoras de servicios cloud, consultoras de seguridad y empresas de infraestructura como servicio (IaaS). 
*   **Justificación:** La empresa seleccionada para el proyecto es una PyME ficticia de venta de componentes tecnológicos que requiere digitalizar sus ventas con las máximas garantías de seguridad.

### 5.2. Productos y servicios
*   **Tienda Segura:** Plataforma de e-commerce donde los usuarios pueden comprar productos tecnológicos.
*   **Servicio de Monitorización Sentinel:** Valor añadido que garantiza la supervisión de la plataforma 24/7.

### 5.3. Relación con los Objetivos de Desarrollo Sostenible (ODS)
*   **ODS 9 (Industria, Innovación e Infraestructura):** Fomento de infraestructuras resilientes y seguras.
*   **ODS 8 (Trabajo Decente y Crecimiento Económico):** Mejora de la productividad mediante la digitalización segura.

### 5.4. Identificación de los riesgos laborales en la empresa
Más allá de los riesgos genéricos de oficina, un entorno de administración de sistemas e infraestructura crítica presenta riesgos específicos:
*   **Gestión de Incidentes y Stress (Burnout):** El soporte 24/7 y la respuesta inmediata ante ataques o caídas de servicio suponen una carga mental elevada. Se propone la rotación de turnos (On-call) y protocolos de escalado claros.
*   **Alert Fatigue:** El exceso de falsos positivos en el SIEM puede generar desensibilización. Se planea el ajuste fino de reglas para mitiar este riesgo.
*   **Riesgos Eléctricos y Ergonomía en SOC:** Postura prolongada en el monitoreo de múltiples pantallas y manipulación de cableado en el centro de datos (CPD).
*   **Riesgos Biomecánicos:** Síndrome del túnel carpiano derivado del uso intensivo de teclado y ratón durante tareas de desarrollo y configuración.

## 6. Desarrollo del Proyecto
### 6.1. Metodología de trabajo
Se ha seguido una metodología ágil de despliegue por fases, aplicando el principio de **Defensa en Profundidad**:

1.  **Fase 1 (Infraestructura de Red):** Despliegue de la `SENTINEL-VPC` (10.0.0.0/16) con segmentación en subredes públicas y privadas para aislar la lógica de negocio.
2.  **Fase 2 (Seguridad Perimetral):** Implementación de **Security Groups** actuando como firewalls con estado, aplicando el principio de mínimo privilegio (ej. la base de datos solo acepta conexiones del SG-WEB).
3.  **Fase 3 (Cómputo y Datos):** Provisionamiento de instancias EC2 (`t2.micro`) y despliegue "Two-Tier". Se destaca el uso de la instancia web como **Bastion Host** para acceder a la base de datos en la subred privada.
4.  **Fase 4 (Monitorización y SIEM):** Integración de Wazuh (SIEM) para la detección de intrusiones y el stack de observabilidad (Prometheus/Grafana).

### 6.2. Temporalización del proyecto
Se ha registrado una desviación temporal del 15% respecto a la planificación inicial. Esta desviación se atribuye principalmente a la complejidad técnica de realizar una **instalación offline (Side-loading)** de paquetes en la subred privada. Esta decisión de diseño fue de carácter **"Security-First"**, priorizando el aislamiento absoluto del nodo de datos frente a la comodidad de las actualizaciones automáticas durante la fase de despliegue inicial (PoC).

### 6.3. Actividades realizadas
*   **Diseño de Red:** Creación de Internet Gateway (SENTINEL-IGW) y tablas de rutas específicas para la subred pública.
*   **Fortificación de la Capa de Datos:** 
    *   Instalación de MariaDB 10.5 en entorno aislado mediante transferencia manual de paquetes `.rpm` vía SCP.
    *   Configuración de usuarios con acceso restringido por segmento de red (`adminweb@10.0.1.%`).
*   **Implementación de Wazuh SIEM:**
    *   Despliegue del Manager en una instancia **t3.medium** (4GB RAM) para soportar el indexador y el panel de control.
    *   Configuración de una partición **Swap de 2GB** como medida de resiliencia adicional para evitar fallos del motor de indexación durante el análisis de picos de tráfico masivo.
    *   Monitorización activa de la integridad de archivos (FIM) en todos los nodos.
*   **Monitorización Avanzada:** Despliegue de exportadores (Node, Apache, MySQL) y unificación de métricas en un Dashboard centralizado en Grafana.
*   **Respuesta Activa:** Configuración de alertas y bloqueo automático de IPs tras detectar intentos de intrusión y ataques de fuerza bruta.

### 6.4. Recursos y tecnologías empleadas
*   **Enfoque IaaS (Infraestructura como Servicio):** Se ha evitado intencionadamente el uso de servicios gestionados (PaaS) nativos de AWS como Application Load Balancer (ALB) o Amazon RDS. Desplegar los servicios (HAProxy, MariaDB) manualmente sobre máquinas virtuales de propósito general permite demostrar las competencias de administración de sistemas e instalación offline (Side-loading) requeridas en el título de ASIR, ejerciendo un control granular sobre las configuraciones del SO y los agentes de seguridad.
*   **Cloud:** AWS (EC2 t2.micro para Web/DB/Balanceo, **t3.medium** para SIEM, VPC, IAM).
*   **SO:** Amazon Linux 2023 (Nodos Web y DB) / Ubuntu 24.04 LTS (Nodo SIEM).
*   **Servicios:** HAProxy, Apache, MariaDB, PHP.
*   **Contenedores:** Docker, Docker Compose.
*   **Monitorización:** Prometheus, Grafana, Loki.
*   **Seguridad:** Wazuh, firewalld/iptables, Fail2Ban, AWS Security Groups.

## 7. Resultados y Análisis
### 7.1. Análisis de los resultados y su impacto
*   **Capacidad de Respuesta:** Se ha validado mediante `Apache Benchmark` (ab) que el sistema procesa eficazmente una concurrencia de **50 usuarios**, alcanzando un rendimiento medio de **43.64 peticiones por segundo (RPS)**.
*   **Latencia y Rendimiento:** El **95% de las peticiones** se sirvieron en menos de **443 ms**, lo que garantiza una experiencia de usuario fluida incluso bajo carga moderada.
*   **Resolución de Problemas Técnicos (Troubleshooting):** Durante el desarrollo se resolvieron desafíos críticos, como la conectividad SSH hacia la subred privada y la instalación de software sin gateway de internet. Se ha detectado un pequeño porcentaje de error (6%) bajo estrés máximo, lo que justifica la implementación futura de mecanismos de auto-escalado horizontal.
*   **Detección de Amenazas:** El SIEM ha demostrado una eficacia muy alta en la detección de ataques de fuerza bruta y monitorización de integridad en tiempo real. Durante el test de estrés, Grafana registró picos de hasta **15 peticiones de Apache/sg** y **30 consultas SQL/sg**.

| Desafío Detectado | Solución Técnica Aplicada |
| :--- | :--- |
| **Permission denied (PEM)** | Aplicación de `chmod 400` para cumplir requisitos de seguridad de SSH. |
| **Timeout en subred privada** | Configuración de la instancia Web como Bastion y reglas de SG cruzadas. |
| **Instalación sin Internet** | Estrategia de **Side-loading** (descarga en Web y envío vía SCP a DB). |

*   **Auditoría Económica:** El coste real del despliegue en AWS tras un mes de uso intensivo es de **$2.29 USD**, lo que demuestra la viabilidad de una solución de alta seguridad con presupuesto mínimo.

## 8. Conclusiones y Recomendaciones
### 8.1. Conclusiones
El proyecto demuestra que es posible desplegar una infraestructura industrialmente viable con recursos limitados, siempre que se sigan buenas prácticas de seguridad y monitorización. Se ha logrado integrar de forma armoniosa servicios clásicos (LAMP) con stacks modernos de observabilidad y seguridad cloud.
### 8.2. Recomendaciones
Para futuros proyectos se sugiere el escalado horizontal dinámico (Auto-scaling groups) y la implementación de una CDN (Content Delivery Network).

### 8.3. Gestión de Contingencias y Continuidad de Negocio
Para mitigar los riesgos asociados a los puntos únicos de fallo (SPOF) en la arquitectura actual, se han definido los siguientes protocolos de contingencia:

| Escenario de Fallo | Impacto | Protocolo de Recuperación |
| :--- | :--- | :--- |
| **Caída de Nodo Web/DB** | Crítico | Restauración inmediata mediante **EBS Snapshots** (Backups de volumen). *Nota: Para la consistencia de la DB se requiere el bloqueo temporal de tablas (`FLUSH TABLES WITH READ LOCK`) antes del disparo del snapshot.* |
| **Corrupción de Datos** | Alto | Recuperación de base de datos desde dumps SQL preventivos almacenados fuera del nodo. |
| **Fallo de HAProxy** | Crítico | Reconfiguración de DNS para apuntar directamente al nodo Web (modo emergencia). |
| **Compromiso de Seguridad** | Alto | Aislamiento del nodo mediante Security Groups y análisis forense con logs de Wazuh. |
| **Saturación de Recursos** | Medio | Escalado vertical preventivo (cambio de tipo de instancia EC2). |

## 9. Bibliografía
[Listado de fuentes: Documentación de AWS, Grafana Labs, Wazuh documentation, PHP.net, etc.]

## 10. Anexos
1.  **Panel de Estadísticas de HAProxy:** Demostración del estado de salud de los nodos de backend. ([capturasfase4/Panel de Estadísticas de HAProxy.png])
2.  **Resultados de Apache Benchmark:** Captura de terminal con métricas de latencia y throughput. ([capturasfase4/Terminal con los resultados de ab.png])
3.  **Monitorización en Grafana:** Captura de picos de carga y alertas de seguridad visualizadas. ([capturasfase4/Pico en Grafana.png])
4.  **Auditoría de Costes:** Captura del dashboard de facturación de AWS. ([capturasfase4/costes1.png])

---

## 11. Trabajo Futuro y Líneas de Mejora
Dada la naturaleza evolutiva del proyecto SENTINEL y las limitaciones de presupuesto impuestas por el entorno de aprendizaje, se han identificado las siguientes áreas de mejora para una futura transición a un entorno de producción industrial:

### 11.1. Alta Disponibilidad (HA) Real
Para eliminar los puntos únicos de fallo (SPOF) detectados, se planea:
*   **Cluster de Balanceo:** Implementar un segundo nodo HAProxy sincronizado mediante **Keepalived** y una IP flotante (VIP).
*   **Replicación de Datos:** Sustituir la instancia única de MariaDB por un entorno de replicación **Master-Slave** o migrar a **Amazon Aurora** con Multi-AZ habilitado.

### 11.2. Automatización e Infraestructura como Código (IaC)
La configuración actual manual de AWS se optimizará mediante:
*   **Terraform:** Para definir la red, instancias y grupos de seguridad de forma declarativa.
*   **Ansible:** Para automatizar el aprovisionamiento de software y la gestión de configuraciones, eliminando la intervención manual en los servidores.

### 11.3. Seguridad Proactiva y Gestión de Parches
*   **IDPS de Red:** Integrar un sistema de detección y prevención de intrusiones a nivel de red como **Snort** o **Suricata** en la entrada de la VPC.
*   **Bastion & Proxy:** Implementar un **Proxy de aplicación** (Squid) para permitir actualizaciones seguras en la subred privada de forma controlada, eliminando definitivamente la dependencia del side-loading manual.
*   **Gestión de Identidades y Secretos:** Integrar **AWS Secrets Manager** para la rotación automática de las credenciales de MariaDB, eliminando el uso de contraseñas embebidas en los archivos `.php` del servidor.

### 11.4. Escalabilidad y Elasticidad (FinOps)
*   **Auto Scaling:** Configurar grupos de auto-escalado basados en métricas de CPU/RAM de Prometheus para añadir dinámicamente nodos web durante picos de carga.
*   **Content Delivery Network (CDN):** Implementar **CloudFront** para el almacenamiento en caché de contenido estático (imágenes de la tienda), reduciendo la carga en el servidor de origen.
