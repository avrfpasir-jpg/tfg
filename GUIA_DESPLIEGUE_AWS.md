# ☁️ Guía de Despliegue: AWS Academy + GitHub (Método Profesional)

Esta guía utiliza **GitHub** para el código y la terminal de **AWS** para el servidor. Es el método estándar en la industria y el que más puntos te dará en tu TFG.

---

## 📋 Requisitos Previos
1. Una cuenta en [GitHub](https://github.com).
2. Tener instalado [Git en Windows](https://git-scm.com/downloads).
3. Acceso a **AWS Academy Learner Lab**.

---

## 1️⃣ Fase 1: Subir tu proyecto a GitHub (Desde Windows)

Si ya tienes el código en GitHub, sáltate este paso. Si no:

1.  Crea un nuevo repositorio en GitHub llamado `tienda_segura` (déjalo como **Público** para facilitar el clonado en AWS).
2.  Abre una terminal en tu carpeta `C:\xampp\htdocs\tienda_segura` y ejecuta:
    ```bash
    git init
    git add .
    git commit -m "Primer commit: Mi tienda segura"
    git branch -M main
    git remote add origin https://github.com/TU_USUARIO/tienda_segura.git
    git push -u origin main
    ```

---

## 2️⃣ Fase 2: Configurar el Servidor en AWS (Red Detallada)

1.  Entra en **AWS Academy** -> **Learner Lab** -> **Start Lab** (espera al círculo verde) -> **AWS Console**.
2.  Busca **EC2** -> **Launch Instance**.
3.  En el apartado **Network Settings**, pulsa en el botón **Edit** (es crucial para ver el VPC y la Subred):
    *   **VPC:** Selecciona el que diga `Lab VPC` (es el que AWS Academy crea para ti). Si solo hay uno, déjalo por defecto.
    *   **Subnet:** Elige una que NO diga "Private". Normalmente cualquiera de las opciones (us-east-1a, 1b, etc.) servirá, pero asegúrate de lo siguiente:
    *   **Auto-assign public IP:** Cámbialo a **Enable**. *Si no haces esto, tu servidor no tendrá dirección IP para entrar desde fuera.*
4.  **Security Groups (Firewall):**
    *   Selecciona "Create security group".
    *   **Name:** `web-server-sg`.
    *   **Reglas de entrada (Inbound Rules):**
        *   **SSH (Puerto 22):** Source: `My IP` (Solo tú puedes entrar a la consola).
        *   **HTTP (Puerto 80):** Source: `Anywhere (0.0.0.0/0)` (Todo el mundo puede ver tu web).
        *   **HTTPS (Puerto 443):** Source: `Anywhere (0.0.0.0/0)`.
5.  **Instancia:**
    *   **Name:** `Servidor-PHP-Tienda`.
    *   **OS:** **Ubuntu 22.04 LTS**.
    *   **Key Pair:** Crea uno nuevo llamado `llave-aws.pem`, descárgalo y **no lo pierdas**.
6.  Pulsa **Launch Instance**.

---

## 2.5️⃣ ¿Cómo conectarse al servidor? (SSH)

Tienes dos formas de entrar a la "pantalla negra" de tu servidor:

### Opción A: Desde el navegador (La más fácil)
1. En el panel de AWS, selecciona tu instancia.
2. Pulsa el botón **Connect** arriba a la derecha.
3. Asegúrate de estar en la pestaña **EC2 Instance Connect**.
4. Pulsa el botón naranja **Connect**. ¡Se abrirá una terminal directamente en tu Chrome/Edge!

### Opción B: Desde tu PC (Windows PowerShell o CMD)
1. Abre la carpeta donde descargaste tu archivo `llave-aws.pem`.
2. Haz clic derecho en un espacio vacío y selecciona **Abrir en Terminal** (o PowerShell).
3. Copia la **IP Pública** de tu servidor desde el panel de AWS.
4. Escribe este comando (ajusta el nombre de tu llave):
   ```bash
   ssh -i "llave-aws.pem" ubuntu@TU_IP_PUBLICA
   ```
5. Si te sale un aviso de "Authenticity", escribe `yes` y pulsa Enter.

> **💡 Nota para Windows:** Si te da un error de "Permissions are too open", haz clic derecho en el archivo `.pem` -> Propiedades -> Seguridad -> Opciones avanzadas -> Deshabilitar herencia -> Quitar todos los permisos -> Agregar -> Selecciona tu usuario -> Dale Permisos de Lectura.

---

## 3️⃣ Fase 3: Instalación del Servidor (LAMP)

Conecta a tu servidor por SSH (desde el botón "Connect" de AWS o desde tu terminal) y pega estos comandos en orden:

```bash
# Actualizar el sistema e instalar Apache, PHP y MySQL
sudo apt update && sudo apt upgrade -y
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql php-gd unzip -y

# Dar permisos a tu usuario sobre la carpeta web para que Git pueda escribir
sudo chown -R ubuntu:www-data /var/www/html
sudo chmod -R 775 /var/www/html
```

---

## 4️⃣ Fase 4: Desplegar el código desde GitHub

Ahora, en lugar de subir archivos a mano, "traemos" el código de GitHub al servidor.

1.  Limpia la carpeta de Apache:
    ```bash
    cd /var/www/html
    rm index.html  # Borra la página por defecto de Apache
    ```
2.  Clona tu repositorio:
    ```bash
    git clone https://github.com/TU_USUARIO/tienda_segura.git .
    ```
    *(Nota: El punto final `.` hace que el código se descargue directamente en la carpeta actual).*

---

## 5️⃣ Fase 5: Configurar la Base de Datos

### 5.1 Crear la BD en el servidor
Entra en MySQL:
```bash
sudo mysql
```
Dentro de MySQL, pega esto:
```sql
CREATE DATABASE tienda_segura;
CREATE USER 'admin_tienda'@'localhost' IDENTIFIED BY 'PasswordSegura123!';
GRANT ALL PRIVILEGES ON tienda_segura.* TO 'admin_tienda'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5.2 Importar los datos
Como el archivo `.sql` se bajó con Git, simplemente impórtalo:
```bash
# Busca dónde está tu archivo SQL (por ejemplo en la carpeta 'database/')
sudo mysql -u admin_tienda -p tienda_segura < database/db.sql
# Escribe la contraseña: PasswordSegura123!
```

---

## 6️⃣ Fase 6: Ajustes finales

1.  **Conexión PHP:** Edita tu archivo de conexión para que use el usuario `admin_tienda` y la contraseña que creamos.
    ```bash
    nano includes/conexion.php
    ```
    Cambia los datos por:
    ```php
    $host = 'localhost';
    $db   = 'tienda_segura';
    $user = 'admin_tienda';
    $pass = 'PasswordSegura123!';
    ```
    *(Guarda con CTRL+O, Enter y sal con CTRL+X).*

2.  **Permisos de subida (uploads):** Si tu tienda permite subir imágenes de productos, dale permiso a Apache para escribir en esa carpeta:
    ```bash
    sudo chown -R www-data:www-data /var/www/html/uploads
    sudo chmod -R 777 /var/www/html/uploads
    ```

---

## 🚀 ¡Página en línea!
Busca la **IP Pública** de tu instancia en el panel de AWS y ponla en tu navegador. ¡Ya debería funcionar!

### 💡 Ventaja de usar este método:
Si mañana haces un cambio en tu código en Windows:
1. En tu PC haces: `git push`.
2. En AWS haces: `cd /var/www/html && git pull`.
3. **¡Los cambios se actualizan al instante sin subir archivos ZIP!**
