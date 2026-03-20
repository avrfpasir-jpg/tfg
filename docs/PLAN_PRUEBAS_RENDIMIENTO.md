# Plan de Pruebas de Rendimiento - Proyecto SENTINEL

## 1. Introducción
El objetivo de estas pruebas es validar la robustez, estabilidad y escalabilidad de la infraestructura desplegada en la Fase 4. Tras implementar el **Load Balancer (HAProxy)** y aplicar medidas de **Hardening**, es crucial determinar los límites operativos del sistema y asegurar que la monitorización (Prometheus/Grafana) y el SIEM (Wazuh) responden correctamente bajo carga.

## 2. Objetivos Principales
- **Determinar el punto de ruptura (Breaking Point):** Identificar cuántas peticiones simultáneas soporta el servidor web antes de degradar el servicio.
- **Validar el Balanceador de Carga:** Verificar que HAProxy distribuye el tráfico eficientemente y no introduce retardos excesivos.
- **Monitorización en Tiempo Real:** Observar el comportamiento de la CPU, RAM y E/S de red en los paneles de Grafana durante el estrés.
- **Detección de Anomalías:** Comprobar si Wazuh identifica el tráfico masivo como un posible ataque de denegación de servicio (DoS).

## 3. Entorno de Pruebas
- **Nodo Inyectores (Atacantes):** Servidor de Monitorización (u otro nodo externo).
- **Objetivo (Target):** IP Pública del Load Balancer (`34.234.93.231`).
- **Nodos de Backend:** Servidor Web (`10.0.1.250`) y Base de Datos (`10.0.2.61`).

## 4. Herramientas Sugeridas
1.  **Apache Benchmark (ab):** Para pruebas rápidas de concurrencia y peticiones totales.
2.  **Siege:** Para simular usuarios reales navegando por diferentes URLs de la tienda.
3.  **Httperf:** Para medir el rendimiento detallado de las conexiones HTTP.

## 5. Escenarios de Prueba

### Escenario A: Carga Normal (Baseline)
- **Descripción:** Simular un tráfico fluido de usuarios realizando compras.
- **Configuración:** 10 usuarios concurrentes durante 5 minutos.
- **Métrica esperada:** Tiempo de respuesta < 200ms, 0% de errores.

### Escenario B: Prueba de Carga (Peak Load)
- **Descripción:** Simular el tráfico esperado en un evento de ventas (ej. Black Friday).
- **Configuración:** 50-100 usuarios concurrentes.
- **Métrica esperada:** Estabilización de CPU en torno al 60-70%, sin caída de servicio.

### Escenario C: Prueba de Estrés (Stress Test)
- **Descripción:** Superar los límites teóricos para ver cómo falla el sistema.
- **Configuración:** Incrementar concurrentes (150, 200, 300...) hasta obtener errores HTTP 5xx.
- **Objetivo:** Identificar el cuello de botella (¿Es la CPU del Web? ¿Conexiones de MariaDB? ¿Ancho de banda?).

### Escenario D: Resistencia (Endurance Test)
- **Descripción:** Mantener carga media (30 concurrentes) durante 1 hora.
- **Objetivo:** Detectar fugas de memoria (Memory Leaks) o saturación de logs.

## 6. Métricas de Seguimiento (KPIs)
| Métrica | Herramienta | Valor Objetivo |
| :--- | :--- | :--- |
| **Response Time (Latencia)** | ab / Grafana | < 500ms (p95) |
| **Throughput (Requests/sec)** | ab / Siege | Maximizar según hardware |
| **Error Rate (%)** | Apache Logs / Grafana | < 1% |
| **CPU Usage** | Prometheus (Node Exporter) | < 80% en carga pico |
| **DB Connections** | MySQL Exporter | < Max_connections |

## 7. Procedimiento de Ejecución
1.  **Limpieza:** Reiniciar servicios para partir de un estado limpio.
2.  **Preparación:** Abrir el dashboard "SENTINEL - Resumen Ejecutivo" en Grafana.
3.  **Ejecución:** Lanzar comandos desde el nodo inyector.
    *   *Ejemplo ab:* `ab -n 5000 -c 50 http://34.234.93.231/index.php`
    *   *Ejemplo Siege:* `siege -c 100 -t 5m http://34.234.93.231/`
4.  **Observación:** Vigilar las alertas en Wazuh y los picos en Grafana.
5.  **Recogida de Datos:** Anotar los resultados finales del comando y capturar los gráficos de Grafana.

## 8. Análisis de Resultados y Conclusiones
Tras las pruebas, se deben documentar:
- ¿Cuál es la capacidad máxima de la infraestructura actual?
- ¿Respondió el balanceador según lo esperado?
- ¿Se dispararon las alertas de seguridad correctas?
- Propuestas de mejora (ej. escalado horizontal, caché con Redis, optimización de queries SQL).
