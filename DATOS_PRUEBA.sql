-- ========================================
-- SCRIPT DE PRUEBA - DATOS INICIALES
-- ========================================
-- 
-- Este script inserta:
-- 1. Roles base del sistema
-- 2. Usuario administrador de prueba
-- 3. Usuario regular de prueba
--
-- Ejecuta este script en tu BD para tener datos de prueba
-- ========================================

-- ========================================
-- 1. ROLES
-- ========================================

INSERT INTO roles (nombre, descripcion) VALUES 
    ('Administrador', 'Usuario con acceso total al sistema'),
    ('Usuario', 'Usuario regular del sistema'),
    ('Gerente', 'Usuario con acceso a reportes y gestión');

-- ========================================
-- 2. USUARIOS DE PRUEBA
-- ========================================

-- Usuario Admin: admin@gestion-pos.com / 123456
-- Contraseña hasheada con bcrypt: password_hash('123456', PASSWORD_BCRYPT)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO usuarios (id_rol, nombre, apellido, dni, f_nacimiento, username, password_hash, email, activo, fecha_creacion) 
VALUES 
    (1, 'Juan', 'Administrador', 12345678, '1990-01-15', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@gestion-pos.com', 1, NOW()),
    (2, 'Carlos', 'Usuario', 87654321, '1995-05-20', 'carlos.usuario', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'carlos@gestion-pos.com', 1, NOW()),
    (3, 'María', 'Gerente', 11223344, '1992-03-10', 'maria.gerente', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'maria@gestion-pos.com', 1, NOW());

-- ========================================
-- INSTRUCCIONES PARA GENERAR NUEVAS CONTRASEÑAS HASHEADAS
-- ========================================
--
-- Si necesitas generar una contraseña hasheada diferente a "123456", 
-- ejecuta este código PHP:
--
-- <?php
-- $password = "tu_contraseña_aqui";
-- $hash = password_hash($password, PASSWORD_BCRYPT);
-- echo "INSERT INTO usuarios (...) VALUES (..., '$hash', ...);";
-- ?>
--
-- Luego copia el hash en la columna password_hash
--
-- ========================================
-- DATOS DE PRUEBA PARA LOGIN
-- ========================================
--
-- Email: admin@gestion-pos.com
-- Usuario: admin
-- Contraseña: 123456
-- Rol: Administrador
--
-- Email: carlos@gestion-pos.com
-- Usuario: carlos.usuario
-- Contraseña: 123456
-- Rol: Usuario
--
-- Email: maria@gestion-pos.com
-- Usuario: maria.gerente
-- Contraseña: 123456
-- Rol: Gerente
--
-- ========================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- ========================================
--
-- Ejecuta estas consultas para verificar:
--
-- Verificar roles:
SELECT * FROM roles;
--
-- Verificar usuarios:
SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.username, r.nombre as rol FROM usuarios u 
JOIN roles r ON r.id_rol = u.id_rol;
--
-- Verificar usuario específico:
SELECT * FROM usuarios WHERE email = 'admin@gestion-pos.com';
--
-- ========================================
-- RESET DE DATOS (OPCIONAL)
-- ========================================
--
-- Si necesitas limpiar todo y empezar de nuevo:
--
-- DELETE FROM usuarios;
-- DELETE FROM roles;
-- ALTER TABLE usuarios AUTO_INCREMENT = 1;
-- ALTER TABLE roles AUTO_INCREMENT = 1;
--
-- Luego re-ejecuta este script.
--
-- ========================================
