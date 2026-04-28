# 📂 Índice de Capturas y Estado de Evidencias — SENTINEL

Este documento centraliza las capturas necesarias para la memoria y defensa del proyecto, vinculando los archivos existentes en `docs/img/` y marcando las tareas pendientes.

---

## 1. Infraestructura Cloud (AWS)
*Demostración del dominio del entorno de nube.*

- [x] **Consola de EC2:** Lista de instancias (`Web-Node`, `Monitoring`, `SIEM`) en verde.  
  > 🖼️ [ver capturasEC2.png](../docs/img/capturasEC2.png)
- [x] **Security Groups:** Reglas de entrada/salida (especialmente ALB -> Web Nodes).  
  > 🖼️ [ver Grupos de seguridad.png](../docs/img/Grupos de seguridad.png)
- [x] **ALB (Load Balancer):** DNS Name y Target Groups en estado "Healthy".  
  > 🖼️ [ver ALB.png](../docs/img/ALB.png)

---

## 2. Monitorización y Observabilidad (Grafana)
*Visualización en tiempo real del estado de los servicios.*

- [x] **Dashboard de Infraestructura:** Uso de CPU/RAM de los nodos.  
  > 🖼️ [ver evidencia_cpu_ram_test.png](../docs/img/evidencia_cpu_ram_test.png)
- [x] **Dashboard de Apache/MySQL:** Peticiones/seg y conexiones a base de datos.  
  > 🖼️ [ver evidencia_cpu_ram_test2.png](../docs/img/evidencia_cpu_ram_test2.png)
- [x] **Panel de Alertas:** Alertas integradas en el dashboard (Loki/Wazuh).  
  > 🖼️ [ver evidencia%20loki.png](../docs/img/evidencia%20loki.png)

---

## 3. Seguridad y SIEM (Wazuh)
*La "joya de la corona" del blindaje del sistema.*

- [x] **Dashboard de Wazuh:** Resumen de agentes y alertas recientes.  
  > 🖼️ [ver evidencia_wazuh_alerts.png](../docs/img/evidencia_wazuh_alerts.png)
- [x] **Evidencia de Active Response:** Log `/var/ossec/logs/active-responses.log`.  
  > 🖼️ [ver evidencia_active_response_log_bloqueo.png](../docs/img/evidencia_active_response_log_bloqueo.png)
- [x] **Iptables del Nodo Web:** Resultado de `sudo iptables -L` con baneo activo.  
  > 🖼️ [ver ipbaneada.png](../docs/img/ipbaneada.png)

---

## 4. Simulacro de Disaster Recovery (El "Antes y Después")
*Validación del plan de continuidad de negocio.*

1. [x] **Estado OK:** La tienda funcionando bajo condiciones normales.  
   > 🖼️ [ver evidencia_dr_estado_ok.png](../docs/img/evidencia_dr_estado_ok.png)
2. [x] **Estado ROTO:** Web caída tras borrar la base de datos.  
   > 🖼️ [ver evidencia_dr_estado_roto.png](../docs/img/evidencia_dr_estado_roto.png)
3. [x] **Proceso de Rescate:** Terminal con `aws s3 cp` e importación SQL.  
   > 🖼️ [ver evidencia_dr_auditoria_terminal.png](../docs/img/evidencia_dr_auditoria_terminal.png)
4. [x] **Estado RECUPERADO:** Web operativa de nuevo con todos los datos.  
   > 🖼️ [ver evidencia_dr_estado_ok.png](../docs/img/evidencia_dr_estado_ok.png)

---

## 5. Resiliencia y Almacenamiento (S3)
*Garantía de persistencia de datos.*

- [x] **Bucket de S3:** Lista de backups `.sql.gz` automatizados.  
  > 🖼️ [ver evidencia_aws_s3_bucket.png](../docs/img/evidencia_aws_s3_bucket.png)
- [ ] **Systemd Timers:** Salida de `systemctl list-timers`.  
  > 📌 **PENDIENTE**

---

## Extra: Diagramas
- **Arquitectura de Red:** [Diagrama Actualizado.drawio.svg](../docs/img/Diagrama%20Actualizado.drawio.svg)
