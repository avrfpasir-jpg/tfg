> ⛔ **DOCUMENTO INVALIDADO** — Sustituido por `PLAN_CIERRE_FINAL.md` (2026-04-01). No usar como referencia activa.

# 🚀 ROADMAP Y TAREAS UNIFICADAS - FASE 4 (SENTINEL)

Este documento unifica los requisitos académicos (`Tareas Fase 4.txt`), los hitos técnicos que planteamos (`ROADMAP_SENTINEL_PRO.md`) y el plan de cierre más inminente (`TAREAS_PROXIMA_SEMANA.md`). Todo centralizado para no perder el foco.

---

## 🎯 1. Alta Disponibilidad y Conectividad (Infraestructura)
*   [x] **Configuración SSL Interna:** Cifrado End-to-End validado entre HAProxy y Web (Puerto 443 interno). *(Hecho y debuggeado)*.
*   [x] **SSL Externo (Let's Encrypt):** Terminar SSL en el HAProxy asociado al dominio `psicopompo.duckdns.org` usando Certbot y forzar redirección de HTTP a HTTPS. *(Finalizado y comprobado)*.
*   [x] **Bastion Host (Salto SSH):** Oficializar la máquina web (`10.0.1.250`) como "Jump Server" para acceder a la BD e implementar el flujo de acceso seguro. *(Configurado y documentado)*.
*   [ ] **Clusterización HAProxy (Opcional TFG):** Investigar e implementar `Keepalived` para evitar que el balanceador sea un SPOF.

## 🛡️ 2. Seguridad y Hardening (Completando la Fase 4)
*   [x] **Segregación de DB y MariaDB Hardening:** POLP implementado, usuario `sentinel_web`, DB `test` eliminada. *(Hecho)*.
*   [ ] **Gestión de Secretos (AWS Secrets Manager):** Migrar si es posible el usuario de BD fuera del root folder (actualmente en `config_sentinel_db.php`) u ocultarlo vía API.
*   [x] **Optimización del SIEM (Wazuh):** Ajustar la memoria de OpenSearch JVM a 1GB para evitar sobrecargas de RAM, programar rotación de índices (ISM a 5 días) y limitar avisos a nivel > 5. Además, afinar las reglas para reducir falsos positivos y mejorar el rendimiento general. *(Script `optimize_wazuh_siem.sh` creado).*
*   [x] **Alertas Mejoradas por Telegram:** Perfeccionar la integración de avisos hacia Telegram, ajustando la severidad, el formato y el contenido de los mensajes enviados por el bot para que sean útiles a nivel administrativo. *(Formato personalizado añadido al script preparator).*

## 💾 3. Resiliencia, Backups y Disaster Recovery
*   [x] **Backup Automatizado en S3:**
    *   [x] Crear bucket privado e implementar el script de bash `backup_db.sh` usando `mysqldump --single-transaction`. *(Script creado)*
    *   [x] Programación nocturna automática (Systemd Timer) del volcado. *(Finalizado en DB Búnker)*
*   [x] **Disaster Recovery (Simulacro):** Validar la restauración de BD y registrar el proceso para los anexos de la memoria. *(Completado y documentado en ANEXO_DISASTER_RECOVERY.md)*.

## 📈 4. Monitorización y Pruebas de Rendimiento
*   [x] **Test de Carga (`ab`):** Lanzar 5000 peticiones con 50 concurrentes. *(Completado y optimizado: 59 RPS alcanzados)*.
*   [ ] **Evidencias Gráficas (Grafana):** Capturar picos de CPU/RAM del último test para la Memoria.
*   [ ] **Comprobación de Active Response (Wazuh):** Validar que `firewall-drop` bloquea agresores durante ataques simulados.

## 📑 5. Entregables y Cierre Documental
*   [ ] **Diagrama de Red Definitivo:** Integrar el SVG (`Diagrama Actualizado.drawio.svg`) en la documentación formal.
*   [ ] **Redacción de la Memoria Fase 4:**
    *   [x] Volcar Métricas del test de carga. *(Hecho)*.
    *   [ ] Exponer las Evidencias (Screenshots finales).
    *   [ ] Argumentar económicamente y justificar decisiones.
*   [ ] **Preparación de Defensa:** Pulir `GUIA_DEFENSA_TFG.md`.

---
*Nota: Este listado sustituye e invalida el uso en paralelo de `Tareas Fase 4.txt`, `ROADMAP_SENTINEL_PRO.md` y `TAREAS_PROXIMA_SEMANA.md` para evitar confusiones.*
