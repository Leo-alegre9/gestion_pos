-- ============================================================
--  bar_pos — SQL MariaDB/MySQL
--  Una sola sucursal
--  Adaptado para XAMPP
-- ============================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS bar_pos;
USE bar_pos;

-- ------------------------------------------------------------
--  1. roles
-- ------------------------------------------------------------
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255),
    UNIQUE KEY uq_roles_nombre (nombre)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  2. usuarios
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_rol INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    dni INT NOT NULL,
    f_nacimiento DATE,
    username VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(120),
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_usuarios_username (username),
    UNIQUE KEY uq_usuarios_email (email),

    CONSTRAINT fk_usuarios_rol 
        FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  3. mesas
-- ------------------------------------------------------------
CREATE TABLE mesas (
    id_mesa    INT AUTO_INCREMENT PRIMARY KEY,
    numero     INT NOT NULL,
    capacidad  INT NULL,
    estado     VARCHAR(20) NOT NULL DEFAULT 'libre',
    
    CONSTRAINT uq_mesas_numero UNIQUE (numero),
    CONSTRAINT ck_mesas_estado CHECK (estado IN ('libre', 'ocupada', 'reservada', 'inactiva'))
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  4. categorias_productos
-- ------------------------------------------------------------
CREATE TABLE categorias_productos (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(100) NOT NULL,
    descripcion  VARCHAR(255) NULL,
    activa       TINYINT(1) NOT NULL DEFAULT 1,
    
    CONSTRAINT uq_categorias_nombre UNIQUE (nombre)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  5. productos
-- ------------------------------------------------------------
CREATE TABLE productos (
    id_producto       INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria      INT NOT NULL,
    nombre            VARCHAR(120) NOT NULL,
    descripcion       VARCHAR(255) NULL,
    precio_venta      DECIMAL(10,2) NOT NULL,
    se_vende_en_barra TINYINT(1) NOT NULL DEFAULT 1,
    controla_stock    TINYINT(1) NOT NULL DEFAULT 1,
    activo            TINYINT(1) NOT NULL DEFAULT 1,
    
    CONSTRAINT fk_productos_categoria FOREIGN KEY (id_categoria)
        REFERENCES categorias_productos (id_categoria)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  6. estados_pedido
-- ------------------------------------------------------------
CREATE TABLE estados_pedido (
    id_estado_pedido INT AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(30) NOT NULL,
    
    CONSTRAINT uq_estados_pedido_nombre UNIQUE (nombre)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  7. pedidos
-- ------------------------------------------------------------
CREATE TABLE pedidos (
    id_pedido        INT AUTO_INCREMENT PRIMARY KEY,
    id_mesa          INT NULL,
    id_usuario       INT NOT NULL,
    id_estado_pedido INT NOT NULL,
    tipo_pedido      VARCHAR(20) NOT NULL,
    fecha_apertura   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre     DATETIME NULL,
    observaciones    VARCHAR(255) NULL,
    
    CONSTRAINT ck_pedidos_tipo CHECK (tipo_pedido IN ('mesa', 'barra', 'take_away')),
    CONSTRAINT fk_pedidos_mesa FOREIGN KEY (id_mesa)
        REFERENCES mesas (id_mesa),
    CONSTRAINT fk_pedidos_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario),
    CONSTRAINT fk_pedidos_estado FOREIGN KEY (id_estado_pedido)
        REFERENCES estados_pedido (id_estado_pedido)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  8. detalle_pedidos
-- ------------------------------------------------------------
CREATE TABLE detalle_pedidos (
    id_detalle_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido         INT NOT NULL,
    id_producto       INT NOT NULL,
    cantidad          DECIMAL(10,2) NOT NULL,
    precio_unitario   DECIMAL(10,2) NOT NULL,
    subtotal          DECIMAL(12,2) NOT NULL,
    observaciones     VARCHAR(255) NULL,
    
    CONSTRAINT fk_detalle_pedido FOREIGN KEY (id_pedido)
        REFERENCES pedidos (id_pedido),
    CONSTRAINT fk_detalle_producto FOREIGN KEY (id_producto)
        REFERENCES productos (id_producto)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  9. metodos_pago
-- ------------------------------------------------------------
CREATE TABLE metodos_pago (
    id_metodo_pago INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(50) NOT NULL,
    activo         TINYINT(1) NOT NULL DEFAULT 1,
    
    CONSTRAINT uq_metodos_pago_nombre UNIQUE (nombre)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  10. pagos
-- ------------------------------------------------------------
CREATE TABLE pagos (
    id_pago        INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido      INT NOT NULL,
    id_metodo_pago INT NOT NULL,
    monto          DECIMAL(12,2) NOT NULL,
    fecha_pago     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_pagos_pedido FOREIGN KEY (id_pedido)
        REFERENCES pedidos (id_pedido),
    CONSTRAINT fk_pagos_metodo FOREIGN KEY (id_metodo_pago)
        REFERENCES metodos_pago (id_metodo_pago)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  11. stock (relación 1-1 con productos)
-- ------------------------------------------------------------
CREATE TABLE stock (
    id_stock              INT AUTO_INCREMENT PRIMARY KEY,
    id_producto           INT NOT NULL,
    cantidad_disponible   DECIMAL(10,2) NOT NULL DEFAULT 0,
    cantidad_minima       DECIMAL(10,2) NOT NULL DEFAULT 0,
    ultima_actualizacion  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT uq_stock_producto UNIQUE (id_producto),
    CONSTRAINT fk_stock_producto FOREIGN KEY (id_producto)
        REFERENCES productos (id_producto)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  12. movimientos_stock
-- ------------------------------------------------------------
CREATE TABLE movimientos_stock (
    id_movimiento   INT AUTO_INCREMENT PRIMARY KEY,
    id_producto     INT NOT NULL,
    id_pedido       INT NULL,
    tipo_movimiento VARCHAR(20) NOT NULL,
    cantidad        DECIMAL(10,2) NOT NULL,
    motivo          VARCHAR(100) NULL,
    fecha           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_usuario      INT NOT NULL,
    
    CONSTRAINT ck_mov_tipo CHECK (tipo_movimiento IN ('entrada', 'salida', 'ajuste')),
    CONSTRAINT fk_mov_producto FOREIGN KEY (id_producto)
        REFERENCES productos (id_producto),
    CONSTRAINT fk_mov_pedido FOREIGN KEY (id_pedido)
        REFERENCES pedidos (id_pedido),
    CONSTRAINT fk_mov_usuario FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
) ENGINE=InnoDB;