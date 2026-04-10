# BITÁCORA DE ERRORES Y SOLUCIONES (TF ASIR)

Este archivo registra las incidencias técnicas encontradas durante el despliegue de la Fase 1 del proyecto SENTINEL y las soluciones aplicadas para superarlas. Este documento es fundamental para la justificación de la memoria del TFG.

---

### ❌ Error 1: `a2enmod: command not found`
- **Contexto**: Al intentar habilitar el módulo SSL y Headers en el servidor Apache (AWS).
- **Causa**: El SO del servidor es **Amazon Linux (RHEL)**, no Ubuntu/Debian. Los comandos `a2enmod` no son válidos.
- **Solución**:
    1. Instalación del paquete `mod_ssl` mediante `yum`.
    2. Activación automática del módulo mediante la creación de los archivos `.conf` en `/etc/httpd/conf.d/`.

---

### ❌ Error 2: `403 Forbidden` (Acceso denegado en Apache)
- **Contexto**: Al probar la conexión SSL cargando la tienda en el navegador.
- **Causa**: El usuario de Apache (`apache`) no tiene permisos de ejecución sobre la ruta de `DocumentRoot` (carpetas padre como `/home/ec2-user`).
- **Solución**:
    1. Aplicación de `chmod 711` a la carpeta `/home/ec2-user` para permitir el paso (transversal) del servidor web.
    2. Ajuste de permisos `chmod -R 755` sobre la carpeta `src` del proyecto.
    3. Corrección del `DocumentRoot` en `/etc/httpd/conf.d/sentinel-ssl.conf` para apuntar a la ruta absoluta correcta.

---

### ❌ Error 3: `curl: (7) Failed to connect to localhost port 443`
- **Contexto**: Durante la verificación del túnel SSL interno.
- **Causa**: Intento de ejecución del comando `curl` en la máquina de la Base de Datos (`10.0.2.61`), que no tiene desplegado el servidor web.
- **Solución**: Ejecución de las pruebas de conectividad directamente en el servidor Web (`10.0.1.250`) o a través del balanceador HAProxy.

---

### ❌ Error 4: `Error de conexión interno` (PHP a MariaDB)
- **Contexto**: Al cargar la aplicación PHP tras aislar los secretos de conexión.
- **Causa (Dual)**: 
    1. **Privilegios de DB**: El usuario `sentinel_web` estaba configurado solo para `@'localhost'`. La conexión desde el servidor web requiere permisos para la IP de origen (`10.0.1.250`).
    2. **Filtro de SELinux**: Amazon Linux bloquea por defecto que el servicio Apache (`httpd`) abra sockets de red hacia bases de datos externas.
- **Solución**:
    1. Ejecución de `CREATE USER 'sentinel_web'@'10.0.1.250' ...` en MariaDB para permitir el acceso remoto desde la red privada.
    2. Ejecución de `sudo setsebool -P httpd_can_network_connect_db 1` en el servidor web para deshabilitar el bloqueo de red de SELinux.

---

### 🛡️ Limitación Encontrada 5: SSM Agent bloqueado en Subred Privada (AWS Academy)
- **Contexto**: Al intentar conectar a la Base de Datos (en subred privada) mediante AWS Systems Manager (SSM) tras configurar la NAT Instance.
- **Sintomatología**: El agente SSM mostraba el error: `unable to acquire credentials... send request failed`.
- **Causa Analizada**: Las cuentas de **AWS Academy (Learner Lab)** aplican políticas estrictas invisibles (`IAM Permissions Boundary`) al `LabInstanceProfile`. Cuando SSM intenta autenticarse usando la NAT Instance casera en lugar de un NAT Gateway oficial (que es de pago), el entorno de laboratorio rechaza el token de seguridad.
- **Solución Técnica (Workaround TFG)**: Se abandona el acceso SSM directo para la Base de Datos. Se justifica en la memoria como una limitación del entorno académico y se opta por el uso del **Servidor Web como Bastion Host (Salto SSH)**, que es una arquitectura igualmente profesional y estandarizada.

---

### ❌ Error 6: `No route to host` (HAProxy marcando Backend DOWN)
- **Contexto**: Al intentar acceder a la web tras configurar el cifrado SSL End-to-End (HTTPS) entre el Load Balancer y el Servidor Web.
- **Causa**: El firewall interno de la máquina Web (`firewalld`) estaba configurado con una regla específica ("rich rule") que solo permitía tráfico procedente de la IP del Load Balancer (`10.0.1.117`) por el puerto `80` (HTTP). Al configurar el SSL interno, HAProxy comenzó a enviar tráfico al puerto `443` (HTTPS), el cual `firewalld` rechazaba ("No route to host").
- **Solución**:
    1. Diagnóstico del error en los logs de HAProxy (`Layer4 connection problem`).
    2. Añadir la regla pertinente en el Servidor Web: `sudo firewall-cmd --add-rich-rule='rule family="ipv4" source address="10.0.1.117" port port="443" protocol="tcp" accept' --permanent`
    3. Aplicar los cambios con `sudo firewall-cmd --reload`.

---

*Documentar estos fallos demuestra que el administrador de sistemas domina tanto la resolución de problemas de Red, como de Sistemas y de Aplicaciones.*
