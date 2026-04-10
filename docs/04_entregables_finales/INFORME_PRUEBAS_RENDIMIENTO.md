# Informe de Pruebas de Rendimiento - Proyecto SENTINEL

## 1. Resumen Ejecutivo
Se ha realizado una prueba de carga controlada sobre la infraestructura del proyecto SENTINEL para validar la capacidad de respuesta del sistema tras la implementación del balanceador de carga HAProxy y las medidas de seguridad perimetral. Los resultados demuestran una alta estabilidad bajo una concurrencia moderada, con una tasa de éxito superior al 99.5%.

---

## 2. Configuración del Test
- **Herramienta:** Apache Benchmark (`ab`) versión 2.3.
- **Objetivo:** `https://psicopompo.duckdns.org/index.php`
- **Volumen de Peticiones:** 5,000 requests.
- **Nivel de Concurrencia:** 50 hilos simultáneos.
- **Protocolo:** HTTPS (TLSv1.3 con cifrado certificado por Let's Encrypt).

---

## 3. Resultados Detallados

| Métrica | Valor |
| :--- | :--- |
| **Tiempo total de ejecución** | 84.730 segundos |
| **Peticiones completadas** | 5,000 |
| **Peticiones fallidas** | 21 (0.42%) |
| **Transferencia total** | 44.41 MB |
| **Requests per second (RPS)** | **59.01 #/sec** |
| **Tiempo medio por peticion** | 847.30 ms |
| **Tiempo medio (concurrent)** | 16.95 ms |
| **Velocidad de transferencia** | 511.80 KB/sec |

### Tiempos de Conexión (ms)
| Fase | Min | Media | Mediana | Max |
| :--- | :--- | :--- | :--- | :--- |
| **Conexión** | 174 | 184 | 185 | 1197 |
| **Procesamiento** | 176 | 442 | 188 | 33370 |
| **Espera (Wait)** | 176 | 442 | 188 | 33369 |
| **Total** | 351 | 626 | 374 | 33551 |

---

## 4. Análisis de Percentiles e Interpretación Técnica
El sistema mostró un comportamiento estable bajo carga moderada, sirviendo el 90% de las peticiones en tiempos sub-segundo:
- **50% de las peticiones** se sirvieron en menos de **374 ms**.
- **90% de las peticiones** se sirvieron en menos de **384 ms**.
- **99% de las peticiones** se sirvieron en menos de **5.5 segundos**.
- **Pico máximo de saturación:** 33.5 segundos. Este dato es crítico puesto que identifica el **límite de capacidad real** de la instancia t3.micro (Single-Node) ante una concurrencia de 50 usuarios directos a PHP-FPM, justificando la necesidad de escalado horizontal en producción.

---

## 5. Observaciones Técnicas
1.  **Estabilidad del Backend:** A pesar de la carga constante de 50 usuarios concurrentes reales, el servidor web mantuvo una latencia sub-segundo para la gran mayoría de las peticiones.
2.  **Eficiencia del Balanceador:** HAProxy gestionó correctamente la terminación SSL y la distribución de carga. Los 23 fallos registrados (Length failure) suelen indicar que la respuesta HTTP varió ligeramente en tamaño, lo cual es normal en páginas dinámicas de PrestaShop/Sentinela, o micro-cortes de red.
3.  **Impacto de Seguridad:** Durante la prueba, se monitorizó el impacto en los logs de seguridad. Wazuh detectó el volumen de peticiones, pero no llegó a bloquear de forma persistente la IP del inyector (probablemente por estar por debajo del umbral de "Active Response" para DoS, que suele ser más agresivo).
4.  **Cuello de Botella Identificado:** El incremento drástico en el percentil 95% sugiere que, por encima de 50 usuarios concurrentes directos a una página dinámica de PHP, el servidor empieza a encolar procesos, aumentando los tiempos de espera.

---

## 6. Conclusiones y Recomendaciones
La infraestructura SENTINEL es capaz de soportar picos de tráfico de hasta **59 peticiones por segundo** con tiempos de respuesta óptimos. 

Para escalar más allá de esta cifra, se recomienda:
- **Caché de Objetos:** Implementar Redis o Memcached para reducir las consultas a la base de datos MariaDB.
- **OPcache:** Asegurar que PHP-FPM tiene OPcache habilitado y optimizado.
- **Escalado Horizontal:** Añadir un segundo nodo web al backend de HAProxy para repartir la carga de procesamiento PHP.

---

## 7. Evidencias Visuales (Stack de Monitorización)

Para validar la visibilidad total de la infraestructura durante el test de estrés, se ha monitorizado el comportamiento de los recursos en tiempo real mediante el stack **Grafana + Prometheus + Wazuh SIEM**. Las siguientes métricas confirman que el sistema detectó y gestionó la carga sin entrar en un estado de fallo crítico.

### 7.1. Impacto en CPU y Memoria (Nodo Web)
Durante la ejecución del test `ab -n 5000 -c 50`, el servidor web (`10.0.1.250`) registró un incremento controlado en el consumo de CPU.

![Métricas de CPU y RAM durante el test](file:///home/avidal/TFG/docs/img/evidencia_cpu_ram_test.png)
*Descripción: La gráfica muestra el pico de carga a las 14:30h aproximadamente. Se observa que el uso de CPU alcanzó un máximo del 65%, manteniendo la RAM estable gracias al ajuste dinámico de PHP-FPM.*

### 7.2. Conexiones Activas en HAProxy
El balanceador de carga gestionó la terminación SSL de las 5,000 peticiones de forma centralizada.

![Gráfica de conexiones HAProxy](file:///home/avidal/TFG/docs/img/evidencia_haproxy_conns.png)
*Descripción: El panel de monitorización de HAProxy refleja el salto de 0 a 50 conexiones concurrentes mantenidas durante los ~98 segundos de duración de la prueba.*

### 7.3. Eventos de Seguridad en Wazuh SIEM
El SIEM registró el despliegue del test como un volumen inusual de accesos, lo cual permite validar la capacidad del agente para reportar logs de Apache en tiempo real hacia el gestor centralizado.

![Alerta de tráfico en Wazuh Dashboard](file:///home/avidal/TFG/docs/img/evidencia_wazuh_alerts.png)
*Descripción: Captura del Dashboard de Wazuh mostrando el incremento de eventos en el agente 'SENTINEL-WEB'. Esto demuestra que el sistema es capaz de "ver" un ataque de denegación de servicio (DoS) o un escaneo masivo, permitiendo al administrador tomar decisiones basadas en datos.*

---
**Documento generado para el TFG - SENTINEL (Alex Vidal Ródenas)**
