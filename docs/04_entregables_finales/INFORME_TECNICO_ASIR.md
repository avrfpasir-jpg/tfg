# 📑 Informe Técnico: SENTINEL y su Correspondencia con el Currículo ASIR

Este documento detalla la infraestructura técnica del proyecto **SENTINEL (Tienda Segura)** y justifica su implementación basándose en las competencias adquiridas en el Ciclo Formativo de Grado Superior en **Administración de Sistemas Informáticos en Red (ASIR)**.

---

## 1. Resumen Técnico del Proyecto
**SENTINEL** es una infraestructura IaaS (Infraestructura como Servicio) desplegada en **AWS** que aloja una aplicación e-commerce segura. La arquitectura sigue un modelo **Multi-Tier** (Multicapa) con segmentación estricta de red, monitorización avanzada y respuesta activa ante incidentes.

---

## 2. Correspondencia con los Módulos de ASIR

### 🌐 Planificación y Administración de Redes (PAR)
*   **Implementación:**
    *   Diseño de una **VPC (Virtual Private Cloud)** con direccionamiento `10.0.0.0/16`.
    *   Segmentación en **Subred Pública** (acceso exterior) y **Subred Privada** (aislamiento de datos).
    *   Configuración de **Tablas de Rutas**, **Internet Gateway** y flujo de tráfico controlado.
    *   Implementación de un **Bastion Host** facilitando el acceso administrativo seguro.
*   **Competencia:** Diseño de infraestructuras de red, gestión de protocolos TCP/IP y segmentación mediante VLANs (en este caso, subredes cloud).

### 🐧 Implantación de Sistemas Operativos (ISO)
*   **Implementación:**
    *   Despliegue y administración de instancias **Amazon Linux 2023** y **Ubuntu 24.04**.
    *   Gestión de procesos y servicios mediante **Systemd**.
    *   **Hardening del SO**: Desactivación de servicios innecesarios y optimización del kernel para rendimiento web.
*   **Competencia:** Instalación, configuración y mantenimiento de sistemas operativos propietarios y libres.

### 🛡️ Seguridad y Alta Disponibilidad (SAD)
*   **Implementación:**
    *   **Wazuh SIEM:** Sistema de detección de intrusiones con mapeo de técnicas **MITRE ATT&CK**.
    *   **Active Response:** Bloqueo automático de IPs atacantes mediante scripts integrados con **iptables**.
    *   **Alta Disponibilidad:** Uso de un **Application Load Balancer (ALB)** para distribuir equilibrar la carga y evitar puntos de fallo únicos.
    *   **Plan de Disaster Recovery:** Protocolo de restauración ante pérdidas totales.
*   **Competencia:** Aseguramiento de la infraestructura, gestión de la disponibilidad y respuesta ante incidentes.

### 🗄️ Gestión de Bases de Datos (GBD) / ASGBD
*   **Implementación:**
    *   Instalación y administración de **MariaDB 10.5**.
    *   Aplicación del **Principio de Mínimo Privilegio (PoLP)** en usuarios de BD (`sentinel_web@10.0.1.%`).
    *   **Políticas de Backup**: Automatización de dumps SQL y exportación cifrada.
*   **Competencia:** Diseño, implantación y administración de sistemas gestores de bases de datos.

### 💻 Implantación de Aplicaciones Web (IAW)
*   **Implementación:**
    *   Configuración del **Stack LAMP** (Linux, Apache, MariaDB, PHP 8.2).
    *   Optimización del servidor **Apache** para servir la tienda "Psicopompo".
    *   Aseguramiento del código PHP contra vulnerabilidades **OWASP Top 10** (SQLi, XSS, RCE).
*   **Competencia:** Instalación y configuración de servidores de aplicaciones y CMS.

### 🌍 Servicios de Red e Internet (SRI)
*   **Implementación:**
    *   **DNS Dinámico:** Uso de **DuckDNS** para la resolución de dominios públicos.
    *   **Protocolos Seguros:** Certificados **SSL/TLS (HTTPS)** mediante **Let's Encrypt** gestionados en el balanceador.
    *   **Proxy/NAT:** Configuración de un Proxy Squid para permitir actualizaciones seguras en la subred aislada.
*   **Competencia:** Configuración de servicios de resolución de nombres, transferencia de archivos y correo.

### 🛠️ Administración de Sistemas Operativos (ASO)
*   **Implementación:**
    *   **Automatización:** Scripts en **Bash** para la gestión de copias de seguridad y auditoría de logs.
    *   **Programación de tareas:** Uso de **Systemd Timers** para ejecutar procesos periódicos de mantenimiento hacia **AWS S3**.
*   **Competencia:** Automatización de tareas administrativas mediante lenguajes de script.

---

## 3. Stack Tecnológico Unificado (WPG)
El proyecto destaca por la integración del llamado **Stack WPG**, que une tres pilares fundamentales de la administración moderna de sistemas:
1.  **Wazuh:** Seguridad y cumplimiento normativa.
2.  **Prometheus:** Recolección de métricas de infraestructura y aplicaciones.
3.  **Grafana:** Visualización centralizada y observabilidad.

---

## 4. Conclusión
El proyecto **SENTINEL** no es solo una demostración de habilidades técnicas aisladas, sino una síntesis práctica de todos los conocimientos adquiridos en el ciclo **ASIR**. Demuestra la capacidad de administrar un centro de datos virtualizado (Cloud), asegurando que los activos de información sean **Íntegros, Disponibles y Confidenciales**.

---
**Elaborado por:** Álex Vidal Ródenas
**Fecha:** 28 de abril de 2026
**Ubicación:** `docs/04_entregables_finales/INFORME_TECNICO_ASIR.md`
