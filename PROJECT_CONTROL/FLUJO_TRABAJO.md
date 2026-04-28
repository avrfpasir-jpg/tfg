# ⚙️ Flujo de Trabajo y Estructura del Sistema

## 📂 Organización de Carpetas
*   `/infrastructure`: Contiene los scripts de automatización, configuraciones de monitoreo y base de datos.
*   `/docs`: Centraliza la gestión del proyecto (Fases) y los entregables finales de la memoria.
*   `/docs/img`: Repositorio único de evidencias visuales (capturas validadas).

## 🛠️ Procedimientos Estándar
1.  **Validación de Evidencias:** Cada nueva funcionalidad debe capturarse y registrarse en el `INDICE_CAPTURAS.md`.
2.  **Actualización de Memoria:** No se cierra una tarea técnica sin actualizar su fase correspondiente en `docs/`.
3.  **Versionado:** Todo cambio en la configuración de monitorización (Prometheus/Grafana) debe reflejarse en los archivos `.yml` y `.json` dentro de `infrastructure/monitoring`.

## ⏭️ Siguiente Paso Inmediato
**Inicialización del módulo de Terraform:**
El objetivo actual es profesionalizar el despliegue manual. Debes crear la carpeta `infrastructure/automation/terraform` e iniciar los archivos `main.tf` y `variables.tf` definiendo los recursos base de la red (VPC y Subredes). 

Este paso elevará el proyecto de una "implementación artesanal" a una "infraestructura industrial".
