# 📚 EXPLICACIÓN COMPLETA DEL PROYECTO - TIENDA SEGURA

## 🎯 ÍNDICE
1. [Introducción y Arquitectura General](#introducción-y-arquitectura-general)
2. [Base de Datos y Conexión](#base-de-datos-y-conexión)
3. [Sistema de Autenticación](#sistema-de-autenticación)
4. [Páginas Públicas](#páginas-públicas)
5. [Sistema de Carrito](#sistema-de-carrito)
6. [Panel de Administración](#panel-de-administración)
7. [Archivos de Acción (Actions)](#archivos-de-acción-actions)
8. [Archivos de Inclusión (Includes)](#archivos-de-inclusión-includes)
9. [Flujo de Datos Completo](#flujo-de-datos-completo)
10. [Seguridad Implementada](#seguridad-implementada)

---

## 1. INTRODUCCIÓN Y ARQUITECTURA GENERAL

### ¿Qué es este proyecto?
**Tienda Segura** es una aplicación web de comercio electrónico desarrollada en **PHP puro** (sin frameworks) que permite:
- A los **usuarios** navegar productos, añadirlos al carrito y realizar pedidos
- A los **administradores** gestionar productos, usuarios y ver logs del sistema

### Arquitectura del Proyecto
```
tienda_segura/
├── index.php                 # Página principal (catálogo)
├── producto.php              # Detalle de producto individual
├── carrito.php               # Vista del carrito de compras
├── finalizar_pedido.php      # Proceso de checkout
├── login.php                 # Inicio de sesión
├── registro.php              # Registro de nuevos usuarios
├── logout.php                # Cierre de sesión
├── mi_perfil.php             # Perfil del usuario
├── mis_pedidos.php           # Historial de pedidos
├── admin_usuarios.php        # Gestión de usuarios (admin)
│
├── includes/                 # Archivos reutilizables
│   ├── conexion.php          # Conexión a base de datos
│   ├── header.php            # Cabecera HTML común
│   ├── footer.php            # Pie de página común
│   ├── seguridad.php         # Protección de páginas de usuario
│   └── admin_auth.php        # Protección de páginas de admin
│
├── actions/                  # Procesamiento de formularios
│   ├── cart_add.php          # Añadir al carrito
│   ├── cart_clear.php        # Vaciar carrito
│   ├── admin_product_delete.php  # Eliminar producto
│   └── admin_user_delete.php     # Eliminar usuario
│
├── admin/                    # Panel de administración
│   ├── productos.php         # Lista de productos
│   ├── producto_editar.php   # Editar/crear producto
│   ├── usuarios.php          # Lista de usuarios
│   ├── usuario_editar.php    # Editar usuario
│   └── logs.php              # Registro de actividad
│
├── database/                 # Scripts SQL
│   └── schema.sql            # Estructura de la BD
│
└── uploads/                  # Imágenes de productos
```

### Tecnologías Utilizadas
- **Backend**: PHP 7.4+ (programación del lado del servidor)
- **Base de Datos**: MySQL/MariaDB (almacenamiento de datos)
- **Frontend**: HTML5, CSS3, JavaScript (interfaz de usuario)
- **Servidor**: Apache (XAMPP)
- **Seguridad**: Sesiones PHP, prepared statements, validación de datos

---

## 2. BASE DE DATOS Y CONEXIÓN

### 2.1 Estructura de la Base de Datos

La base de datos se llama `tienda_segura` y contiene 5 tablas principales:

#### Tabla: `usuarios`
```sql
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),        -- Hash bcrypt
    rol ENUM('user', 'admin'),
    fecha_registro TIMESTAMP
);
```
**Propósito**: Almacena información de usuarios y administradores.

#### Tabla: `productos`
```sql
CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(200),
    descripcion TEXT,
    precio DECIMAL(10,2),
    stock INT,
    imagen VARCHAR(255),
    categoria VARCHAR(100),
    fecha_creacion TIMESTAMP
);
```
**Propósito**: Catálogo de productos disponibles.

#### Tabla: `pedidos`
```sql
CREATE TABLE pedidos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    total DECIMAL(10,2),
    estado ENUM('pendiente', 'procesado', 'enviado', 'entregado'),
    fecha_pedido TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
```
**Propósito**: Registro de pedidos realizados.

#### Tabla: `detalle_pedidos`
```sql
CREATE TABLE detalle_pedidos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pedido_id INT,
    producto_id INT,
    cantidad INT,
    precio_unitario DECIMAL(10,2),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);
```
**Propósito**: Detalles de cada producto en un pedido.

#### Tabla: `logs`
```sql
CREATE TABLE logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    accion VARCHAR(255),
    detalles TEXT,
    ip VARCHAR(45),
    fecha TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
```
**Propósito**: Auditoría de acciones del sistema.

### 2.2 Archivo de Conexión: `includes/conexion.php`

```php
<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'tienda_segura');
define('DB_USER', 'root');
define('DB_PASS', '');

// Crear conexión usando mysqli
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer charset UTF-8 para caracteres especiales
$conn->set_charset("utf8mb4");
?>
```

**¿Cómo funciona?**
1. **Define constantes** con los datos de conexión
2. **Crea un objeto mysqli** que representa la conexión
3. **Verifica** si hay errores de conexión
4. **Establece UTF-8** para soportar caracteres especiales (ñ, acentos, etc.)

**¿Dónde se usa?**
Este archivo se incluye en **TODAS** las páginas que necesitan acceso a la base de datos:
```php
require_once 'includes/conexion.php';
```

---

## 3. SISTEMA DE AUTENTICACIÓN

### 3.1 Registro de Usuarios: `registro.php`

**Flujo de funcionamiento:**

```
Usuario completa formulario
        ↓
Envía datos por POST
        ↓
PHP valida los datos
        ↓
Verifica que el email no exista
        ↓
Hashea la contraseña con password_hash()
        ↓
Inserta en la base de datos
        ↓
Crea sesión automáticamente
        ↓
Redirige a index.php
```

**Código clave:**
```php
// Validación de datos
if (empty($_POST['nombre']) || empty($_POST['email']) || empty($_POST['password'])) {
    $error = "Todos los campos son obligatorios";
}

// Verificar email único
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $error = "El email ya está registrado";
}

// Hashear contraseña (NUNCA se guarda en texto plano)
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Insertar usuario
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'user')");
$stmt->bind_param("sss", $nombre, $email, $password_hash);
$stmt->execute();

// Crear sesión
$_SESSION['user_id'] = $conn->insert_id;
$_SESSION['user_nombre'] = $nombre;
$_SESSION['user_rol'] = 'user';
```

**Conceptos PHP importantes:**
- **`password_hash()`**: Encripta la contraseña de forma segura
- **`prepare()` y `bind_param()`**: Previenen inyección SQL
- **`$_SESSION`**: Array global que persiste datos entre páginas
- **`$_POST`**: Array con datos enviados por formulario

### 3.2 Inicio de Sesión: `login.php`

**Flujo de funcionamiento:**

```
Usuario ingresa email y contraseña
        ↓
PHP busca el usuario en la BD
        ↓
Verifica la contraseña con password_verify()
        ↓
Si es correcta, crea variables de sesión
        ↓
Redirige según el rol (admin → admin/productos.php, user → index.php)
```

**Código clave:**
```php
// Buscar usuario por email
$stmt = $conn->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $error = "Credenciales incorrectas";
} else {
    $usuario = $result->fetch_assoc();
    
    // Verificar contraseña hasheada
    if (password_verify($password, $usuario['password'])) {
        // Crear sesión
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_nombre'] = $usuario['nombre'];
        $_SESSION['user_rol'] = $usuario['rol'];
        
        // Redirigir según rol
        if ($usuario['rol'] === 'admin') {
            header("Location: admin/productos.php");
        } else {
            header("Location: index.php");
        }
    }
}
```

**Conceptos PHP importantes:**
- **`password_verify()`**: Compara contraseña ingresada con el hash almacenado
- **`fetch_assoc()`**: Convierte resultado SQL en array asociativo
- **`header("Location: ...")`**: Redirige a otra página

### 3.3 Protección de Páginas: `includes/seguridad.php`

**Propósito**: Evitar que usuarios no autenticados accedan a páginas protegidas.

```php
<?php
session_start();

// Si no hay sesión activa, redirigir a login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
```

**¿Dónde se usa?**
Al inicio de páginas como `mi_perfil.php`, `mis_pedidos.php`, `carrito.php`:
```php
require_once 'includes/seguridad.php';
```

### 3.4 Protección de Admin: `includes/admin_auth.php`

```php
<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
```

**¿Dónde se usa?**
Al inicio de todas las páginas en la carpeta `admin/`.

---

## 4. PÁGINAS PÚBLICAS

### 4.1 Página Principal: `index.php`

**Función**: Mostrar el catálogo de productos disponibles.

**Flujo de funcionamiento:**

```
Usuario accede a index.php
        ↓
PHP consulta todos los productos de la BD
        ↓
Genera HTML dinámicamente con un bucle foreach
        ↓
Muestra tarjetas de productos con imagen, nombre, precio
        ↓
Cada producto tiene botón "Añadir al carrito"
```

**Código clave:**
```php
<?php
require_once 'includes/conexion.php';
session_start();

// Consultar productos
$sql = "SELECT * FROM productos WHERE stock > 0 ORDER BY fecha_creacion DESC";
$result = $conn->query($sql);
?>

<!-- HTML -->
<div class="productos-grid">
    <?php while ($producto = $result->fetch_assoc()): ?>
        <div class="producto-card">
            <img src="uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
            <p class="precio">€<?php echo number_format($producto['precio'], 2); ?></p>
            <form action="actions/cart_add.php" method="POST">
                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                <button type="submit">Añadir al carrito</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>
```

**Conceptos PHP importantes:**
- **`while ($row = $result->fetch_assoc())`**: Itera sobre resultados SQL
- **`htmlspecialchars()`**: Previene XSS (inyección de código HTML/JS)
- **`number_format()`**: Formatea números (2 decimales)
- **Mezcla PHP-HTML**: PHP genera HTML dinámicamente

### 4.2 Detalle de Producto: `producto.php`

**Función**: Mostrar información completa de un producto específico.

**Flujo de funcionamiento:**

```
Usuario hace clic en un producto
        ↓
Se pasa el ID por URL (?id=5)
        ↓
PHP recibe el ID con $_GET['id']
        ↓
Consulta ese producto en la BD
        ↓
Muestra descripción completa, stock, etc.
```

**Código clave:**
```php
<?php
// Obtener ID del producto desde la URL
$producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consultar producto específico
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Producto no encontrado");
}

$producto = $result->fetch_assoc();
?>

<h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
<p><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
<p>Stock disponible: <?php echo $producto['stock']; ?> unidades</p>
```

**Conceptos PHP importantes:**
- **`$_GET['id']`**: Obtiene parámetros de la URL
- **`intval()`**: Convierte a entero (seguridad)
- **`nl2br()`**: Convierte saltos de línea en `<br>`

---

## 5. SISTEMA DE CARRITO

### 5.1 ¿Cómo funciona el carrito?

El carrito se almacena en **sesiones de PHP** (no en la base de datos hasta finalizar pedido).

**Estructura del carrito en sesión:**
```php
$_SESSION['carrito'] = [
    5 => [  // ID del producto
        'nombre' => 'Laptop Gaming',
        'precio' => 899.99,
        'cantidad' => 2,
        'imagen' => 'laptop.jpg'
    ],
    12 => [
        'nombre' => 'Mouse RGB',
        'precio' => 29.99,
        'cantidad' => 1,
        'imagen' => 'mouse.jpg'
    ]
];
```

### 5.2 Añadir al Carrito: `actions/cart_add.php`

**Flujo de funcionamiento:**

```
Usuario hace clic en "Añadir al carrito"
        ↓
Formulario envía producto_id por POST
        ↓
PHP consulta los datos del producto en la BD
        ↓
Añade/actualiza el producto en $_SESSION['carrito']
        ↓
Redirige de vuelta a la página anterior
```

**Código completo:**
```php
<?php
session_start();
require_once '../includes/conexion.php';

// Obtener ID del producto
$producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;

if ($producto_id > 0) {
    // Consultar datos del producto
    $stmt = $conn->prepare("SELECT nombre, precio, imagen FROM productos WHERE id = ? AND stock > 0");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
        
        // Inicializar carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        
        // Si el producto ya está en el carrito, incrementar cantidad
        if (isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id]['cantidad']++;
        } else {
            // Añadir nuevo producto al carrito
            $_SESSION['carrito'][$producto_id] = [
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => 1,
                'imagen' => $producto['imagen']
            ];
        }
    }
}

// Redirigir de vuelta
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
```

**Conceptos PHP importantes:**
- **`$_SESSION['carrito']`**: Array asociativo que persiste entre páginas
- **`$_SERVER['HTTP_REFERER']`**: URL de la página anterior
- **`isset()`**: Verifica si una variable existe

### 5.3 Ver Carrito: `carrito.php`

**Función**: Mostrar todos los productos del carrito con opciones para modificar cantidades.

**Código clave:**
```php
<?php
require_once 'includes/seguridad.php';
require_once 'includes/conexion.php';

$total = 0;

if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0):
    foreach ($_SESSION['carrito'] as $id => $item):
        $subtotal = $item['precio'] * $item['cantidad'];
        $total += $subtotal;
?>
        <tr>
            <td><?php echo htmlspecialchars($item['nombre']); ?></td>
            <td>€<?php echo number_format($item['precio'], 2); ?></td>
            <td><?php echo $item['cantidad']; ?></td>
            <td>€<?php echo number_format($subtotal, 2); ?></td>
        </tr>
<?php
    endforeach;
endif;
?>

<p>Total: €<?php echo number_format($total, 2); ?></p>
<a href="finalizar_pedido.php">Finalizar Pedido</a>
```

### 5.4 Vaciar Carrito: `actions/cart_clear.php`

```php
<?php
session_start();

// Eliminar el carrito de la sesión
unset($_SESSION['carrito']);

// Redirigir al carrito
header("Location: ../carrito.php");
exit();
?>
```

### 5.5 Finalizar Pedido: `finalizar_pedido.php`

**Flujo de funcionamiento:**

```
Usuario hace clic en "Finalizar Pedido"
        ↓
Verifica que esté logueado (si no, redirige a login)
        ↓
Calcula el total del carrito
        ↓
Inserta un registro en la tabla 'pedidos'
        ↓
Inserta cada producto en 'detalle_pedidos'
        ↓
Reduce el stock de cada producto
        ↓
Vacía el carrito de la sesión
        ↓
Muestra mensaje de confirmación
```

**Código clave:**
```php
<?php
require_once 'includes/seguridad.php';
require_once 'includes/conexion.php';

// Verificar que haya productos en el carrito
if (!isset($_SESSION['carrito']) || count($_SESSION['carrito']) === 0) {
    header("Location: carrito.php");
    exit();
}

// Calcular total
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

// Iniciar transacción (todo o nada)
$conn->begin_transaction();

try {
    // 1. Insertar pedido
    $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, 'pendiente')");
    $stmt->bind_param("id", $_SESSION['user_id'], $total);
    $stmt->execute();
    $pedido_id = $conn->insert_id;
    
    // 2. Insertar detalles y actualizar stock
    foreach ($_SESSION['carrito'] as $producto_id => $item) {
        // Insertar detalle
        $stmt = $conn->prepare("INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $pedido_id, $producto_id, $item['cantidad'], $item['precio']);
        $stmt->execute();
        
        // Reducir stock
        $stmt = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
        $stmt->bind_param("ii", $item['cantidad'], $producto_id);
        $stmt->execute();
    }
    
    // Confirmar transacción
    $conn->commit();
    
    // Vaciar carrito
    unset($_SESSION['carrito']);
    
    $mensaje = "Pedido realizado con éxito. Número de pedido: " . $pedido_id;
    
} catch (Exception $e) {
    // Revertir cambios si hay error
    $conn->rollback();
    $error = "Error al procesar el pedido";
}
?>
```

**Conceptos PHP importantes:**
- **Transacciones**: `begin_transaction()`, `commit()`, `rollback()`
  - Aseguran que TODAS las operaciones se completen o NINGUNA
- **`insert_id`**: Obtiene el ID del último registro insertado
- **Try-catch**: Manejo de errores

---

## 6. PANEL DE ADMINISTRACIÓN

### 6.1 Gestión de Productos: `admin/productos.php`

**Función**: Listar todos los productos con opciones para editar/eliminar.

**Código clave:**
```php
<?php
require_once '../includes/admin_auth.php';
require_once '../includes/conexion.php';

// Consultar todos los productos
$sql = "SELECT * FROM productos ORDER BY id DESC";
$result = $conn->query($sql);
?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($producto = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $producto['id']; ?></td>
                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                <td>€<?php echo number_format($producto['precio'], 2); ?></td>
                <td><?php echo $producto['stock']; ?></td>
                <td>
                    <a href="producto_editar.php?id=<?php echo $producto['id']; ?>">Editar</a>
                    <form action="../actions/admin_product_delete.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                        <button type="submit" onclick="return confirm('¿Eliminar este producto?')">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<a href="producto_editar.php">Añadir Nuevo Producto</a>
```

### 6.2 Editar/Crear Producto: `admin/producto_editar.php`

**Función**: Formulario para crear nuevos productos o editar existentes.

**Flujo de funcionamiento:**

```
Admin accede a producto_editar.php?id=5 (editar) o sin ID (crear)
        ↓
Si hay ID, PHP carga los datos del producto
        ↓
Muestra formulario prellenado (editar) o vacío (crear)
        ↓
Admin modifica datos y envía formulario
        ↓
PHP procesa el formulario:
  - Valida datos
  - Sube imagen si hay una nueva
  - INSERT (crear) o UPDATE (editar) en la BD
        ↓
Redirige a productos.php
```

**Código clave:**
```php
<?php
require_once '../includes/admin_auth.php';
require_once '../includes/conexion.php';

// Determinar si es edición o creación
$editar = isset($_GET['id']);
$producto = null;

if ($editar) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $categoria = $_POST['categoria'];
    
    // Manejar subida de imagen
    $imagen = $producto['imagen'] ?? 'default.jpg';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen = uniqid() . '.' . $extension;
        move_uploaded_file($_FILES['imagen']['tmp_name'], '../uploads/' . $imagen);
    }
    
    if ($editar) {
        // Actualizar producto existente
        $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, categoria=?, imagen=? WHERE id=?");
        $stmt->bind_param("ssdissi", $nombre, $descripcion, $precio, $stock, $categoria, $imagen, $id);
    } else {
        // Crear nuevo producto
        $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, categoria, imagen) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $nombre, $descripcion, $precio, $stock, $categoria, $imagen);
    }
    
    $stmt->execute();
    header("Location: productos.php");
    exit();
}
?>

<!-- Formulario HTML -->
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nombre" value="<?php echo $producto['nombre'] ?? ''; ?>" required>
    <textarea name="descripcion" required><?php echo $producto['descripcion'] ?? ''; ?></textarea>
    <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio'] ?? ''; ?>" required>
    <input type="number" name="stock" value="<?php echo $producto['stock'] ?? ''; ?>" required>
    <input type="text" name="categoria" value="<?php echo $producto['categoria'] ?? ''; ?>">
    <input type="file" name="imagen" accept="image/*">
    <button type="submit"><?php echo $editar ? 'Actualizar' : 'Crear'; ?> Producto</button>
</form>
```

**Conceptos PHP importantes:**
- **`$_FILES`**: Array con archivos subidos
- **`move_uploaded_file()`**: Mueve archivo temporal a destino final
- **`uniqid()`**: Genera nombre único para evitar colisiones
- **`enctype="multipart/form-data"`**: Necesario en formularios con archivos
- **Operador `??`**: Valor por defecto si la variable no existe

### 6.3 Eliminar Producto: `actions/admin_product_delete.php`

```php
<?php
require_once '../includes/admin_auth.php';
require_once '../includes/conexion.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id > 0) {
    // Obtener nombre de imagen para eliminarla
    $stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    
    // Eliminar producto de la BD
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // Eliminar imagen del servidor
    if ($producto && file_exists('../uploads/' . $producto['imagen'])) {
        unlink('../uploads/' . $producto['imagen']);
    }
}

header("Location: ../admin/productos.php");
exit();
?>
```

**Conceptos PHP importantes:**
- **`unlink()`**: Elimina archivos del servidor
- **`file_exists()`**: Verifica si un archivo existe

### 6.4 Gestión de Usuarios: `admin/usuarios.php`

Similar a `productos.php`, pero lista usuarios con opciones para editar/eliminar.

### 6.5 Logs del Sistema: `admin/logs.php`

**Función**: Mostrar registro de actividad del sistema (auditoría).

```php
<?php
require_once '../includes/admin_auth.php';
require_once '../includes/conexion.php';

// Consultar logs con información del usuario
$sql = "SELECT l.*, u.nombre, u.email 
        FROM logs l 
        LEFT JOIN usuarios u ON l.usuario_id = u.id 
        ORDER BY l.fecha DESC 
        LIMIT 100";
$result = $conn->query($sql);
?>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Acción</th>
            <th>IP</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($log = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $log['fecha']; ?></td>
                <td><?php echo htmlspecialchars($log['nombre'] ?? 'Sistema'); ?></td>
                <td><?php echo htmlspecialchars($log['accion']); ?></td>
                <td><?php echo $log['ip']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
```

---

## 7. ARCHIVOS DE ACCIÓN (ACTIONS)

Los archivos en la carpeta `actions/` procesan formularios y **nunca muestran HTML**. Siempre redirigen después de procesar.

### Patrón común:
```php
<?php
// 1. Iniciar sesión y conexión
session_start();
require_once '../includes/conexion.php';

// 2. Verificar permisos
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// 3. Procesar datos
$dato = $_POST['dato'];
// ... lógica de procesamiento ...

// 4. Registrar en logs (opcional)
$stmt = $conn->prepare("INSERT INTO logs (usuario_id, accion, ip) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $_SESSION['user_id'], $accion, $_SERVER['REMOTE_ADDR']);
$stmt->execute();

// 5. SIEMPRE redirigir
header("Location: ../pagina_destino.php");
exit();
?>
```

---

## 8. ARCHIVOS DE INCLUSIÓN (INCLUDES)

### 8.1 Header: `includes/header.php`

**Función**: Cabecera HTML común para todas las páginas.

```php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Tienda Segura'; ?></title>
    <link rel="stylesheet" href="/tienda_segura/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="/tienda_segura/index.php">Inicio</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/tienda_segura/carrito.php">Carrito</a>
                <a href="/tienda_segura/mis_pedidos.php">Mis Pedidos</a>
                <a href="/tienda_segura/mi_perfil.php">Mi Perfil</a>
                
                <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                    <a href="/tienda_segura/admin/productos.php">Admin</a>
                <?php endif; ?>
                
                <a href="/tienda_segura/logout.php">Cerrar Sesión</a>
                <span>Hola, <?php echo htmlspecialchars($_SESSION['user_nombre']); ?></span>
            <?php else: ?>
                <a href="/tienda_segura/login.php">Iniciar Sesión</a>
                <a href="/tienda_segura/registro.php">Registrarse</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
```

**Uso en páginas:**
```php
<?php
session_start();
$page_title = "Catálogo de Productos";
require_once 'includes/header.php';
?>
<!-- Contenido de la página -->
<?php require_once 'includes/footer.php'; ?>
```

### 8.2 Footer: `includes/footer.php`

```php
    </main>
    <footer>
        <p>&copy; 2026 Tienda Segura. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
```

---

## 9. FLUJO DE DATOS COMPLETO

### Ejemplo: Usuario realiza una compra

```
1. Usuario navega a index.php
   ├─ PHP consulta: SELECT * FROM productos
   └─ Muestra catálogo

2. Usuario hace clic en "Añadir al carrito"
   ├─ Formulario envía POST a actions/cart_add.php
   ├─ PHP consulta: SELECT nombre, precio FROM productos WHERE id = ?
   ├─ Añade a $_SESSION['carrito']
   └─ Redirige de vuelta

3. Usuario va a carrito.php
   ├─ PHP lee $_SESSION['carrito']
   └─ Muestra productos y total

4. Usuario hace clic en "Finalizar Pedido"
   ├─ Verifica autenticación (includes/seguridad.php)
   ├─ Si no está logueado → redirige a login.php
   └─ Si está logueado → continúa

5. finalizar_pedido.php procesa el pedido
   ├─ BEGIN TRANSACTION
   ├─ INSERT INTO pedidos (usuario_id, total)
   ├─ Para cada producto:
   │   ├─ INSERT INTO detalle_pedidos
   │   └─ UPDATE productos SET stock = stock - cantidad
   ├─ COMMIT
   ├─ unset($_SESSION['carrito'])
   └─ Muestra confirmación

6. Usuario puede ver su pedido en mis_pedidos.php
   ├─ SELECT * FROM pedidos WHERE usuario_id = ?
   └─ Muestra historial
```

---

## 10. SEGURIDAD IMPLEMENTADA

### 10.1 Prevención de Inyección SQL

**Problema**: Un atacante podría manipular consultas SQL.

**Solución**: Prepared Statements
```php
// ❌ INSEGURO (vulnerable a inyección SQL)
$sql = "SELECT * FROM usuarios WHERE email = '$email'";

// ✅ SEGURO (prepared statement)
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
```

### 10.2 Prevención de XSS (Cross-Site Scripting)

**Problema**: Un atacante podría inyectar código JavaScript.

**Solución**: `htmlspecialchars()`
```php
// ❌ INSEGURO
echo $nombre;

// ✅ SEGURO
echo htmlspecialchars($nombre);
```

### 10.3 Protección de Contraseñas

**Problema**: Nunca almacenar contraseñas en texto plano.

**Solución**: Hashing con bcrypt
```php
// Al registrar
$hash = password_hash($password, PASSWORD_BCRYPT);

// Al verificar
if (password_verify($password_ingresada, $hash_almacenado)) {
    // Contraseña correcta
}
```

### 10.4 Control de Acceso

**Problema**: Usuarios no autorizados accediendo a páginas protegidas.

**Solución**: Verificación de sesión
```php
// includes/seguridad.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// includes/admin_auth.php
if ($_SESSION['user_rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
```

### 10.5 Validación de Subida de Archivos

```php
// Verificar tipo de archivo
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
$extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

if (!in_array($extension, $allowed)) {
    die("Tipo de archivo no permitido");
}

// Verificar tamaño (máximo 5MB)
if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
    die("Archivo demasiado grande");
}
```

### 10.6 Protección CSRF (Cross-Site Request Forgery)

**Implementación con tokens:**
```php
// Generar token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// En formulario
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// Verificar al procesar
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Token CSRF inválido");
}
```

---

## 11. CONCEPTOS CLAVE DE PHP

### Variables y Tipos de Datos
```php
$nombre = "Juan";           // String
$edad = 25;                 // Integer
$precio = 19.99;            // Float
$activo = true;             // Boolean
$productos = [1, 2, 3];     // Array indexado
$usuario = [                // Array asociativo
    'nombre' => 'Juan',
    'email' => 'juan@email.com'
];
```

### Estructuras de Control
```php
// If-else
if ($edad >= 18) {
    echo "Mayor de edad";
} else {
    echo "Menor de edad";
}

// Foreach (iterar arrays)
foreach ($productos as $producto) {
    echo $producto['nombre'];
}

// While (iterar resultados SQL)
while ($row = $result->fetch_assoc()) {
    echo $row['nombre'];
}
```

### Funciones Comunes
```php
// Strings
strlen($texto)              // Longitud
strtolower($texto)          // A minúsculas
trim($texto)                // Eliminar espacios
htmlspecialchars($texto)    // Escapar HTML

// Arrays
count($array)               // Número de elementos
in_array($valor, $array)    // Verificar si existe
array_push($array, $valor)  // Añadir elemento

// Validación
isset($variable)            // ¿Existe?
empty($variable)            // ¿Está vacía?
is_numeric($valor)          // ¿Es número?
filter_var($email, FILTER_VALIDATE_EMAIL)  // Validar email
```

### Superglobales
```php
$_GET       // Datos de URL (?id=5)
$_POST      // Datos de formularios
$_SESSION   // Datos de sesión
$_FILES     // Archivos subidos
$_SERVER    // Información del servidor
$_COOKIE    // Cookies
```

---

## 12. GLOSARIO DE TÉRMINOS

| Término | Significado |
|---------|-------------|
| **PHP** | Lenguaje de programación del lado del servidor |
| **MySQL** | Sistema de gestión de bases de datos |
| **XAMPP** | Paquete que incluye Apache, PHP y MySQL |
| **Session** | Mecanismo para mantener datos entre páginas |
| **Prepared Statement** | Consulta SQL parametrizada (segura) |
| **Hash** | Encriptación unidireccional (contraseñas) |
| **CRUD** | Create, Read, Update, Delete (operaciones básicas) |
| **XSS** | Cross-Site Scripting (ataque de inyección de código) |
| **SQL Injection** | Ataque que manipula consultas SQL |
| **CSRF** | Cross-Site Request Forgery (ataque de falsificación) |
| **MVC** | Model-View-Controller (patrón de diseño) |
| **ORM** | Object-Relational Mapping (mapeo objeto-relacional) |

---

## 13. PREGUNTAS FRECUENTES

### ¿Por qué usar `require_once` en lugar de `require`?
`require_once` evita incluir el mismo archivo múltiples veces, lo que podría causar errores de redefinición.

### ¿Cuál es la diferencia entre `GET` y `POST`?
- **GET**: Datos visibles en la URL, para consultas (`?id=5`)
- **POST**: Datos ocultos, para formularios y acciones que modifican datos

### ¿Por qué usar `exit()` después de `header()`?
Para asegurar que el script se detenga y no ejecute código adicional después de la redirección.

### ¿Qué es `mysqli` vs `PDO`?
Ambos son formas de conectar a MySQL. `mysqli` es específico de MySQL, `PDO` soporta múltiples bases de datos.

### ¿Cómo depurar errores en PHP?
```php
// Mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Imprimir variables
var_dump($variable);
print_r($array);

// Detener ejecución y mostrar mensaje
die("Debug: " . $variable);
```

---

## 14. PRÓXIMOS PASOS Y MEJORAS

### Mejoras de Seguridad
- [ ] Implementar tokens CSRF en todos los formularios
- [ ] Añadir rate limiting en login
- [ ] Validar y sanitizar todas las entradas
- [ ] Implementar HTTPS

### Mejoras de Funcionalidad
- [ ] Sistema de búsqueda de productos
- [ ] Filtros por categoría y precio
- [ ] Sistema de valoraciones
- [ ] Pasarela de pago real (Stripe, PayPal)
- [ ] Envío de emails de confirmación

### Mejoras de Rendimiento
- [ ] Implementar caché
- [ ] Optimizar consultas SQL (índices)
- [ ] Lazy loading de imágenes
- [ ] Minificar CSS/JS

### Mejoras de UX
- [ ] Diseño responsive (móviles)
- [ ] Notificaciones en tiempo real
- [ ] Carrito persistente (guardar en BD)
- [ ] Recuperación de contraseña

---

## 15. RECURSOS ADICIONALES

### Documentación Oficial
- [PHP Manual](https://www.php.net/manual/es/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [MDN Web Docs](https://developer.mozilla.org/es/)

### Tutoriales Recomendados
- [W3Schools PHP](https://www.w3schools.com/php/)
- [PHP The Right Way](https://phptherightway.com/)
- [OWASP Security Guide](https://owasp.org/www-project-top-ten/)

---

**Documento creado para el proyecto Tienda Segura**  
**Fecha**: Febrero 2026  
**Versión**: 1.0

---

## 16. INFRAESTRUCTURA Y DESPLIEGUE (AVANZADO)

Este apartado explica el razonamiento arquitectónico de por qué el proyecto ha evolucionado de un entorno local (XAMPP) a una infraestructura profesional en la nube.

### 16.1 ¿Por qué AWS? (La Nube)
AWS proporciona una disponibilidad global y herramientas profesionales que un entorno local no puede ofrecer:
- **Disponibilidad:** La tienda es accesible desde cualquier parte del mundo 24/7.
- **Seguridad en Red:** Uso de VPC (Virtual Private Cloud) y Security Groups para aislar los servidores.
- **Realismo:** Demuestra el dominio de la gestión de recursos remotos y conectividad SSH, estándar en la industria.

### 16.2 ¿Por qué Docker? (Contenedores)
El uso de contenedores permite una gestión mucho más eficiente de los servicios:
- **Aislamiento:** Cada servicio (Grafana, Prometheus) corre en su propio entorno, evitando conflictos de dependencias.
- **Portabilidad:** Permite mover todo el stack de monitorización entre diferentes proveedores de nube simplemente copiando el archivo `docker-compose.yml`.
- **Rapidez:** Desplegar servicios complejos se reduce a un solo comando, garantizando que el entorno sea idéntico para todos los desarrolladores.

### 16.3 Observabilidad: Grafana, Prometheus y Loki
Para gestionar el sistema de forma profesional, no basta con que "funcione"; hay que saber **cómo** funciona en todo momento:
- **Prometheus (Métricas):** El "termómetro" del sistema. Mide el consumo de CPU, RAM y tráfico de red en tiempo real.
- **Loki (Logs):** La "caja negra". Centraliza todos los registros de errores de Apache, accesos y actividad del sistema para análisis posterior.
- **Grafana (Visualización):** El panel de control. Traduce los datos técnicos en gráficas intuitivas que permiten identificar problemas de un vistazo.

### 16.4 Seguridad Activa: Wazuh (SIEM)
Mientras la monitorización tradicional vigila el rendimiento, Wazuh vigila la integridad:
- **Detección de Amenazas:** Identifica ataques de fuerza bruta, escaneos de puertos o cambios no autorizados en archivos críticos.
- **Integración:** Envía alertas de seguridad directamente a Grafana, permitiendo una respuesta rápida ante incidentes.

### 16.5 Balanceo de Carga (Fase 4)
Para escalar el sistema y evitar puntos únicos de fallo:
- **HAProxy:** Actúa como la "puerta de entrada" (puerta giratoria) que reparte el tráfico entre varios servidores web.
- **Resiliencia:** Si uno de los servidores falla, el balanceador redirige automáticamente el tráfico a los nodos sanos, manteniendo la tienda siempre activa.

---

## 17. OBJETIVO FINAL Y FASE 4

### 17.1 El Objetivo Final: Proyecto SENTINEL
El propósito de este proyecto es demostrar la capacidad de diseñar y desplegar una infraestructura electrónica **e2e (end-to-end)** profesional, capaz de auto-protegerse, auto-vigilarse y mantenerse siempre operativa bajo estándares industriales.

**Hitos finales:**
1. **Redundancia:** Eliminación de puntos únicos de fallo mediante balanceo.
2. **Seguridad Proactiva:** SIEM operativo y políticas de Hardening aplicadas.
3. **Observabilidad:** Cuadros de mando ejecutivos para monitorización técnica y de seguridad.
4. **Integración:** Convergencia de Programación, Sistemas, Cloud y Seguridad.

### 17.2 Enfoque Fase 4: "De Prototipo a Fortaleza"

#### Pilares de ejecución:
1. **HA / Balanceo (Alta Disponibilidad):** Implementación de un **Load Balancer** (HAProxy) para distribuir el tráfico y ocultar la IP real de los servidores de backend.
2. **Hardening (Endurecimiento):** Reducción de la superficie de ataque mediante auditoría de puertos, cierre de servicios innecesarios y parcheo de seguridad.
3. **Pruebas de Rendimiento (Stress Testing):** Validación de la carga soportada y verificación de la respuesta de las alertas de monitorización bajo estrés.
4. **Automatización:** Refinado de scripts para asegurar la reproducibilidad total del entorno en minutos.


