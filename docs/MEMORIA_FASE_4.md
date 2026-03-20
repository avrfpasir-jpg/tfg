# Proyecto SENTINEL - Memoria de Fase 4

## 1. Actualización de la Planificación
En esta fase final, se han cumplido los objetivos de transformar el prototipo inicial en una infraestructura robusta. El foco principal ha sido la eliminación de puntos únicos de fallo y el blindaje de la red.

## 2. Ejecución de Servicios Avanzados (HA y Monitorización)
Se ha implementado un **Load Balancer (HAProxy)** que actúa como puerta de enlace única, distribuyendo la carga y protegiendo el servidor web.

- **Panel de estadísticas:** Permite verificar en tiempo real la salud de los servidores backend.
- **Monitorización Unificada:** Grafana centraliza métricas de CPU, RAM, Apache y MariaDB.

![Estadísticas de HAProxy](/home/avidal/TFG/capturasfase4/Panel de Estadísticas de HAProxy.png)
*Panel de salud de HAProxy mostrando los nodos operativos.*

## 3. Informe de Hardening y Seguridad
Se han aplicado las siguientes capas de blindaje:
- **Aislamiento de Red:** El servidor Web solo permite tráfico desde la IP privada del Load Balancer.
- **SSH Hardening:** Desactivado el login de root y restringido por llaves RSA.
- **SIEM Wazuh:** Integración total con Grafana para visualización de alertas.

![Bloqueo de red](/home/avidal/TFG/capturasfase4/image.png)
*Prueba de acceso directo fallida a la IP del servidor web (Hardening verificado).*

## 4. Resultados de Pruebas de Rendimiento
Se han realizado pruebas de estrés utilizando `Apache Benchmark (ab)` para encontrar los límites de la infraestructura actual.

- **Capacidad Máxima:** ~47 peticiones por segundo sostenidas.
- **Cuello de Botella:** Al superar las 50 conexiones concurrentes, el servidor web alcanzó picos de latencia de 800ms.

### 4.1. Propuestas de Mejora
*   **Escalado Horizontal:** Añadir un segundo nodo en el backend de HAProxy para repartir la carga de PHP.
*   **Caché de Consultas:** Implementar Redis o Memcached para reducir la carga en MariaDB.
*   **Optimización de PHP-FPM:** Ajustar los parámetros de `pm.max_children` para manejar más procesos simultáneos.

![Resultados Stress Test](/home/avidal/TFG/capturasfase4/Terminal con los resultados de ab.png)
*Resultado de la prueba de 5000 peticiones concurrentes.*

![Carga de la Base de Datos](/home/avidal/TFG/capturasfase4/Carga de la Base de Datos.png)
*Impacto en la base de datos MariaDB durante las ráfagas de peticiones.*

## 5. Gestión de Cambios e Incidencias
Durante las pruebas de estrés, el sistema de monitorización detectó correctamente la inestabilidad de los servicios.

| Fecha | Incidencia | Resolución |
| :--- | :--- | :--- |
| 20/03/2026 | Caída de Apache bajo estrés (5000 pet) | Reinicio automático de Wazuh y ajuste de MaxClients |
| 20/03/2026 | Error XML en agente Wazuh | Corrección manual en ossec.conf |

![Alerta Wazuh](/home/avidal/TFG/capturasfase4/Pico en Grafana.png)
*Visualización en Grafana del pico de carga y alertas de seguridad generadas.*

![Logs de HAProxy](/home/avidal/TFG/capturasfase4/Logs de HAProxy.png)
*Registro de logs en tiempo real observando las respuestas 200 y 503 durante el estrés.*

## 6. Valoración Económica Final (AWS Academy)
Considerando el uso de 5 instancias tipo `t2.micro` y `t3.small`:
- **Presupuesto Estimado:** $0.0116/hora por instancia.
- **Coste Real Acumulado:** Dentro de los $100 de crédito de AWS Academy.

---

Este documento completa los requisitos técnicos de la Fase 4 del Proyecto SENTINEL.
