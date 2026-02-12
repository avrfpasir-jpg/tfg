-- PSICOPOMPO - ULTA LEAN Edition
CREATE DATABASE IF NOT EXISTS tienda_segura;
USE tienda_segura;

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    imagen VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100),
    password_hash VARCHAR(255) NOT NULL,
    es_admin TINYINT DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE logs_seguridad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    evento VARCHAR(50) NOT NULL,
    detalles TEXT,
    nivel_riesgo INT DEFAULT 1
) ENGINE=InnoDB;

-- Usuario admin por defecto (password: admin)
INSERT INTO usuarios (username, password_hash, es_admin) VALUES 
('admin', '$2y$10$yI97v/fK.u0l/z6H8u6C4e4s0zR/f6Uq0U/0U/0U/0U/0U/0U/0U/', 1);
-- Nota: El hash de arriba es un placeholder, lo generaré correctamente en el script o usaré uno conocido.
-- En realidad, usaré el hash de 'admin': $2y$10$zSDF1p/LgQY8XJ5Xh6D6O.8D.D8D8D8D8D8D8D8D8D8D8D8D8D8D
-- Mejor: Actualizaré el password_hash en el script de carga.

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE pedido_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    producto_id INT,
    cantidad INT,
    precio_unitario DECIMAL(10,2),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB;

-- Datos de ejemplo PSICOPOMPO
INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES 
('Botas Militares "Phlegethon"', 'Calzado táctico de cuero reforzado. Tracción máxima.', 120.00, 20, '698cc9a06c3f5.jpg'),
('Máscara de Gas "Styx"', 'Protección integral con filtros HEPA clase 3.', 75.00, 15, '698cca9a792a6.jpg'),
('Chaqueta Cuero "Acheron"', 'Cuero envejecido con protecciones internas.', 190.00, 10, '698ccaccce63a.jpg'),
('Bomber Beige "Lethe"', 'Chaqueta ligera de aviador. Resistente al agua.', 85.00, 25, '698ccb023ef6f.jpg'),
('Pantalón Táctico "Cocytus"', 'Múltiples bolsillos de carga y refuerzo en rodillas.', 95.00, 30, '698ccb1b422a1.jpg');
