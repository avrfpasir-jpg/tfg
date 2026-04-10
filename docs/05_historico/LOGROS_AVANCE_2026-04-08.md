# 📜 Resumen de Avances Técnicos - 08 de Abril de 2026

## 🎯 Objetivo de la Sesión
Sincronizar la documentación teórica con la realidad técnica del proyecto y consolidar la migración a servicios gestionados de AWS (RDS y ALB).

---

## 🛠️ Logros Técnicos Verificados (100% Real)

### 1. Validación Definitiva de RDS ✅
*   **Estado:** La base de datos ha dejado de ser un plan para ser la infraestructura de producción.
*   **Prueba de Conexión:** Se ejecutó con éxito el script `test_rds.php` desde el Servidor Web de AWS (`10.0.1.250`) usando conexión SSL/TLS obligatoria.
*   **Integridad de Datos:** Se ha verificado que el RDS Multi-AZ contiene actualmente **5 productos** y es capaz de servir la web de forma autónoma.
*   **Desmantelamiento del SPOF:** El nodo "Búnker" ha sido degradado a estado de backup histórico, eliminando la dependencia crítica del almacenamiento local.

### 2. Implementación de Alta Disponibilidad (ALB) y Seguridad Criptográfica 🏗️🛡️
Se han completado los pasos de configuración del **Application Load Balancer** en AWS Academy:
*   **AMI (Imagen Maestra):** Creada a partir del servidor web configurado para permitir el escalado horizontal.
*   **Target Group (Grupo de Destino):** Configurado con **Persistencia (Stickiness)** mediante cookies de AWS para garantizar que las sesiones de usuario (carritos de compra) sean consistentes.
*   **Debugging de Red (Resuelto):** Se identificó un bloqueo en el firewall interno del servidor (`firewalld`). Se eliminaron reglas restrictivas heredadas (SPOF IP `10.0.1.117`) y se habilitó el tráfico HTTP/HTTPS para el balanceador.
*   **Cifrado SSL/TLS (HTTPS):** Se rescató el certificado de Let's Encrypt del antiguo HAProxy y se importó con éxito a **AWS Certificate Manager (ACM)**. El balanceador ahora gestiona el cifrado (SSL Offloading) por el puerto 443.
*   **Dominio y Sincronización:** Se implementó un **script de actualización dinámica** (`update_duckdns.sh`) con ejecución vía `cron` para vincular automáticamente las IPs cambiantes del ALB con el dominio `psicopompo.duckdns.org`.
*   **Estado Final:** Sistema **Healthy** y accesible de forma segura en: **https://psicopompo.duckdns.org**

---

## 📂 Coherencia Documental
*   **Actualización de IPs:** Se ha saneado el archivo `conexiones.txt`, eliminando comandos SSH obsoletos y dejando solo las rutas de acceso reales y actualizadas.
*   **Sincronización de Roadmap:** Se ha validado que el Roadmap ahora sí refleja la realidad del sistema, cerrando la "Gran Incoherencia" entre lo que se decía y lo que había.
*   **Limpieza Técnica:** Se han eliminado los scripts de prueba temporales (`test_rds.php`) para mantener la higiene del entorno de producción.

---

## 🚩 Pendientes para la próxima sesión
*   Documentar el proceso de "Stickiness" como medida de seguridad y experiencia de usuario para la memoria final.
*   Realizar simulación final de caída de nodo web para verificar la resiliencia (Alta Disponibilidad).

**Firmado:**
*Álex Vidal - Responsable de Infraestructura e IA*  
*Proyecto SENTINEL V2.0*
