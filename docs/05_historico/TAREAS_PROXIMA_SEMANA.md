# 🚀 Plan de Cierre - Proyecto SENTINEL (Próxima Semana)

Este documento detalla las tareas técnicas para asegurar el proyecto y validar la arquitectura final, manteniendo una infraestructura costo-eficiente ("FinOps-first") pero con resiliencia garantizada.

## 📅 Hito 1: Cifrado y Exposición Segura (SSL)
**Objetivo:** Asegurar que todo el tráfico entrante al balanceador esté cifrado (HTTPS), indispensable para un e-commerce.

- [ ] **Configuración de Dominio:** Verificar la conexión del balanceador HAProxy con el dominio `psicopompo.duckdns.org` (o similar).
- [ ] **Despliegue de Let's Encrypt:** 
    - Generar el certificado SSL mediante Certbot.
    - Configurar la "Terminación SSL" (SSL Termination) en HAProxy para el puerto 443, enviando tráfico limpio (80) al backend.
- [ ] **Redirección HTTP a HTTPS:** Forzar todo el tráfico web a conexiones seguras.

## 📅 Hito 2: Automatización de Backups (Desastre y Recuperación)
**Objetivo:** Garantizar la persistencia de datos y compensar la falta de redundancia en la DB mediante backups off-site.

- [ ] **Backups a AWS S3:** 
    - Crear un bucket S3 privado y habilitar el versionado.
    - Crear un script Bash (`backup_db.sh`) que ejecute `mysqldump` con bloqueo de tablas temporal.
- [ ] **Programación (Cron):** Programar el script para que suba diariamente la copia de seguridad cifrada a S3.

## 📅 Hito 3: Gestión de Secretos (Optativo pero sumamente recomendado)
**Objetivo:** Mejorar la seguridad de la aplicación eliminando credenciales en código fuente.

- [ ] **AWS Secrets Manager:** 
    - Almacenar la contraseña de la base de datos de forma segura.
    - Modificar el archivo `conexiones.php` para recuperar la credencial usando la API de AWS.

## 📅 Hito 4: Pruebas de Rendimiento y Documentación Final
**Objetivo:** Validar la infraestructura actual y obtener los datos empíricos para la Memoria.

- [ ] **Test de Carga con Apache Benchmark (`ab`):**
    - Identificar IP pública de HAProxy.
    - Lanzar `ab -n 5000 -c 50 https://psicopompo.duckdns.org/index.php`.
    - Registrar peticiones por segundo (RPS) y latencia media para **sustituir obligatoriamente el marcador `[X]` en la sección 7.1 de la Memoria.**
- [ ] **Validación de la Respuesta Activa:** Comprobar si `firewall-drop` se activa correctamente en Wazuh ante ataques agresivos simulados.
- [ ] **Verificación en Grafana:** Capturar el pico de CPU y RAM del Servidor Web durante el test de estrés para los anexos de la memoria.

## 📅 Hito 5: Preparación de la Defensa (Los "Caza-Errores")
**Objetivo:** Blindar el argumentario técnico para evitar fisuras ante las preguntas "trampa" del tribunal.

- [ ] **Creación del Diagrama de Red (Imprescindible):** Dibujar la topología completa (Externa/Internet -> IGW -> HAProxy -> Web Bastion -> MariaDB Air-Gapped + Wazuh/Grafana). Se puede usar *Draw.io* o *Lucidchart*.
- [ ] **Argumentario de la Swap:** Interiorizar la respuesta ante la crítica del uso de memoria de intercambio en un SIEM: *"Es una medida de resiliencia de última instancia (fail-safe) para evitar que el OOM Killer del kernel termine el proceso del indexador de Wazuh ante un pico de logs imprevisto, priorizando la persistencia de la alerta de seguridad sobre la latencia de disco"*.
- [ ] **Cierre Final de la Memoria:** Revisión ortotipográfica y generación del PDF final.

---
**💡 Valoración Global del Proyecto:** La memoria demuestra una madurez técnica excepcional de nivel ASIR, integrando 5 pilares fundamentales: **Administración de Servidores (Debian/Linux), Redes y Seguridad (VPC, SG), Servicios e Infraestructura (HAProxy, MariaDB, PHP), Observabilidad (Wazuh, Grafana) y Continuidad de Negocio (Backups en S3)**.

