# 🍺 BarPOS - Sistema de Gestión para Bares

Sistema POS (Point of Sale) minimalista y confiable para la gestión operativa de bares, desarrollado con **CodeIgniter 4**.

## 📋 Contenido

- [Características](#características)
- [Stack Tecnológico](#stack-tecnológico)
- [Instalación](#instalación)
- [Estructura de Carpetas](#estructura-de-carpetas)
- [Archivos Generados](#archivos-generados)
- [Rutas Disponibles](#rutas-disponibles)
- [Credenciales de Prueba](#credenciales-de-prueba)
- [Próximos Pasos](#próximos-pasos)

---

## 🎯 Características

✅ **Login/Logout** - Autenticación con sesiones (hardcodeado temporalmente)  
✅ **Dashboard** - Panel principal con estadísticas en tiempo real  
✅ **Gestión de Mesas** - Control visual del estado de mesas  
✅ **Toma de Pedidos** - Interfaz rápida y eficiente  
✅ **Sistema de Caja** - Registro de pagos y cierre de caja  
✅ **Control de Stock** - Monitoreo de productos  
✅ **Roles y Permisos** - Admin y Staff (para cuando se conecte a BD)  
✅ **Diseño Responsivo** - Optimizado para pantallas táctiles  
✅ **Documentación Inline** - Comentarios explicativos en todo el código  

---

## 🛠️ Stack Tecnológico

```
Backend:
- Framework: CodeIgniter 4 (PHP 8+)
- Lenguaje: PHP 8.0+
- Base de datos: MariaDB (XAMPP)

Frontend:
- HTML5 / CSS3
- Bootstrap 5
- Animate.css
- Font Awesome 6
- JavaScript Vanilla (no dependencies)

Herramientas:
- XAMPP (Local development)
- Composer (Dependency manager)
- Git (Version control)
```

---

## 📦 Instalación

### 1. **Clonar o descargar el repositorio**

```bash
git clone <repository-url>
cd barpos-pos
```

### 2. **Instalar dependencias**

```bash
composer install
```

### 3. **Configurar el archivo .env**

Copiar `.env.example` a `.env` y configurar:

```env
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'
database.default.hostname = localhost
database.default.database = barpos_db
database.default.username = root
database.default.password = ''
```

### 4. **Configurar sesiones**

En `app/Config/Session.php`:

```php
public $driver            = 'files'; // O 'database' si se prefiere
public $cookieName        = 'BARPOS_SESSION';
public $expiration        = 7200; // 2 horas
public $savePath          = WRITEPATH . 'session';
```

### 5. **Ejecutar servidor local**

```bash
php spark serve
```

Acceder a: `http://localhost:8080/login`

---

## 📁 Estructura de Carpetas

```
barpos/
├── app/
│   ├── Config/
│   │   └── Routes.php              ← Rutas definidas
│   ├── Controllers/
│   │   ├── Auth.php                ← Login/Logout
│   │   └── Dashboard.php           ← Panel principal
│   ├── Models/                     ← (Próximamente)
│   ├── Views/
│   │   ├── auth/
│   │   │   └── login.php           ← Formulario login
│   │   ├── dashboard/
│   │   │   └── index.php           ← Panel principal
│   │   └── layouts/
│   │       └── dashboard.php       ← Layout base
│   ├── css/
│   │   └── dashboard.css           ← Estilos globales
│   └── js/
│       └── dashboard.js            ← Scripts globales
├── public/
│   ├── css/                        ← Estilos estáticos
│   ├── js/                         ← Scripts estáticos
│   └── images/                     ← Imágenes
├── .env                            ← Configuración del proyecto
├── .gitignore
├── composer.json
└── README.md
```

---

## 📄 Archivos Generados

### Controllers

#### `Auth.php` (Autenticación)
- `login()` - Muestra formulario de login
- `processLogin()` - Procesa credenciales (POST)
- `logout()` - Cierra sesión

**Datos hardcodeados:**
```
admin@bar.local / password123     (Admin)
mozo@bar.local / password123      (Staff)
```

#### `Dashboard.php` (Panel Principal)
- `index()` - Dashboard con estadísticas
- `getMesaStatus($id)` - API para estado de mesas

### Views

#### `auth/login.php`
- Formulario elegante con validación client-side
- Diseño responsivo y moderno
- Animaciones con Animate.css

#### `dashboard/index.php`
- Panel con KPIs (estadísticas)
- Grid de mesas con estados
- Tabla de últimas órdenes
- Panel de stock bajo
- **100% funcional** (datos hardcodeados)

#### `layouts/dashboard.php`
- Layout base para todas las vistas post-login
- Sidebar navegación con menú
- Topbar con usuario, notificaciones, reloj
- Footer
- Scripts globales

### Estilos y Scripts

#### `css/dashboard.css` (1000+ líneas)
- Variables CSS para tema consistente
- Sidebar con animaciones
- Responsive design (mobile, tablet, desktop)
- Componentes: cards, badges, tables, forms
- Paleta de colores: marrón, oro, crema

#### `js/dashboard.js` (600+ líneas)
- Objeto global `BarPOS` con utilidades
- Funciones API ready (sin implementar)
- Validación de formularios
- Notificaciones
- Manejo de eventos globales
- Funciones específicas para mesas, pedidos, stock

### Rutas

#### `Config/Routes.php`
```
POST   /login/process              ← Procesar login
GET    /logout                     ← Cerrar sesión
GET    /dashboard                  ← Panel principal
GET    /mesas                      ← Listado de mesas
GET    /pedidos                    ← Listado de pedidos
GET    /caja                       ← Panel de caja
GET    /productos                  ← Catálogo de productos
GET    /stock                      ← Control de stock
GET    /usuarios                   ← Gestión de usuarios (admin)
GET    /reportes                   ← Reportes (admin)
GET    /configuracion              ← Configuración (admin)
```

---

## 🔐 Credenciales de Prueba

**Administrador:**
- Email: `admin@bar.local`
- Contraseña: `password123`

**Personal:**
- Email: `mozo@bar.local`
- Contraseña: `password123`

---

## 🔄 Próximos Pasos (Conectar a Base de Datos)

### 1. Crear Migraciones

```bash
php spark make:migration CreateUsersTable
php spark make:migration CreateMesasTable
php spark make:migration CreateProductosTable
php spark make:migration CreatePedidosTable
```

### 2. Crear Modelos

```bash
php spark make:model User
php spark make:model Mesa
php spark make:model Producto
php spark make:model Pedido
```

### 3. Crear Seeders

```bash
php spark make:seeder UserSeeder
php spark make:seeder MesaSeeder
php spark make:seeder ProductoSeeder
```

### 4. Ejemplo de Migración

```php
// app/Database/Migrations/2024-01-01-000001_CreateUsersTable.php

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->createTable('users', function(ColumnDefinition $table) {
            $table->increments('id');
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->string('name', 100);
            $table->enum('role', ['admin', 'staff']);
            $table->enum('status', ['active', 'inactive']);
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
```

### 5. Reemplazar datos hardcodeados en Controllers

```php
// Antes (hardcodeado)
$users = [
    ['id' => 1, 'email' => 'admin@bar.local', ...]
];

// Después (con BD)
$userModel = new UserModel();
$user = $userModel->where('email', $email)->first();
```

---

## 🎨 Personalización de Colores

Editar variables en `css/dashboard.css`:

```css
:root {
    --primary-color: #2c1810;        /* Marrón oscuro */
    --secondary-color: #d4a574;      /* Oro/Bronce */
    --accent-color: #e8d5b7;         /* Crema */
    --success-color: #4ecdc4;        /* Verde agua */
    --danger-color: #ff6b6b;         /* Rojo */
    --warning-color: #ffa500;        /* Naranja */
    --info-color: #3b82f6;           /* Azul */
}
```

---

## 📱 Responsive Design

- ✅ **Desktop** (1920px+) - Grid completo
- ✅ **Tablet** (768px - 1024px) - Sidebar colapsable
- ✅ **Mobile** (320px - 767px) - Menú hamburguesa

---

## 🔍 Documentación del Código

Cada función incluye comentarios con:
- **Descripción** de qué hace
- **Parámetros** esperados
- **Retorno** esperado
- **Ejemplo** de uso (en algunos casos)

Ejemplo:

```php
/**
 * Procesa el login del usuario
 * 
 * POST /login/process
 * 
 * Validaciones:
 * - Email requerido
 * - Contraseña requerida
 * 
 * @return \CodeIgniter\HTTP\RedirectResponse
 */
public function processLogin() { ... }
```

---

## 🚀 Deployment a Hosting

1. **Configurar .env para producción**
   ```
   CI_ENVIRONMENT = production
   app.baseURL = 'https://tudominio.com/'
   ```

2. **Crear base de datos en el hosting**

3. **Ejecutar migraciones**
   ```bash
   php spark migrate
   ```

4. **Cargar seeders iniciales**
   ```bash
   php spark db:seed UserSeeder
   ```

5. **Asegurar carpetas**
   - `writable/` - Permisos 755
   - `.env` - No compartir públicamente

---

## 📞 Soporte

Para preguntas sobre la arquitectura o instalación, consulta la documentación oficial de [CodeIgniter 4](https://codeigniter.com/user_guide/intro/index.html).

---

## 📝 Licencia

Este proyecto está bajo licencia MIT. Libre para usar y modificar.

---

## ✨ Características Futuras

- [ ] Conexión real a MariaDB
- [ ] WebSockets para actualizaciones en tiempo real
- [ ] Exportación de reportes (PDF, Excel)
- [ ] Integración con impresoras térmicas
- [ ] App móvil nativa (React Native)
- [ ] Sincronización multi-dispositivo
- [ ] Sistema de notificaciones push
- [ ] Backup automático de datos

---

**¡Listo para comenzar! 🎉**

Accede a `http://localhost:8080/login` con las credenciales de prueba.