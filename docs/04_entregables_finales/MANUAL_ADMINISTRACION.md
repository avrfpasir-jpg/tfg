# 📘 Manual de Administración y Operativa — SENTINEL

**Proyecto:** SENTINEL (Infraestructura Web Segura)  
**Versión:** 1.0 (Edición Final Fase 5)  
**Responsable:** Álex Vidal Ródenas  

---

## 1. Introducción
Este manual describe los procedimientos necesarios para la operación, mantenimiento y resolución de incidencias de la infraestructura SENTINEL desplegada en AWS. Está dirigido a administradores de sistemas con conocimientos en plataformas Cloud y entornos Linux.

---

## 2. Mapa de Infraestructura y Accesos

### 2.1 Inventario de Nodos
| Componente | Rol | IP Privada | Acceso Público / DNS |
| :--- | :--- | :--- | :--- |
| **Sentinel-ALB** | Balanceador de Carga | N/A | `psicopompo.duckdns.org` |
| **Web-Node-1** | Servidor Apache/PHP | `10.0.1.250` | Vía ALB |
| **Sentinel-Monitoring** | Grafana / Prometheus | `10.0.1.233` | `statuspsicopompo.duckdns.org:3000` |
| **Sentinel-DB (RDS)** | MariaDB (Managed) | `10.0.0.242` | End-point privado RDS |
| **Wazuh-Manager** | SIEM / Seguridad | `10.0.1.170` | Vía SSH desde Bastion |

### 2.2 Gestión de Credenciales
*   **SSH:** Acceso mediante clave privada `.pem` autorizada en los Security Groups.
*   **Base de Datos:** Las credenciales de la aplicación se gestionan mediante variables de entorno (getenv) en el código PHP para evitar *hardcoding*.
*   **Grafana:** Acceso vía navegador en el puerto 3000 del nodo de monitorización.

---

## 3. Procedimientos Operativos (Día a Día)

### 3.1 Gestión de Copias de Seguridad (Backups)
El sistema cuenta con un script automatizado que realiza volcados de la base de datos MariaDB y los sube a Amazon S3.

*   **Frecuencia:** Diaria (gestionada por un Systemd Timer).
*   **Bucket Destino:** `s3://tfg-sentinel-backups-alex`
*   **Comando para backup manual:**
    ```bash
    sudo /usr/local/bin/backup_sentinel.sh
    ```
*   **Verificación:** Revisar los logs en `/var/log/sentinel_backup.log` o listar el bucket con `aws s3 ls s3://tfg-sentinel-backups-alex`.

### 3.2 Monitorización del Sistema
Para validar la salud de la infraestructura, el administrador debe consultar el panel de Grafana:
1.  Acceder a `http://statuspsicopompo.duckdns.org:3000`.
2.  Dashboard **"SENTINEL Infrastructure Overview"**.
3.  **Métricas clave:** Uso de CPU (>80% requiere escalado), Tráfico Apache (Peticiones/seg) y Latencia de base de datos.

### 3.3 Auditoría de Seguridad con Wazuh
El SIEM monitoriza ataques de fuerza bruta y cambios de integridad en los archivos.
*   **Alertas:** Las alertas críticas (Nivel > 7) se registran en el dashboard integrado de Grafana.
*   **Respuesta Activa:** Si una IP es bloqueada por fuerza bruta SSH, permanecerá en `iptables DROP` por 10 minutos.

---

## 4. Resolución de Incidencias Comunes (Troubleshooting)

### 4.1 Error de Conexión a la Base de Datos
*   **Causa:** El servicio RDS puede estar en mantenimiento o el Security Group ha perdido la regla de entrada.
*   **Acción:** Verificar el estado del endpoint en la consola de AWS y asegurar que el puerto 3306 está abierto para la IP del servidor web.

### 4.2 El sitio web no carga (Timeout)
*   **Causa:** El ALB no recibe respuesta de los nodos (Health Check fallido).
*   **Acción:** 
    1. Comprobar que el servicio apache esté corriendo: `sudo systemctl status httpd`.
    2. Verificar el puerto 80/443 en el Security Group de la instancia.

### 4.3 Wazuh Agents "Disconnected"
*   **Causa:** El Wazuh Manager (10.0.1.170) ha cambiado de IP o el servicio está caído.
*   **Acción:** Reiniciar el manager en el nodo correspondiente: `sudo systemctl restart wazuh-manager`.

---

## 5. Actualizaciones y Mantenimiento
Para mantener la seguridad, se recomienda ejecutar mensualmente en todos los nodos:
```bash
sudo dnf update -y  # En Amazon Linux 2023
sudo apt update && sudo apt upgrade -y # En Ubuntu (SIEM)
```
*Nota: Realizar siempre un Snapshot de la instancia en AWS antes de actualizaciones críticas.*

---
**Documento generado para el TFG - SENTINEL (2026)**
