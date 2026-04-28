# 🧠 Registro de Decisiones Clave (Architecture Decision Records)

## 01. Arquitectura Multi-Tier en AWS
*   **Decisión:** Aislar la Base de Datos y el Servidor Web en subredes privadas.
*   **Razón:** Minimizar la superficie de ataque. Solo el balanceador (ALB) es visible desde internet.
*   **Consecuencia:** Requiere el uso de una instancia NAT o Proxy Squid para actualizaciones de sistema.

## 02. Adopción de Wazuh como SIEM
*   **Decisión:** Usar Wazuh en lugar de solo logs estándar o Fail2Ban.
*   **Razón:** Necesitamos cumplimiento normativo (GDPR/PCI-DSS) y respuesta activa centralizada que mapee con MITRE ATT&CK.

## 03. Migración de HAProxy (Prototipo) a AWS ALB (Producción)
*   **Decisión:** El balanceador definitivo es el Application Load Balancer de AWS.
*   **Razón:** Facilita la gestión de certificados SSL vía ACM y permite el escalado horizontal nativo de AWS.

## 04. Estrategia de Backup Off-site (S3)
*   **Decisión:** Volcados SQL diarios enviados a un Bucket de S3 fuera de la zona de disponibilidad principal.
*   **Razón:** Garantizar que, ante un fallo catastrófico de la infraestructura de cómputo, los datos (el activo más valioso) permanezcan intactos.

## 05. Uso de PHP 8.2 y Hardening de Aplicación
*   **Decisión:** Implementar validación estricta de tipos MIME y "Soft Delete".
*   **Razón:** Prevenir ataques RCE (Remote Code Execution) por subida de archivos y mantener la integridad referencial en la base de datos.
