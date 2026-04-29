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
- [x] **Observabilidad Centralizada:** Métricas de CloudWatch (RDS/ALB) y Alertas SIEM en Grafana.
- [x] **Alerting Activo:** Notificaciones automáticas a Telegram integradas.

## 📋 Backlog de Tareas (Próxima Sesión)
1.  **🛠️ Recubrimiento Post-Estrés:** Reiniciar `Apache` en `10.0.1.250` (usando `ec2-user`).
2.  **🛡️ Hardening Final:** Revisar permisos de llaves en el nodo de monitoreo.
3.  **📸 Captura de Defensa:** Generar 30 mins de tráfico limpio para métricas finales.

## 🚀 Futuro (Post-Hito)
1.  **Automatización IaC:** Terraform para despliegue "One-Click".
2.  **Hardening de Secretos:** AWS Secrets Manager para credenciales.
