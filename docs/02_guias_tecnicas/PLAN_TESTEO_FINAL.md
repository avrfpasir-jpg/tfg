# 🧪 Plan de Testeo y Validación Final - Proyecto SENTINEL

Este documento detalla las pruebas necesarias para validar la infraestructura de Alta Disponibilidad del proyecto SENTINEL y servir de base para las capturas de pantalla de la memoria del TFG.

## 1. Validación de Conectividad y Acceso Externo

### 1.1 Acceso Web (ALB + DuckDNS)
- **Acción:** Navegar a `https://psicopompo.duckdns.org`.
- **Resultado Esperado:** 
    - Carga de la aplicación Sentinel.
    - Certificado SSL válido (proporcionado por AWS ACM).
    - Persistencia de sesión (Stickiness) gestionada por el ALB.
- **Captura:** Navegador mostrando el candado verde y la web operativa.

### 1.2 Balanceo de Carga (HA)
- **Acción:** Producir una caída simulada de una de las instancias del Web Server.
- **Resultado Esperado:** El ALB redirige el tráfico a la instancia restante sin interrupción del servicio.
- **Captura:** Panel de AWS EC2 mostrando una instancia "InService" y otra "OutOfService/Terminated".

---

## 2. Validación de Base de Datos (RDS Multi-AZ)

### 2.1 Conexión Aplicación -> RDS
- **Acción:** Realizar un login o registrar una actividad en la web.
- **Resultado Esperado:** Los datos se persisten correctamente en el endpoint `database-sentinel...`.
- **Captura:** Captura de la base de datos desde Workbench/HeidiSQL o logs de la aplicación confirmando el Host de la DB.

### 2.2 Failover de RDS
- **Acción:** Provocar un "Reboot with Failover" en la consola RDS.
- **Resultado Esperado:** La web sigue conectada tras unos segundos de reconexión automática.
- **Captura:** Eventos de RDS mostrando el cambio a la instancia de Standby.

---

## 3. Monitorización y Seguridad (SIEM)

### 3.1 Dashboards de Grafana
- **Acción:** Acceder a `http://98.81.162.69:3000`.
- **Resultado Esperado:** Visualización de métricas en tiempo real de los nodos (CPU, RAM, Conexiones del ALB).
- **Captura:** Dashboard "SENTINEL - Resumen Ejecutivo" con datos dinámicos.

### 3.2 Alertas en Wazuh
- **Acción:** Intentar un ataque de fuerza bruta por SSH o acceso no autorizado.
- **Resultado Esperado:** El Wazuh Manager detecta la actividad y genera una alerta de Nivel > 7.
- **Captura:** Dashboard de Wazuh mostrando la alerta real generada durante el test.

---

## 4. Pruebas de Estrés (Stress Testing)

### 4.1 Simulación de Carga
- **Comando:** `ab -n 1000 -c 20 https://psicopompo.duckdns.org/`
- **Resultado Esperado:** Respuesta exitosa por parte del ALB.
- **Captura:** Resultado en terminal de Apache Benchmark y pico de tráfico en Grafana.

---

## 5. Control de Auditoría
- **Archivo Referencia:** `docs/02_guias_tecnicas/auditoria.txt`
- **Captura:** Mostrar el script de sincronización `update_duckdns.sh` funcionando en cron.
