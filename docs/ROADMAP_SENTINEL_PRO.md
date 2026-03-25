# ROADMAP SENTINEL: Hacia la Excelencia Profesional (TFG ASIR)

Este listado detalla las tareas necesarias para elevar el proyecto SENTINEL de un entorno de laboratorio a una infraestructura de nivel industrial blindada ante tribunales expertos.

---

## 🔐 Fase 1: Seguridad de Acceso y Datos (Prioridad Alta)
*   [ ] **Segimentación de Privilegios en DB:** Crear usuario `sentinel_web` con permisos restringidos (SELECT, INSERT, UPDATE) solo sobre la base de datos de la tienda.
*   [ ] **Hardening de MariaDB:** Eliminar la base de datos `test`, deshabilitar acceso remoto de root y aplicar `mysql_secure_installation`.
*   [ ] **Cifrado de Secretos:** Migrar las contraseñas de los archivos `.php` a variables de entorno o a un archivo de configuración fuera de la raíz web (o usar AWS Secrets Manager si el presupuesto lo permite).
*   [ ] **SSL End-to-End:** Configurar el cifrado TLS entre el HAProxy y el Servidor Apache (puerto 443 interno) para evitar sniffing en tránsito dentro de la VPC.

---

## 📡 Fase 2: Conectividad y Mantenimiento (Prioridad Media)
*   [ ] **Implementación de NAT:** Desplegar una **NAT Instance** (t2.micro) o un Proxy Squid en la subred pública para permitir que la base de datos descargue parches de seguridad de forma controlada.
*   [ ] **Gestión Remota Segura:** Configurar **AWS Systems Manager (SSM)** para acceder a las instancias sin abrir el puerto 22 (SSH) al mundo.
*   [ ] **Bastion Host Dedicado:** Migrar la funcionalidad de salto SSH de la máquina web a una instancia mínima dedicada y endurecida.

---

## 💾 Fase 3: Resiliencia y Continuidad (Prioridad Media)
*   [ ] **Optimización de Backups:** Refinar el script de S3 con el flag `--single-transaction` y añadir rotación de copias (borrar antiguos).
*   [ ] **Protocolo de Restauración:** Documentar y realizar al menos una prueba de restauración completa (Disaster Recovery Plan) y guardarla como anexo.
*   [ ] **Ciclo de Vida de Logs:** Configurar políticas de retención en Wazuh/Loki para evitar el llenado de disco en instancias de bajo coste.

---

## 🚀 Fase 4: Alta Disponibilidad y Escalabilidad (Prioridad Opcional)
*   [ ] **Clúster HAProxy:** Investigar e implementar (si es posible en AWS Academy) el uso de **Keepalived** para redundancia de balanceadores.
*   [ ] **Auto-scaling Group:** Definir una AMI del servidor web y configurar un grupo de auto-escalado basado en la carga detectada por Prometheus.
*   [ ] **Content Delivery Network (CDN):** Usar **AWS CloudFront** para servir las imágenes de los productos desde el borde (Edge locations).

---

## 📄 Fase 5: Documentación y Defensa (Prioridad Final)
*   [ ] **Integración de Argumentos "Pro":** Actualizar la Memoria Final con las justificaciones técnicas aprendidas en la simulación del tribunal.
*   [ ] **Anexo de Troubleshooting:** Crear una tabla de errores reales encontrados durante estos 2 meses y cómo se resolvieron.
*   [ ] **Guion de Defensa:** Preparar la presentación visual enfocada en la "Arquitectura Resiliente" más que en la simple venta de productos.
