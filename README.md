# PSICOPOMPO

Este proyecto es una aplicación web de comercio electrónico desarrollada en PHP con conexión a base de datos MySQL.

## Descripción

El objetivo de este proyecto es simular una tienda en línea con funcionalidades básicas como:
- Registro y autenticación de usuarios.
- Gestión de productos (CRUD) por parte de administradores.
- Visualización de productos.

## Tecnologías Utilizadas

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Base de Datos**: MySQL
- **Servidor Local**: XAMPP (Apache, MySQL)

## Instalación y Configuración

1.  Clona el repositorio en tu carpeta `htdocs` de XAMPP.
2.  Importa la base de datos (si existe un script `.sql` en `dbs/`).
3.  Configura la conexión en `conexion.php` si es necesario.
4.  Abre el navegador y ve a `http://localhost/tienda_segura`.

## Estructura del Proyecto

- `/admin`: Archivos relacionados con el panel de administración.
- `/includes`: Archivos PHP reutilizables (cabeceras, pies de página, autenticación).
- `/dbs`: Scripts de base de datos.
- `/config`: Archivos de configuración.
