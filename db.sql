-- 1. Creación de la base de datos
CREATE DATABASE IF NOT EXISTS tienda_segura;
USE tienda_segura;

-- 2. Tabla de Roles (Catálogo)
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

-- 3. Tabla de Categorías (Negocio)
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- 4. Tabla de Productos (Negocio)
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    categoria_id INT,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. Tabla de Usuarios (Gestión + Honeytokens)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    rol_id INT,
    es_honeytoken BOOLEAN DEFAULT FALSE, -- Identificador de la trampa
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- 6. Tabla de Inteligencia de Red (Seguridad)
CREATE TABLE direcciones_ip (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(45) NOT NULL UNIQUE,
    pais VARCHAR(50) DEFAULT 'Desconocido',
    reputacion ENUM('limpia', 'sospechosa', 'bloqueada') DEFAULT 'limpia',
    ultima_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 7. Tabla de Logs de Seguridad (Honeypot)
CREATE TABLE logs_seguridad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_id INT,
    evento_tipo VARCHAR(50), -- Ej: 'SQL_INJECTION', 'BRUTE_FORCE', 'HONEYTOKEN'
    detalle TEXT,
    user_agent TEXT,
    nivel_alerta INT DEFAULT 1, -- Escala 1-15 (estilo Wazuh)
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ip_id) REFERENCES direcciones_ip(id)
) ENGINE=InnoDB;

-- 8. Tabla de Pedidos (Ventas)
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2),
    estado ENUM('pendiente', 'completado', 'cancelado') DEFAULT 'pendiente',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- 9. Tabla Detalle de Pedidos (Relación M:N)
CREATE TABLE pedido_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    producto_id INT,
    cantidad INT,
    precio_unitario DECIMAL(10,2),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB;

-- DATOS INICIALES NECESARIOS

-- Roles
INSERT INTO roles (nombre_rol) VALUES ('admin'), ('cliente');

-- Categorías
INSERT INTO categorias (nombre) VALUES ('Ropa'), ('Electrónica'), ('Muebles');

-- Productos Ejemplo
INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id) VALUES 
('Camiseta Básica', '100% Algodón, color blanco', 15.00, 100, 1),
('Auriculares Bluetooth', 'Cancelación de ruido activa', 59.99, 20, 2),
('Silla de Oficina', 'Ergonómica y ajustable', 120.50, 5, 3),
('Lámpara de Escritorio', 'LED con 3 modos de luz', 25.00, 15, 3),
('Zapatillas Deportivas', 'Para correr y caminar', 45.00, 30, 1);

-- IP Localhost
INSERT INTO direcciones_ip (ip, pais) VALUES ('::1', 'Local'), ('127.0.0.1', 'Local');
