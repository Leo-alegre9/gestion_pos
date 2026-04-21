# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

BarPOS is a Point of Sale system for bars, built with **CodeIgniter 4** (PHP 8+) and **MariaDB** (via XAMPP). It manages tables (mesas), orders (pedidos), products, inventory, and user authentication.

## Common Commands

```bash
# Start local dev server
php spark serve

# Run all tests
php vendor/bin/phpunit

# Run a single test file
php vendor/bin/phpunit tests/app/SomeTest.php

# Run database migrations
php spark migrate

# Create a seeder
php spark make:seeder NombreSeeder

# Run seeders
php spark db:seed NombreSeeder
```

## Environment Setup

1. Copy `.env.example` to `.env` and configure:
   ```
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost:8080/'
   database.default.hostname = localhost
   database.default.database = bar_pos
   database.default.username = root
   database.default.password = ''
   ```
2. Import `bar_pos.sql` into MariaDB to create the schema.
3. Run `composer install`.
4. Run `php spark serve` and access `http://localhost:8080/auth/login`.

## Architecture

### Request Flow
All requests go through `public/index.php` → CI4 Router (`app/Config/Routes.php`) → Controller → Model → View.

Authentication state is stored in the PHP session. The session key `autenticado` (bool) controls access. Session also stores `id_usuario`, `nombre`, `apellido`, `email`, `username`, `id_rol`, `rol_nombre`.

**Important:** There are currently **no route filters enforcing authentication**. `app/Config/Filters.php` has no `filters` rules configured — all routes are publicly accessible. Adding a session-check filter to protected routes is pending.

### Controllers
Each controller extends `BaseController`. Models are instantiated in `__construct()` or `initController()`. Flash messages use `session()->set('success'|'error'|'errors', ...)` and redirect chains.

| Controller | Responsibility |
|---|---|
| `Auth` | Login (`/auth/login`), register (`/auth/register`), logout |
| `DashboardController` | Main dashboard — mesas data is real, KPI stats are hardcoded placeholders |
| `MesaController` | Table CRUD + state changes (`libre`/`ocupada`/`reservada`/`inactiva`) |
| `PedidoController` | Order lifecycle: create → add items → close; also historial |
| `ProductoController` | Product CRUD with soft-deactivation |
| `CategoriaController` | Product category CRUD with soft-deactivation |
| `InventarioController` | Stock CRUD and low-stock alerts |

### Models
All models extend CI4's `Model` with `$returnType = 'array'` and `$useTimestamps = false`.

| Model | Table | Notes |
|---|---|---|
| `UsuarioModel` | `usuarios` | Login via `validarLogin()` (accepts email or username); use `getErrores()` not `errors()` — see `$customErrors` pattern |
| `MesaModel` | `mesas` | States enforced by DB CHECK constraint |
| `PedidoModel` | `pedidos` | Open orders have `fecha_cierre = NULL`; `tipo_pedido` IN (`mesa`, `barra`, `take_away`) |
| `DetallePedidoModel` | `detalle_pedidos` | Order line items |
| `ProductoModel` | `productos` | `se_vende_en_barra` flag; soft-delete via `activo` field |
| `CategoriaProductoModel` | `categorias_productos` | Soft-delete via `activa` field |
| `StockModel` | `stock` | 1-to-1 with `productos`; alerts when `cantidad_disponible < cantidad_minima` |

### Database Schema (key relationships)
```
roles → usuarios → pedidos → detalle_pedidos → productos → categorias_productos
mesas → pedidos
estados_pedido → pedidos
stock → productos (1:1)
pagos → pedidos, metodos_pago
```

### Views
Views are plain PHP files under `app/Views/`. No shared layout system is in place — each view includes its own `<html>` structure with Bootstrap 5, Font Awesome 6, and Animate.css loaded from CDN. CSS variables for the color theme (brown/gold/cream) are defined inline in each view's `<style>` block.

## Known Issues / Pending Work

- **Dashboard KPIs are hardcoded** (`ventas_hoy`, `pedidos_hoy`, `alertas_stock`, `top_products`, `recent_orders`, `stock_alerts`). These need to be replaced with real DB queries once the full models are complete.
- **No authentication filter** — all routes are publicly accessible without being logged in.
- **Encoding issue** in `PedidoController` lines 203, 236, 245 — error strings have garbled UTF-8 characters (`vÃ¡lido`). These should be fixed to use UTF-8 literals directly.
- **`UsuarioModel` validation quirk** — the model's `$validationRules` has a `password` field but the DB column is `password_hash`. When inserting via the model, use `skipValidation(true)` and hash manually (as done in `Auth::store()`), or use `registrarUsuario()` which handles this correctly.
- **`DashboardController` reads session keys `user_name`/`user_role`** but `Auth::authenticate()` stores `nombre`/`rol_nombre`. The dashboard falls back to "Administrador"/"Admin" as defaults.
