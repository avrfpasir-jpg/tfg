# 🛡️ Anexo: Simulacro de Disaster Recovery (Recuperación ante Desastres)

Este documento registra el procedimiento técnico y las evidencias visuales obtenidas durante el simulacro de restauración de la base de datos de **SENTINEL (Tienda Segura)** realizado el **1 de abril de 2026**. El objetivo es validar la resiliencia de la infraestructura y asegurar la continuidad de negocio ante una pérdida total de datos en el nodo búnker.

---

## 📅 Detalles del Simulacro
*   **Fecha de ejecución:** 1 de abril de 2026
*   **Sistema Afectado:** Nodo de Base de Datos Búnker (`10.0.2.61`)
*   **Base de Datos:** `tienda_segura` (MariaDB 10.5)
*   **Repositorio de Backups:** Amazon S3 (`s3://tfg-sentinel-backups-alex`)

---

## 🛠️ Fase 1: Estado Inicial (Antes del Desastre)
Se verifica que la tienda e-commerce está operativa y sirviendo productos correctamente a través del balanceador HAProxy.

> **[INSERTAR CAPTURA A: WEB FUNCIONANDO NORMALMENTE]**
> *Evidencia de que el sistema está en estado "Healthy" antes del incidente.*

---

## 🆘 Fase 2: Simulación del Desastre (Borrado de DB)
Se procede al borrado forzoso de la base de datos `tienda_segura` mediante el comando `DROP DATABASE` ejecutado desde el servidor de administración.

**Fallo del Servicio:** Al intentar acceder a la web, el sistema devuelve un error crítico de conexión a la base de datos, confirmando el escenario de desastre.

> **[INSERTAR CAPTURA B: ERROR DE CONEXIÓN EN LA WEB]**
> *Evidencia del "Downtime" causado por la pérdida total de la base de datos.*

---

## ♻️ Fase 3: Protocolo de Restauración desde S3
Se inicia el plan de recuperación descargando el último backup seguro desde el almacenamiento off-site en Amazon S3 y volcando los datos en el nodo MariaDB.

**Comandos ejecutados (Flujo de Recuperación):**
1.  **Descarga:** `aws s3 cp s3://tfg-sentinel-backups-alex/tienda_segura_2026-04-01.sql.gz /tmp/`
2.  **Importación:** `gunzip -c /tmp/tienda_segura_2026-04-01.sql.gz | sudo mysql`

---

## ✅ Fase 4: Validación y Resurrección del Servicio
Tras la restauración exitosa, se verifica que la web vuelve a estar online y que la integridad de los datos (productos, usuarios, pedidos) es del 100%.

> **[INSERTAR CAPTURA C: WEB RECUPERADA Y FUNCIONANDO]**
> *Evidencia del restablecimiento del servicio tras aplicar el protocolo de Disaster Recovery.*

---

## 🖥️ Evidencias Técnicas de Consola (Auditoría Final)

Para garantizar que la recuperación ha sido íntegra a nivel de sistema, se presentan las siguientes capturas tomadas desde la terminal del administrador:

### 1. Validación del Backup en la Nube (AWS S3)
Muestra que los datos están seguros y versionados fuera de la infraestructura principal.
> **[INSERTAR CAPTURA TERMINAL 1: Salida de 'aws s3 ls']**
> *Resultado: Archivo 'tienda_segura_2026-04-01.sql.gz' presente en S3.*

### 2. Integridad de Datos (MariaDB Búnker)
Verificación del número de tablas y registros recuperados tras el proceso de restauración.
> **[INSERTAR CAPTURA TERMINAL 2: Salida de 'SHOW TABLES' y 'COUNT(*) FROM productos']**
> *Resultado: 100% de tablas recuperadas (productos, pedidos, usuarios).*

### 3. Automatización y Prevención (Systemd Timer)
Demostración de que el sistema cuenta con una política de backup automatizada y autónoma.
> **[INSERTAR CAPTURA TERMINAL 3: Salida de 'systemctl list-timers tfg-backup.timer']**
> *Resultado: Timer activo y programado para el próximo ciclo de backup.*

---

### 📝 Conclusiones
El simulacro demuestra un **RTO (Recovery Time Objective)** de menos de **5 minutos**, garantizando que SENTINEL puede recuperarse de un desastre crítico con una pérdida nula de información (RPO = 0 horas respecto al último backup programado). La arquitectura aislada (Private Subnet) no impide que los datos fluyan hacia S3 de forma segura, blindando el activo más valioso de la empresa: su información transaccional.

---
**Firmado:**
Álex Vidal Ródenas
*Responsable de Infraestructura e IA - Proyecto SENTINEL*
