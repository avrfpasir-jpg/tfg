# Enfoque del Proyecto - Fase 4: Robustecimiento y Alta Disponibilidad

## 📍 Estado Actual (Recap)
La infraestructura base en AWS está operativa (**100% UP**):
- **Nodos:** Web (Apache/PHP), DB (MariaDB), Wazuh (SIEM) y Monitoring (Grafana/Prom/Loki).
- **Hito alcanzado:** El sistema ya es capaz de detectar ataques reales (como fuerza bruta SSH) y visualizarlos en tiempo real en Grafana.

---

## 🚀 Pilares de la Fase 4: "De Prototipo a Fortaleza"

### 1. HA / Balanceo (Alta Disponibilidad)
- **Objetivo:** Eliminar puntos únicos de fallo (*Single Points of Failure*).
- **Acción:** Implementar un **Load Balancer** (HAProxy o Nginx) para distribuir el tráfico y, si es posible, preparar la replicación de nodos críticos.

### 2. Hardening (Endurecimiento)
- **Objetivo:** Reducir la superficie de ataque al mínimo.
- **Acción:** Auditoría de puertos, desactivación de servicios innecesarios, aplicación de políticas de contraseñas estrictas y parcheo de seguridad siguiendo estándares.

### 3. Pruebas de Rendimiento (Stress Testing)
- **Objetivo:** Validar qué carga real soporta la infraestructura.
- **Acción:** Ejecutar pruebas de estrés para identificar cuellos de botella (CPU/RAM) y verificar que el sistema de alertas responde correctamente bajo presión.

### 4. Automatización Final
- **Objetivo:** Garantizar la consistencia y la reproducibilidad.
- **Acción:** Refinar los scripts de despliegue (`start.sh`, `ansible`, etc.) para que el entorno sea recreable de forma rápida y sin errores manuales.

---

## 🎯 Próximos Pasos Sugeridos
1.  **Ejecutar Pruebas de Rendimiento:** Utilizar `ab` y `siege` según el [Plan de Pruebas](docs/PLAN_PRUEBAS_RENDIMIENTO.md).
2.  **Monitorización Activa:** Observar el impacto en Grafana y Wazuh durante el estrés.
3.  **Ajuste Fino (Tuning):** Optimizar HAProxy o Apache basándose en los resultados.

*Documento actualizado para la ejecución de pruebas de rendimiento.*
