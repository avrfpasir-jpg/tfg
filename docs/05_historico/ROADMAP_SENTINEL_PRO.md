# ROADMAP SENTINEL: Hacia la Excelencia Profesional (TFG ASIR)

Este listado detalla las tareas necesarias para elevar el proyecto SENTINEL de un entorno de laboratorio a una infraestructura de nivel industrial blindada ante tribunales expertos.

---

## 🔐 Fase 1: Seguridad de Acceso y Datos (Prioridad Alta)
*   [x] **Segimentación de Privilegios en DB:** Crear usuario `sentinel_web` con permisos restringidos (SELECT, INSERT, UPDATE) solo sobre la base de datos de la tienda.
*   [x] **Hardening de MariaDB:** Eliminar la base de datos `test`, deshabilitar acceso remoto de root y aplicar `mysql_secure_installation`.
*   [x] **Cifrado de Secretos:** Migrar las contraseñas de los archivos `.php` a variables de entorno o a un archivo de configuración fuera de la raíz web (o usar AWS Secrets Manager si el presupuesto lo permite).
*   [x] **SSL End-to-End:** Configurar el cifrado TLS entre el HAProxy y el Servidor Apache (puerto 443 interno) para evitar sniffing en tránsito dentro de la VPC.

---

## 📡 Fase 2: Conectividad y Mantenimiento (Prioridad Media)
*   [x] **Implementación de NAT:** Desplegar una **NAT Instance** (usando la máquina web) en la subred pública para permitir que la base de datos descargue parches de seguridad de forma controlada.
*   [x] **Gestión Remota Segura:** Configurar **AWS Systems Manager (SSM)** para acceder a las instancias públicas sin puerto 22. *(Nota: En AWS Academy, SSM puede fallar en subredes privadas por bloqueos del LabRole)*.
*   [ ] **Bastion Host / Salto SSH:** Usar la máquina web como "Bastion Host" para acceder por SSH a la base de datos, en lugar de intentar forzar el SSM bloqueado por Academy.

---

## 💾 Fase 3: Resiliencia y Continuidad (Prioridad Media)
*   [ ] **Optimización de Backups:** Refinar el script de S3 con el flag `--single-transaction` y añadir rotación de copias (borrar antiguos).
*   [ ] **Protocolo de Restauración:** Documentar y realizar al menos una prueba de restauración completa (Disaster Recovery Plan) y guardarla como anexo.
*   [ ] **JVM Optimization:** Ajustar el Heap Size de OpenSearch (indexador de Wazuh) a un máximo de 1GB en `jvm.options` para evitar el colapso de la RAM.
*   [ ] **Index Management (ISM):** Configurar políticas de borrado automático para índices de Wazuh con más de 5 días de antigüedad para liberar IOPS y espacio en disco.
*   [ ] **Filtrado de Nivel de Eventos:** Configurar los agentes Wazuh para reportar solo eventos de nivel > 5, reduciendo el ruido y la carga del manager durante picos de tráfico.
*   [ ] **Programación de Escaneos:** Ajustar el `vulnerability-detector` para que los escaneos de vulnerabilidades ocurran solo en horas de baja actividad (ej. 4:00 AM).

---

## 🚀 Fase 4: Alta Disponibilidad y Escalabilidad (Prioridad Opcional)
*   [ ] **Clúster HAProxy:** Investigar e implementar (si es posible en AWS Academy) el uso de **Keepalived** para redundancia de balanceadores.
*   [ ] **Auto-scaling Group:** Definir una AMI del servidor web y configurar un grupo de auto-escalado basado en la carga detectada por Prometheus.
*   [ ] **Content Delivery Network (CDN):** Usar **AWS CloudFront** para servir las imágenes de los productos desde el borde (Edge locations).

---

## 📑 Reorientación de la Memoria (ensayo1.pdf)

Para que el documento pase de ser un borrador a una memoria final profesional, es necesario reenfocar varios apartados:

*   **Pivote en la Sección de Redes (Fase 1-2):** En lugar de presentar el "side-loading" manual como la solución final, preséntalo como el "método de despliegue inicial" que luego fue evolucionado a un entorno gestionado mediante **Proxy/NAT** para mayor seguridad.
*   **Sección de Base de Datos:** Debe aparecer explícitamente el **Principio de Mínimo Privilegio**. No menciones solo que MariaDB está aislada; menciona los permisos del usuario `sentinel_web`.
*   **Definición de Alta Disponibilidad:** Cambiar la narrativa. En lugar de decir "el HAProxy elimina el SPOF", decir "el HAProxy introduce la **lógica de balanceo** necesaria para la redundancia, identificando el propio balanceador como el siguiente ítem a Clusterizar (SPOF residual documentado)".
*   **Apartado de Backups:** Eliminar cualquier duda sobre la funcionalidad de S3. Presentar los dumps de SQL con `--single-transaction` como la garantía de integridad referencial definitiva.

---

## 📸 Capturas de Pantalla Necesarias (Evidencias Técnicas)

Añade estas capturas en las secciones indicadas para "blindar" la memoria:

| Captura de Pantalla | Sección en Memoria | Ubicación Recomendada |
| :--- | :--- | :--- |
| **Configuración de Security Groups** | Fase 1: Infraestructura | Anexos o Desarrollo |
| **`SHOW GRANTS` para `sentinel_web`** | Fase 3: Datos | Apartado 6.3 (Actividades) |
| **Panel de Estadísticas de HAProxy** | Fase 4: HA | Anexo 2 |
| **Dashboard de Wazuh con Alerta Real** | 7.1. Análisis de Seguridad | Apartado 7.1 |
| **Peticiones en Terminal (`ab`)** | 7.1. Rendimiento | Anexo 3 |
| **Bucket de S3 con Backups Diarios** | Fase 4: Resiliencia | Apartado 7.1 (Tabla) |
| **Terminal con `mysqldump --single-transaction`** | Fase 4: Resiliencia | Apartado 6.3 |
| **Log de Proxy (Acceso DB -> Internet)** | Fase 2: Conectividad | Desarrollo o Anexos |
| **Diagrama de Red Final (VPC)** | Fase 1: Redes | Anexo 1 |

---

## 🎯 Conclusión del Reenfoque
El objetivo es que la memoria no parezca un diario de lo que hiciste, sino un **Informe de Ingeniería** donde cada decisión técnica (por limitada que fuera por AWS) está justificada profesionalmente.
