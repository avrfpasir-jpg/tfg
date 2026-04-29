# 📅 PLAN PARA MAÑANA

Este documento resume el estado actual y las tareas necesarias para finalizar la puesta a punto del proyecto SENTINEL.

## 📍 Estado Actual

- **Dashboard**: Versión 9 (Final) con Alert List y SIEM integrado.
- **Conectividad AWS**: Operativa 100% mediante configuración estática (`config.yml` corregido).
- **Alertmanager**: Conectado a Telegram.

## 🛠️ Tareas Pendientes (Mañana)

1.  **Reiniciar Web Server**: Entrar en `10.0.1.250` usando `ec2-user` (no `ubuntu`) para devolver la tienda a la vida después del test de estrés.
2.  **Captura de 30 min**: Una vez reiniciado, el tráfico del ALB volverá a fluir y tendremos la captura perfecta para la defensa.
3.  **Hardening Final**: Revisar los permisos de los archivos de llaves en el servidor de monitorce.

## 🔑 Credenciales Críticas

- **ALB ARN**: `arn:aws:elasticloadbalancing:us-east-1:741758839788:loadbalancer/app/Sentinel-ALB/b5f600e0080669d3`
- **User Web**: `ec2-user`
- **User Monitoring/Wazuh**: `ubuntu`

---
*Documento generado al cierre de la sesión del 29/04/2026. Ready for the final stretch! 🚀*
