# 📦 Guía de Configuración: Backups a Amazon S3

Has implementado la arquitectura de "Continuidad de Negocio" que prometiste en el diagrama. Aquí tienes los **3 pasos finales** para que el script funcione en tus servidores de AWS:

---

## 1. Crear el Bucket en AWS ☁️
En tu consola de AWS (donde lanzaste las instancias EC2):
1. Busca el servicio **S3** -> **Create bucket**.
2. Dale un nombre (ej: `tfg-sentinel-backups-tunombre`).
3. Deja todo por defecto (bloquea el acceso público, es fundamental por seguridad).
4. Pulsa **Create bucket**.

*Importante*: Copia ese nombre y sustitúyelo en la variable `S3_BUCKET` dentro de tu archivo [backup_db.sh](file:///home/avidal/TFG/automation/scripts/backup_db.sh).

---

## 2. Configurar Acceso sin Contraseña (Seguridad ASIR) 🔐
Para que el script no se quede esperando a que alguien escriba la contraseña de MariaDB, crea este archivo oculto en la **HOME** del servidor de Base de Datos:

1. Ejecuta: `nano ~/.my.cnf`
2. Pega este contenido:
   ```ini
   [mysqldump]
   user=admin_tienda
   password=TU_PASSWORD_AQUI
   ```
3. Ejecuta `chmod 600 ~/.my.cnf` (esto es CRUCIAL: solo tú usuario podrá leer este archivo con tu clave).

---

## 3. Automatización con Cron (El toque final) ⏰
Para que el backup se haga solo **todas las noches a las 03:00 AM**, haz lo siguiente en la terminal del servidor:

1. Ejecuta: `crontab -e`
2. Añade esta línea al final del archivo:
   ```bash
   00 03 * * * /home/ubuntu/tfg/automation/scripts/backup_db.sh >> /home/ubuntu/backup_log.txt 2>&1
   ```
3. Guarda y sal.

---

### 🛡️ ¿Qué has ganado con esto para el TFG?
*   **Recuperación ante desastres**: Al estar en S3, si la instancia de AWS "muere", tus datos siguen vivos en otro sitio.
*   **Seguridad y Privacidad**: Al usar `.my.cnf` con permisos 600, los secretos no están visibles en el historial de comandos ni dentro del script.
*   **Estrategia FinOps**: S3 tiene un coste ínfimo comparado con tener discos EBS gigantes haciendo snapshots.

---

**Siguiente paso sugerido**: ¿Quieres que pasemos a configurar el **SSL (HTTPS)** en el HAProxy con Certbot para que la web sea segura ya hoy?🚀
