# 🛠️ Stack Tecnológico Oficial - SENTINEL

Este documento consolida el inventario completo de herramientas, servicios, lenguajes y plataformas utilizados para el desarrollo, despliegue y securización de la infraestructura **SENTINEL**.

## ☁️ Capa 1: Proveedor Cloud (IaaS) y Redes
| Tecnología | Uso en el Proyecto | Justificación |
| :--- | :--- | :--- |
| **Amazon Web Services (AWS)** | Proveedor de nube pública principal. | Entorno base para el despliegue del IaaS. |
| **AWS VPC (Virtual Private Cloud)** | Orquestación de red y segmentación (`10.0.0.0/16`). | Aislamiento lógico de recursos (Zero Trust). |
| **Squid Proxy (NAT Proxy)** | Pasarela segura de salida a Internet para la subred privada. | Permite actualizar el SO sin asignar IPs públicas a los nodos internos. |
| **AWS EC2 (Elastic Compute Cloud)**| Servidores virtuales (Web, Monitoring, Wazuh). | Computación a medida con SO Linux. |
| **AWS ALB (Application Load Balancer)**| Balanceador de Carga Capa 7. | Absorción de tráfico web y mitigación SPOF. |
| **AWS ACM (Certificate Manager)** | Alojamiento y validación de certificados SSL/TLS. | Cifrado en tránsito (HTTPS). |
| **DuckDNS** | Proveedor de DNS dinámico gratuito. | Resolución de las IPs dinámicas del ALB de AWS (`psicopompo.duckdns.org`). |

## 📦 Capa 2: Almacenamiento y Bases de Datos
| Tecnología | Uso en el Proyecto | Justificación |
| :--- | :--- | :--- |
| **Amazon RDS (Relational Database Service)** | Servidor de base de datos administrado. | Escalabilidad, parches automáticos y Multi-AZ. |
| **MariaDB (Motor de RDS)** | Sistema de gestión de bases de datos relacionales. | Rendimiento óptimo para CMS y E-commerce. |
| **Amazon S3 (Simple Storage Service)** | Bucket inmutable para copias de seguridad (`.sql.gz`). | Disaster Recovery y alta durabilidad (99.999% de los datos). |

## 💻 Capa 3: Servidor Web y Lógica de Negocio
| Tecnología | Uso en el Proyecto | Justificación |
| :--- | :--- | :--- |
| **Amazon Linux 2023** | Sistema Operativo del nodo Edge (Application). | Rendimiento optimizado dentro del ecosistema AWS. |
| **Apache HTTP Server (httpd)** | Servidor Web de la tienda virtual. | Flexibilidad de configuración de cabeceras de seguridad y Mime-Types. |
| **PHP 8.2** | Lenguaje backend de la tienda. | Rapidez de procesamiento. |

## 👁️ Capa 4: Observabilidad y Telemetría (Stack WPG)
| Tecnología | Uso en el Proyecto | Justificación |
| :--- | :--- | :--- |
| **Docker Engine & Docker Compose** | Contenerización del entorno de monitorización. | Despliegue elástico, limpio y versionable del Stack. |
| **Prometheus** | Base de datos de series temporales (TSDB). | Recolección de métricas de todos los nodos. |
| **Node Exporter & Apache Exporter** | Agentes recolectores locales. | Extracción de telemetría de CPU, RAM y peticiones web de las EC2. |
| **YACE (CloudWatch Exporter)** | Adaptador Prometheus-AWS. | Ingesta de métricas nativas de AWS RDS y ALB hacia Prometheus. |
| **Grafana** | Dashboard y panel visual interactivo. | Visualización ejecutiva del estado de salud del clúster. |
| **Loki + Promtail** | Agregación de logs del sistema. | Concentración de los access logs y error logs en un solo punto. |

## 🛡️ Capa 5: Ciberseguridad y Alertas (SOC)
| Tecnología | Uso en el Proyecto | Justificación |
| :--- | :--- | :--- |
| **Wazuh (Manager + Indexer)** | SIEM e IDS/IPS basado en host. | Detección de intrusiones corporativa y Open Source. |
| **Wazuh Agent** | Instalado en Web Node y DB. | Envío de trazas analíticas y logs en tiempo real hacia el Manager. |
| **IPTABLES / UFW** | Firewall a nivel de Sistema Operativo. | Brazo ejecutor del "Active Response" de Wazuh para bloquear IPs hostiles. |
| **Alertmanager** | Enrutador de alarmas anexo a Prometheus. | Agrupación de métricas críticas y supresión de *Alert Fatigue*. |
| **Telegram Bot API** | Canal de Notificación End-to-End. | Recepción asíncrona e inmediata de incidentes (MTTR minimizado). |

## ⚙️ Capa 6: Automatización y Herramientas Locales
| Tecnología | Uso en el Proyecto | Justificación |
| :--- | :--- | :--- |
| **Bash Scripting** | Scripts de aprovisionamiento (`start.sh`, duckdns cron). | Automatización de tareas repetitivas en Linux. |
| **envsubst (Gettext)** | Inyección de plantillas de configuración. | Desacoplamiento de IPs estáticas en configuraciones YAML de Docker. |
| **Apache Benchmark (ab)** | Herramienta de pruebas de estrés (CLI). | Comprobación empírica de concurrencia y resiliencia del ALB. |

---
*Este documento resume las tecnologías que han permitido implementar la arquitectura Zero Trust y Alta Disponibilidad del proyecto SENTINEL en abril de 2026.*
