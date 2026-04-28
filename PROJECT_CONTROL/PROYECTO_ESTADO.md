# 🚀 Estado del Proyecto: SENTINEL

## 📝 Objetivo Principal
Desplegar una **Infraestructura Empresarial Segura y Replicable** (Secure Landing Zone) en AWS para alojar aplicaciones e-commerce críticas. El enfoque no es solo la funcionalidad, sino la **resiliencia, observabilidad y autodefensa**.

## 🛠️ Stack Tecnológico Actual
*   **Cloud (IaaS):** AWS (VPC, EC2, S3, ALB, RDS MariaDB).
*   **Servicios Core:** Apache 2.4, PHP 8.2, MariaDB 10.5.
*   **Seguridad:** Wazuh SIEM (HIDS), Active Response, SSL/TLS (Let's Encrypt/ACM).
*   **Observabilidad:** Stack WPG (Wazuh, Prometheus, Grafana).
*   **Sistemas:** Amazon Linux 2023 / Ubuntu 24.04.

## ✅ Funcionalidades Implementadas
- [x] **Arquitectura Multi-Tier:** Segmentación estricta entre subredes públicas y privadas.
- [x] **Seguridad Activa:** Ciclo de detección y bloqueo automático de ataques SSH (Active Response).
- [x] **Balanceo de Carga:** SSL Termination configurado en AWS ALB con redirección forzada a HTTPS.
- [x] **Plan de Disaster Recovery:** Backups automatizados hacia AWS S3 con simulacro de restauración validado.
- [x] **Observabilidad Centralizada:** Métricas de CPU/RAM, Tráfico Web y Alertas SIEM en un único panel de Grafana.

## 📋 Backlog de Tareas Pendientes (Priorizado)
1.  **🚀 [PRIORIDAD ALTA] Automatización IaC:** Crear el esqueleto de Terraform para permitir el despliegue "One-Click".
2.  **🔒 [SEGURIDAD] Hardening de Secretos:** Migrar credenciales de BD de archivos `.php` a variables de entorno o AWS Secrets Manager.
3.  **📡 [REDES] Egress Control:** Refinar las reglas del Proxy Squid para auditoría total de tráfico saliente.
4.  **📈 [ESCALABILIDAD] Roadmap HA:** Documentar la configuración de Auto Scaling Groups para los nodos web.
