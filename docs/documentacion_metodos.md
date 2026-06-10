# Documentación de Métodos — BarPOS

> Generado el 2026-05-21. Cubre Controllers, Models y Services del directorio `app/`.

---

## Tabla de contenidos

- [Controllers](#controllers)
  - [BaseController](#basecontroller)
  - [Home](#home)
  - [Auth](#auth)
  - [DashboardController](#dashboardcontroller)
  - [MesaController](#mesacontroller)
  - [PedidoController](#pedidocontroller)
  - [ProductoController](#productocontroller)
  - [CategoriaController](#categoriacontroller)
  - [InventarioController](#inventariocontroller)
  - [FacturacionController](#facturacioncontroller)
- [Models](#models)
  - [UsuarioModel](#usuariomodel)
  - [MesaModel](#mesamodel)
  - [PedidoModel](#pedomodel)
  - [ProductoModel](#productomodel)
  - [CategoriaProductoModel](#categoriaproductomodel)
  - [StockModel](#stockmodel)
  - [PagoModel](#pagomodel)
  - [DetallePedidoModel](#detallepedidomodel)
  - [MetodoPagoModel](#metodopagomodel)
- [Services](#services)
  - [ComprobanteService](#comprobanteservice)
  - [PrepararPagoService](#prepararpAgoservice)
  - [RegistrarPagoService](#registrarpagoservice)

---

## Controllers

### BaseController

**Archivo:** `app/Controllers/BaseController.php`  
Clase abstracta base de la que extienden todos los controladores.

---

#### `initController`

```php
public function initController(
    RequestInterface $request,
    ResponseInterface $response,
    LoggerInterface $logger
): void
```

Inicializa el controlador base invocando al padre de CI4. Punto de extensión para pre-cargar helpers y servicios globales.

| Parámetro  | Tipo                | Descripción                    |
|------------|---------------------|--------------------------------|
| `$request` | `RequestInterface`  | Objeto de solicitud HTTP       |
| `$response`| `ResponseInterface` | Objeto de respuesta HTTP       |
| `$logger`  | `LoggerInterface`   | Instancia del logger           |

**Retorna:** `void`

---

### Home

**Archivo:** `app/Controllers/Home.php`  
Controlador de la página de inicio (landing page).

---

#### `index`

```php
public function index(): string
```

Renderiza la vista de la landing page pública (`views/landing.php`).

**Retorna:** `string` — HTML de la vista.

---

### Auth

**Archivo:** `app/Controllers/Auth.php`  
Gestiona el flujo completo de autenticación: login, registro y logout.

---

#### `initController`

```php
public function initController(
    RequestInterface $request,
    ResponseInterface $response,
    LoggerInterface $logger
): void
```

Extiende el `initController` del padre para instanciar `UsuarioModel`.

| Parámetro  | Tipo                | Descripción              |
|------------|---------------------|--------------------------|
| `$request` | `RequestInterface`  | Objeto de solicitud HTTP |
| `$response`| `ResponseInterface` | Objeto de respuesta HTTP |
| `$logger`  | `LoggerInterface`   | Instancia del logger     |

**Retorna:** `void`

---

#### `login`

```php
public function login(): \CodeIgniter\HTTP\RedirectResponse|string
```

Muestra el formulario de login. Si el usuario ya está autenticado (sesión activa), redirige al dashboard.

**Retorna:** Vista `auth/login` o redirección a `/dashboard`.

---

#### `authenticate`

```php
public function authenticate(): \CodeIgniter\HTTP\RedirectResponse
```

Procesa el formulario de login (solo acepta `POST`). Valida credenciales contra `UsuarioModel::validarLogin()`, establece la sesión con los datos del usuario y redirige al dashboard.

Datos que almacena en sesión: `id_usuario`, `nombre`, `apellido`, `email`, `username`, `id_rol`, `rol_nombre`, `autenticado`.

**Retorna:** Redirección a `/dashboard` con mensaje de éxito, o de vuelta al login con mensaje de error.

---

#### `register`

```php
public function register(): \CodeIgniter\HTTP\RedirectResponse|string
```

Muestra el formulario de registro. Si el usuario ya está autenticado, redirige al dashboard.

**Retorna:** Vista `auth/register` o redirección a `/dashboard`.

---

#### `store`

```php
public function store(): \CodeIgniter\HTTP\RedirectResponse
```

Procesa el formulario de registro de nuevo usuario (solo acepta `POST`). Realiza validaciones manuales de campos obligatorios, coincidencia de contraseñas y unicidad de DNI, username y email antes de insertar. Hashea la contraseña con `PASSWORD_BCRYPT` y asigna el rol `id_rol = 1` por defecto.

**Retorna:** Redirección a `/auth/login` con éxito, o de vuelta al formulario con el error correspondiente.

---

#### `logout`

```php
public function logout(): \CodeIgniter\HTTP\RedirectResponse
```

Destruye la sesión activa y redirige a la raíz del sitio con mensaje de confirmación.

**Retorna:** Redirección a `/` con mensaje de éxito.

---

### DashboardController

**Archivo:** `app/Controllers/DashboardController.php`  
Controlador del panel principal del sistema.

---

#### `index`

```php
public function index(): string
```

Construye y renderiza el dashboard completo. Consulta directamente la base de datos para obtener:

- Estado y montos actuales de todas las mesas (pedidos abiertos).
- Ventas del día (suma de pagos).
- Cantidad de pedidos abiertos hoy.
- Contador de alertas de stock bajo.
- Top 5 productos más vendidos en el día.
- Últimos 8 pedidos del día con su estado (abierto / pagado / sin cobrar).
- Detalle de hasta 6 productos con stock crítico o bajo.

**Retorna:** `string` — HTML de la vista `dashboard`.

---

### MesaController

**Archivo:** `app/Controllers/MesaController.php`  
CRUD de mesas y cambio de estado.

---

#### `mostrarResumen`

```php
public function mostrarResumen(): string
```

Lista todas las mesas con su estado actual e indica si tienen un pedido activo asociado. También incluye un resumen de conteo por estado (`libre`, `ocupada`, `reservada`, `inactiva`).

**Retorna:** `string` — HTML de la vista `mesas`.

---

#### `cambiarEstado`

```php
public function cambiarEstado(int $idMesa): \CodeIgniter\HTTP\RedirectResponse
```

Actualiza el estado de una mesa específica. Valida que el estado sea uno de los valores permitidos y bloquea el cambio a `libre` o `inactiva` si la mesa tiene un pedido activo.

| Parámetro | Tipo  | Descripción              |
|-----------|-------|--------------------------|
| `$idMesa` | `int` | ID de la mesa a modificar |

**Retorna:** Redirección a `/mesas` con mensaje de éxito o error.

---

#### `crearMesa`

```php
public function crearMesa(): string
```

Muestra el formulario para crear una nueva mesa. Calcula y pre-llena el próximo número de mesa disponible (máximo actual + 1).

**Retorna:** `string` — HTML de la vista `mesas_crear`.

---

#### `validarYalmacenar`

```php
public function validarYalmacenar(): \CodeIgniter\HTTP\RedirectResponse
```

Valida los datos del formulario con las reglas del modelo y persiste la nueva mesa. El estado inicial siempre es `libre`.

**Retorna:** Redirección a `/mesas` con mensaje de éxito o error.

---

#### `eliminarMesa`

```php
public function eliminarMesa(int $idMesa): \CodeIgniter\HTTP\RedirectResponse
```

Elimina físicamente una mesa de la base de datos. Bloquea la eliminación si la mesa tiene un pedido activo.

| Parámetro | Tipo  | Descripción               |
|-----------|-------|---------------------------|
| `$idMesa` | `int` | ID de la mesa a eliminar  |

**Retorna:** Redirección a `/mesas` con mensaje de éxito o error.

---

### PedidoController

**Archivo:** `app/Controllers/PedidoController.php`  
Ciclo de vida completo de los pedidos.

---

#### `mostrarResumen`

```php
public function mostrarResumen(): string
```

Lista todos los pedidos activos (sin fecha de cierre), junto con un resumen por tipo y los pedidos cerrados que aún no tienen pago registrado.

**Retorna:** `string` — HTML de la vista `pedidos/index`.

---

#### `crearPedido`

```php
public function crearPedido(): string
```

Muestra el formulario para crear un nuevo pedido. Carga las mesas disponibles (estado `libre`) y los productos activos para seleccionar ítems.

**Retorna:** `string` — HTML de la vista `pedidos/crear`.

---

#### `validarYguardarPedido`

```php
public function validarYguardarPedido(): \CodeIgniter\HTTP\RedirectResponse
```

Valida los datos del formulario y persiste el pedido con sus ítems dentro de una transacción. Verifica sesión activa, que la mesa esté libre (si aplica) y las reglas del modelo. Al finalizar, marca la mesa como `ocupada`.

**Retorna:** Redirección a `/pedidos/detalles/{id}` con éxito, o al formulario con error.

---

#### `mostrarPedidoConDetalles`

```php
public function mostrarPedidoConDetalles(int $idPedido): \CodeIgniter\HTTP\RedirectResponse|string
```

Muestra el detalle de un pedido: información del encabezado, ítems actuales, productos disponibles para agregar y estado de pago.

| Parámetro   | Tipo  | Descripción         |
|-------------|-------|---------------------|
| `$idPedido` | `int` | ID del pedido       |

**Retorna:** `string` — HTML de la vista `pedidos/detalles`, o redirección con error si el pedido no existe.

---

#### `agregarDetalle`

```php
public function agregarDetalle(int $idPedido): \CodeIgniter\HTTP\RedirectResponse
```

Agrega un ítem (producto + cantidad) al pedido indicado. Rechaza la operación si el pedido ya está cerrado o si el producto no existe.

| Parámetro   | Tipo  | Descripción                           |
|-------------|-------|---------------------------------------|
| `$idPedido` | `int` | ID del pedido al que se agrega el ítem |

**Retorna:** Redirección al detalle del pedido con mensaje de éxito o error.

---

#### `eliminarDetalle`

```php
public function eliminarDetalle(int $idPedido, int $idDetalle): \CodeIgniter\HTTP\RedirectResponse
```

Elimina un ítem del pedido. Valida que el pedido esté abierto y que el detalle pertenezca al pedido indicado.

| Parámetro    | Tipo  | Descripción                    |
|--------------|-------|--------------------------------|
| `$idPedido`  | `int` | ID del pedido                  |
| `$idDetalle` | `int` | ID del detalle a eliminar      |

**Retorna:** Redirección al detalle del pedido con mensaje de éxito o error.

---

#### `cerrarPedido`

```php
public function cerrarPedido(int $idPedido): \CodeIgniter\HTTP\RedirectResponse
```

Cierra un pedido registrando la fecha y hora de cierre y actualizando su estado a `cerrado`. Si tiene mesa asociada, la cambia a `libre`.

| Parámetro   | Tipo  | Descripción          |
|-------------|-------|----------------------|
| `$idPedido` | `int` | ID del pedido        |

**Retorna:** Redirección al detalle del pedido con éxito o error.

---

#### `reabrirPedido`

```php
public function reabrirPedido(int $idPedido): \CodeIgniter\HTTP\RedirectResponse
```

Reabre un pedido cerrado, siempre que no tenga un pago registrado. Borra la fecha de cierre, cambia el estado a `abierto` y, si tiene mesa, la vuelve a `ocupada`.

| Parámetro   | Tipo  | Descripción          |
|-------------|-------|----------------------|
| `$idPedido` | `int` | ID del pedido        |

**Retorna:** Redirección al detalle del pedido con éxito o error.

---

#### `mostrarHistorialDePedidos`

```php
public function mostrarHistorialDePedidos(): string
```

Muestra el historial de pedidos cerrados en una fecha específica (por defecto la fecha actual). La fecha se recibe como query string `?fecha=Y-m-d`.

**Retorna:** `string` — HTML de la vista `pedidos/historial`.

---

#### `getEstadoId` *(private)*

```php
private function getEstadoId(string $nombre): int
```

Busca el `id_estado_pedido` de un estado por su nombre en la tabla `estados_pedido`. Si no existe, lo inserta y devuelve el ID generado. Garantiza la integridad de la FK de estado sin requerir seeders manuales.

| Parámetro | Tipo     | Descripción                            |
|-----------|----------|----------------------------------------|
| `$nombre` | `string` | Nombre del estado (ej. `'abierto'`, `'cerrado'`) |

**Retorna:** `int` — ID del estado.

---

### ProductoController

**Archivo:** `app/Controllers/ProductoController.php`  
CRUD de productos con soft-deactivation.

---

#### `mostrarResumen`

```php
public function mostrarResumen(): string
```

Lista todos los productos (activos e inactivos) con su categoría asociada.

**Retorna:** `string` — HTML de la vista `productos/index`.

---

#### `crearProducto`

```php
public function crearProducto(): string
```

Muestra el formulario para crear un nuevo producto. Carga las categorías activas disponibles.

**Retorna:** `string` — HTML de la vista `productos/crear`.

---

#### `validarYalmacenar`

```php
public function validarYalmacenar(): \CodeIgniter\HTTP\RedirectResponse
```

Valida los datos del formulario y persiste el nuevo producto con `activo = 1`. Sanitiza los campos antes de insertar usando las reglas del modelo.

**Retorna:** Redirección a `/productos` con éxito, o al formulario con errores.

---

#### `editarProducto`

```php
public function editarProducto(int $idProducto): \CodeIgniter\HTTP\RedirectResponse|string
```

Muestra el formulario de edición de un producto existente con sus datos pre-cargados.

| Parámetro     | Tipo  | Descripción             |
|---------------|-------|-------------------------|
| `$idProducto` | `int` | ID del producto a editar |

**Retorna:** `string` — HTML de la vista `productos/editar`, o redirección con error si no existe.

---

#### `actualizarProducto`

```php
public function actualizarProducto(int $idProducto): \CodeIgniter\HTTP\RedirectResponse
```

Valida y persiste los cambios sobre un producto existente. Excluye el propio registro de la verificación `is_unique` del modelo.

| Parámetro     | Tipo  | Descripción                  |
|---------------|-------|------------------------------|
| `$idProducto` | `int` | ID del producto a actualizar |

**Retorna:** Redirección a `/productos` con éxito o errores.

---

#### `desactivarProducto`

```php
public function desactivarProducto(int $idProducto): \CodeIgniter\HTTP\RedirectResponse
```

Desactiva un producto (soft-delete) cambiando `activo = 0` sin eliminar el registro físicamente de la base de datos.

| Parámetro     | Tipo  | Descripción                  |
|---------------|-------|------------------------------|
| `$idProducto` | `int` | ID del producto a desactivar |

**Retorna:** Redirección a `/productos` con mensaje de éxito o error.

---

### CategoriaController

**Archivo:** `app/Controllers/CategoriaController.php`  
CRUD de categorías de productos con soft-deactivation.

---

#### `mostrarResumen`

```php
public function mostrarResumen(): string
```

Lista todas las categorías (activas e inactivas).

**Retorna:** `string` — HTML de la vista `categorias/index`.

---

#### `crearCategoria`

```php
public function crearCategoria(): string
```

Muestra el formulario para crear una nueva categoría.

**Retorna:** `string` — HTML de la vista `categorias/crear`.

---

#### `validarYguardar`

```php
public function validarYguardar(): \CodeIgniter\HTTP\RedirectResponse
```

Valida los datos del formulario y persiste la nueva categoría con `activa = 1`. Usa las reglas del modelo antes de insertar.

**Retorna:** Redirección a `/categorias` con éxito, o al formulario con errores.

---

#### `editarCategoria`

```php
public function editarCategoria(int $idCategoria): \CodeIgniter\HTTP\RedirectResponse|string
```

Muestra el formulario de edición de una categoría existente con sus datos pre-cargados.

| Parámetro      | Tipo  | Descripción               |
|----------------|-------|---------------------------|
| `$idCategoria` | `int` | ID de la categoría a editar |

**Retorna:** `string` — HTML de la vista `categorias/editar`, o redirección con error si no existe.

---

#### `actualizarCategoria`

```php
public function actualizarCategoria(int $idCategoria): \CodeIgniter\HTTP\RedirectResponse
```

Valida y persiste los cambios sobre una categoría existente.

| Parámetro      | Tipo  | Descripción                    |
|----------------|-------|--------------------------------|
| `$idCategoria` | `int` | ID de la categoría a actualizar |

**Retorna:** Redirección a `/categorias` con éxito o errores.

---

#### `desactivarCategoria`

```php
public function desactivarCategoria(int $idCategoria): \CodeIgniter\HTTP\RedirectResponse
```

Desactiva una categoría (soft-delete) cambiando `activa = 0` sin eliminar el registro físicamente.

| Parámetro      | Tipo  | Descripción                    |
|----------------|-------|--------------------------------|
| `$idCategoria` | `int` | ID de la categoría a desactivar |

**Retorna:** Redirección a `/categorias` con mensaje de éxito o error.

---

### InventarioController

**Archivo:** `app/Controllers/InventarioController.php`  
Gestión de stock e inventario.

---

#### `index`

```php
public function index(): string
```

Muestra el inventario completo con información de cada producto y sus niveles de stock, junto con las alertas de stock bajo.

**Retorna:** `string` — HTML de la vista `inventario/index`.

---

#### `create`

```php
public function create(): string
```

Muestra el formulario para registrar el stock de un producto que aún no tiene registro. Carga los productos con control de stock habilitado.

**Retorna:** `string` — HTML de la vista `inventario/crear`.

---

#### `store`

```php
public function store(): \CodeIgniter\HTTP\RedirectResponse
```

Persiste un nuevo registro de stock para un producto. Valida con las reglas del modelo antes de insertar.

**Retorna:** Redirección a `/inventario` con éxito, o al formulario con errores.

---

#### `edit`

```php
public function edit(int $idStock): \CodeIgniter\HTTP\RedirectResponse|string
```

Muestra el formulario de edición de un registro de stock existente, incluyendo el nombre del producto asociado.

| Parámetro  | Tipo  | Descripción                       |
|------------|-------|-----------------------------------|
| `$idStock` | `int` | ID del registro de stock a editar |

**Retorna:** `string` — HTML de la vista `inventario/editar`, o redirección con error si no existe.

---

#### `update`

```php
public function update(int $idStock): \CodeIgniter\HTTP\RedirectResponse
```

Actualiza las cantidades (`cantidad_disponible`, `cantidad_minima`) de un registro de stock y registra la fecha y hora de la última actualización.

| Parámetro  | Tipo  | Descripción                          |
|------------|-------|--------------------------------------|
| `$idStock` | `int` | ID del registro de stock a actualizar |

**Retorna:** Redirección a `/inventario` con éxito o errores.

---

#### `alertas`

```php
public function alertas(): string
```

Lista los productos cuyo stock disponible está en o por debajo del mínimo establecido.

**Retorna:** `string` — HTML de la vista `inventario/alertas`.

---

### FacturacionController

**Archivo:** `app/Controllers/FacturacionController.php`  
Listado de pagos del día y detalle de factura individual.

---

#### `index`

```php
public function index(): string
```

Lista todos los pagos registrados en la fecha seleccionada (por defecto hoy, recibida como `?fecha=Y-m-d`). Calcula KPIs: total del día, cantidad de pagos, ticket promedio, método de pago más usado y variación porcentual respecto al día anterior.

**Retorna:** `string` — HTML de la vista `facturacion/index`.

---

#### `detalle`

```php
public function detalle(int $idPago): \CodeIgniter\HTTP\RedirectResponse|string
```

Muestra el comprobante de un pago específico con todos los ítems del pedido asociado, el total y el vuelto (si corresponde).

| Parámetro | Tipo  | Descripción           |
|-----------|-------|-----------------------|
| `$idPago` | `int` | ID del pago a mostrar |

**Retorna:** `string` — HTML de la vista `facturacion/detalle`, o redirección con error si no existe.

---

## Models

### UsuarioModel

**Archivo:** `app/Models/UsuarioModel.php`  
**Tabla:** `usuarios`

---

#### `validarLogin`

```php
public function validarLogin(string $login, string $password): ?array
```

Valida las credenciales de un intento de login. Busca al usuario por email o username, verifica que esté activo y confirma la contraseña con `password_verify()`. Elimina `password_hash` del resultado antes de retornar.

| Parámetro   | Tipo     | Descripción                    |
|-------------|----------|--------------------------------|
| `$login`    | `string` | Email o username               |
| `$password` | `string` | Contraseña en texto plano      |

**Retorna:** `array` con los datos del usuario (sin hash), o `null` si las credenciales son inválidas.

---

#### `getUsuarioPorLogin`

```php
public function getUsuarioPorLogin(string $login): ?array
```

Busca un usuario por email o username haciendo JOIN con la tabla `roles` para incluir el nombre del rol.

| Parámetro | Tipo     | Descripción        |
|-----------|----------|--------------------|
| `$login`  | `string` | Email o username   |

**Retorna:** `array` con los datos del usuario y `rol_nombre`, o `null` si no existe.

---

#### `registrarUsuario`

```php
public function registrarUsuario(array $datos): int|false
```

Registra un nuevo usuario aplicando las reglas de validación del modelo. Hashea la contraseña antes de insertar y construye el array final con solo los campos permitidos en la BD.

| Parámetro | Tipo    | Descripción                                        |
|-----------|---------|----------------------------------------------------|
| `$datos`  | `array` | Datos del usuario; debe incluir `password` en texto plano |

**Retorna:** `int` con el ID del usuario creado, o `false` si falla la validación o la inserción.

---

#### `emailExiste`

```php
public function emailExiste(string $email): bool
```

Verifica si un email ya está registrado en la tabla `usuarios`.

| Parámetro | Tipo     | Descripción      |
|-----------|----------|------------------|
| `$email`  | `string` | Email a verificar |

**Retorna:** `true` si existe, `false` si no.

---

#### `usernameExiste`

```php
public function usernameExiste(string $username): bool
```

Verifica si un nombre de usuario ya está en uso.

| Parámetro   | Tipo     | Descripción              |
|-------------|----------|--------------------------|
| `$username` | `string` | Username a verificar     |

**Retorna:** `true` si existe, `false` si no.

---

#### `dniExiste`

```php
public function dniExiste(int $dni): bool
```

Verifica si un DNI ya está registrado.

| Parámetro | Tipo  | Descripción      |
|-----------|-------|------------------|
| `$dni`    | `int` | DNI a verificar  |

**Retorna:** `true` si existe, `false` si no.

---

#### `emailExisteExcluir`

```php
public function emailExisteExcluir(string $email, int $excludeId): bool
```

Verifica si un email está registrado excluyendo un usuario específico. Útil para validar unicidad al actualizar un perfil sin rechazar el propio email del usuario.

| Parámetro    | Tipo     | Descripción                         |
|--------------|----------|-------------------------------------|
| `$email`     | `string` | Email a verificar                   |
| `$excludeId` | `int`    | ID del usuario a excluir de la búsqueda |

**Retorna:** `true` si otro usuario ya tiene ese email, `false` si no.

---

#### `getUsuarioConRol`

```php
public function getUsuarioConRol(int $id): ?array
```

Obtiene un usuario por ID con el nombre y descripción de su rol.

| Parámetro | Tipo  | Descripción         |
|-----------|-------|---------------------|
| `$id`     | `int` | ID del usuario      |

**Retorna:** `array` con datos del usuario y rol, o `null` si no existe.

---

#### `getUsuariosActivos`

```php
public function getUsuariosActivos(): array
```

Obtiene todos los usuarios con `activo = 1`, ordenados por nombre, incluyendo el nombre del rol.

**Retorna:** `array` de usuarios activos.

---

#### `getUsuariosPorRol`

```php
public function getUsuariosPorRol(int $idRol): array
```

Obtiene los usuarios activos que pertenecen a un rol específico.

| Parámetro | Tipo  | Descripción   |
|-----------|-------|---------------|
| `$idRol`  | `int` | ID del rol    |

**Retorna:** `array` de usuarios del rol indicado.

---

#### `actualizarPassword`

```php
public function actualizarPassword(
    int $idUsuario,
    string $passwordActual,
    string $passwordNueva
): bool
```

Actualiza la contraseña de un usuario verificando primero la contraseña actual con `password_verify()`. Almacena el error en `$customErrors` si la contraseña actual es incorrecta.

| Parámetro         | Tipo     | Descripción                   |
|-------------------|----------|-------------------------------|
| `$idUsuario`      | `int`    | ID del usuario                |
| `$passwordActual` | `string` | Contraseña actual en texto plano |
| `$passwordNueva`  | `string` | Nueva contraseña en texto plano  |

**Retorna:** `true` si se actualizó exitosamente, `false` si el usuario no existe o la contraseña actual es incorrecta.

---

#### `setActivo`

```php
public function setActivo(int $idUsuario, bool $activo): bool
```

Activa o desactiva un usuario.

| Parámetro    | Tipo   | Descripción                                  |
|--------------|--------|----------------------------------------------|
| `$idUsuario` | `int`  | ID del usuario                               |
| `$activo`    | `bool` | `true` para activar, `false` para desactivar |

**Retorna:** `true` si la actualización fue exitosa.

---

#### `getErrores`

```php
public function getErrores(): array
```

Combina los errores del framework CI4 (`errors()`) con los errores manuales almacenados en `$customErrors`. Usar este método en lugar de `errors()` para obtener todos los errores del modelo.

**Retorna:** `array` combinado de errores.

---

#### `getPrimerError`

```php
public function getPrimerError(): ?string
```

Obtiene el primer error disponible del conjunto combinado de errores.

**Retorna:** `string` con el mensaje del primer error, o `null` si no hay errores.

---

### MesaModel

**Archivo:** `app/Models/MesaModel.php`  
**Tabla:** `mesas`

---

#### `getMesasConPedidoActivo`

```php
public function getMesasConPedidoActivo(): array
```

Obtiene todas las mesas ordenadas por número, con información del pedido abierto asociado (si existe) mediante un `LEFT JOIN` con `pedidos` filtrando `fecha_cierre IS NULL`.

**Retorna:** `array` de mesas con `id_pedido` y `fecha_apertura` del pedido activo si lo tienen.

---

#### `contarPorEstado`

```php
public function contarPorEstado(): array
```

Cuenta la cantidad de mesas agrupadas por estado. Siempre devuelve las cuatro claves de estado aunque alguna tenga cero.

**Retorna:** `array` asociativo con claves `libre`, `ocupada`, `reservada`, `inactiva` y sus totales como `int`.

---

### PedidoModel

**Archivo:** `app/Models/PedidoModel.php`  
**Tabla:** `pedidos`

---

#### `getPedidoActivoPorMesa`

```php
public function getPedidoActivoPorMesa(int $idMesa): ?array
```

Devuelve el pedido abierto (sin `fecha_cierre`) de una mesa específica.

| Parámetro | Tipo  | Descripción    |
|-----------|-------|----------------|
| `$idMesa` | `int` | ID de la mesa  |

**Retorna:** `array` del pedido activo, o `null` si no hay ninguno.

---

#### `getPedidosActivos`

```php
public function getPedidosActivos(): array
```

Obtiene todos los pedidos sin fecha de cierre con información del usuario, mesa y estado del pedido mediante JOINs.

**Retorna:** `array` de pedidos abiertos con datos relacionados.

---

#### `getPedidosCerradosPorFecha`

```php
public function getPedidosCerradosPorFecha(string $fecha): array
```

Obtiene los pedidos cerrados en una fecha específica con información de mesa, usuario, estado y pago (si existe).

| Parámetro | Tipo     | Descripción               |
|-----------|----------|---------------------------|
| `$fecha`  | `string` | Fecha en formato `Y-m-d`  |

**Retorna:** `array` de pedidos cerrados en esa fecha.

---

#### `getPedidosCerradosSinPago`

```php
public function getPedidosCerradosSinPago(): array
```

Obtiene todos los pedidos que tienen `fecha_cierre` pero no tienen registro en la tabla `pagos`. Usado para mostrar pendientes de cobro.

**Retorna:** `array` de pedidos cerrados sin pago.

---

#### `getPedidoConDetalles`

```php
public function getPedidoConDetalles(int $idPedido): ?array
```

Obtiene un pedido completo con datos de mesa, usuario, rol del usuario y estado del pedido.

| Parámetro   | Tipo  | Descripción     |
|-------------|-------|-----------------|
| `$idPedido` | `int` | ID del pedido   |

**Retorna:** `array` con todos los datos del pedido, o `null` si no existe.

---

#### `cerrarPedido`

```php
public function cerrarPedido(int $idPedido): bool
```

Registra la fecha y hora de cierre del pedido con la marca de tiempo actual.

| Parámetro   | Tipo  | Descripción     |
|-------------|-------|-----------------|
| `$idPedido` | `int` | ID del pedido   |

**Retorna:** `true` si se actualizó exitosamente.

---

#### `contarPedidosPorTipo`

```php
public function contarPedidosPorTipo(): array
```

Cuenta los pedidos abiertos agrupados por `tipo_pedido` (`mesa`, `barra`, `take_away`).

**Retorna:** `array` con `tipo_pedido` y `total` por cada tipo.

---

### ProductoModel

**Archivo:** `app/Models/ProductoModel.php`  
**Tabla:** `productos`

---

#### `getTodos`

```php
public function getTodos(): array
```

Obtiene todos los productos (activos e inactivos) con el nombre de su categoría. Ordena por `activo DESC`, luego por categoría y nombre.

**Retorna:** `array` de todos los productos.

---

#### `getProductosActivos`

```php
public function getProductosActivos(): array
```

Obtiene solo los productos con `activo = 1`, con el nombre de su categoría. Ordena por categoría y nombre.

**Retorna:** `array` de productos activos.

---

#### `getProductosPorCategoria`

```php
public function getProductosPorCategoria(int $idCategoria, bool $soloVentaBarra = false): array
```

Obtiene productos activos filtrados por categoría. Opcionalmente filtra solo los marcados para venta en barra.

| Parámetro         | Tipo   | Descripción                                             |
|-------------------|--------|---------------------------------------------------------|
| `$idCategoria`    | `int`  | ID de la categoría                                      |
| `$soloVentaBarra` | `bool` | Si `true`, filtra por `se_vende_en_barra = 1` (default `false`) |

**Retorna:** `array` de productos filtrados.

---

#### `getProductosConControlStock`

```php
public function getProductosConControlStock(): array
```

Obtiene los productos activos que tienen `controla_stock = 1`, usados en el módulo de inventario.

**Retorna:** `array` de productos con control de stock.

---

#### `getProductoConCategoria`

```php
public function getProductoConCategoria(int $idProducto): ?array
```

Obtiene un producto por ID incluyendo el nombre de su categoría.

| Parámetro     | Tipo  | Descripción         |
|---------------|-------|---------------------|
| `$idProducto` | `int` | ID del producto     |

**Retorna:** `array` del producto con `categoria_nombre`, o `null` si no existe.

---

### CategoriaProductoModel

**Archivo:** `app/Models/CategoriaProductoModel.php`  
**Tabla:** `categorias_productos`

---

#### `getAll`

```php
public function getAll(): array
```

Retorna todas las categorías (activas e inactivas) ordenadas alfabéticamente por nombre. Usado en vistas de administración.

**Retorna:** `array` de todas las categorías.

---

#### `getCategoriasActivas`

```php
public function getCategoriasActivas(): array
```

Retorna solo las categorías con `activa = 1` ordenadas por nombre. Usado en formularios de productos.

**Retorna:** `array` de categorías activas.

---

### StockModel

**Archivo:** `app/Models/StockModel.php`  
**Tabla:** `stock`

---

#### `getStockConProductos`

```php
public function getStockConProductos(): array
```

Obtiene el inventario completo con nombre del producto y nombre de la categoría, ordenado por cantidad disponible ascendente (los más críticos primero).

**Retorna:** `array` de registros de stock con datos de producto y categoría.

---

#### `getStockBajo`

```php
public function getStockBajo(): array
```

Obtiene los registros donde `cantidad_disponible <= cantidad_minima`, con un campo calculado `nivel_alerta` que puede ser `'critico'` (stock en 0) o `'bajo'`.

**Retorna:** `array` de productos con stock en alerta.

---

#### `getStockPorProducto`

```php
public function getStockPorProducto(int $idProducto): ?array
```

Obtiene el registro de stock de un producto específico con el nombre y precio del producto.

| Parámetro     | Tipo  | Descripción     |
|---------------|-------|-----------------|
| `$idProducto` | `int` | ID del producto |

**Retorna:** `array` del stock, o `null` si el producto no tiene registro.

---

#### `actualizarCantidad`

```php
public function actualizarCantidad(int $idProducto, int $diferencia): bool
```

Suma o resta la diferencia indicada a la `cantidad_disponible` del producto. Rechaza la operación si el resultado sería negativo. Registra la fecha de `ultima_actualizacion`.

| Parámetro     | Tipo  | Descripción                                  |
|---------------|-------|----------------------------------------------|
| `$idProducto` | `int` | ID del producto                              |
| `$diferencia` | `int` | Cantidad a sumar (positiva) o restar (negativa) |

**Retorna:** `true` si se actualizó, `false` si el producto no tiene stock o el resultado sería negativo.

---

#### `haySuficienteStock`

```php
public function haySuficienteStock(int $idProducto, int $cantidad): bool
```

Verifica si hay suficiente stock disponible para cubrir la cantidad solicitada.

| Parámetro     | Tipo  | Descripción              |
|---------------|-------|--------------------------|
| `$idProducto` | `int` | ID del producto          |
| `$cantidad`   | `int` | Cantidad requerida       |

**Retorna:** `true` si hay suficiente stock, `false` si no hay registro o el stock es insuficiente.

---

### PagoModel

**Archivo:** `app/Models/PagoModel.php`  
**Tabla:** `pagos`

---

#### `getPagoPorPedido`

```php
public function getPagoPorPedido(int $idPedido): ?array
```

Obtiene el pago registrado de un pedido junto con el nombre del método de pago.

| Parámetro   | Tipo  | Descripción   |
|-------------|-------|---------------|
| `$idPedido` | `int` | ID del pedido |

**Retorna:** `array` del pago con `metodo_nombre`, o `null` si no existe.

---

#### `getPagoConMetodo`

```php
public function getPagoConMetodo(int $idPago): ?array
```

Obtiene un pago específico por su ID junto con el nombre del método de pago.

| Parámetro | Tipo  | Descripción    |
|-----------|-------|----------------|
| `$idPago` | `int` | ID del pago    |

**Retorna:** `array` del pago con `metodo_nombre`, o `null` si no existe.

---

#### `getTotalPagadoPorFecha`

```php
public function getTotalPagadoPorFecha(string $fecha): float
```

Suma el total de todos los pagos registrados en una fecha específica.

| Parámetro | Tipo     | Descripción              |
|-----------|----------|--------------------------|
| `$fecha`  | `string` | Fecha en formato `Y-m-d` |

**Retorna:** `float` con el total del día, o `0.0` si no hay pagos.

---

### DetallePedidoModel

**Archivo:** `app/Models/DetallePedidoModel.php`  
**Tabla:** `detalle_pedidos`

---

#### `getDetallesPorPedido`

```php
public function getDetallesPorPedido(int $idPedido): array
```

Obtiene todos los ítems de un pedido incluyendo el nombre del producto mediante `LEFT JOIN`.

| Parámetro   | Tipo  | Descripción    |
|-------------|-------|----------------|
| `$idPedido` | `int` | ID del pedido  |

**Retorna:** `array` de líneas de detalle con `nombre` del producto.

---

#### `getTotalPedido`

```php
public function getTotalPedido(int $idPedido): float
```

Calcula el total de un pedido sumando los `subtotal` de todos sus ítems.

| Parámetro   | Tipo  | Descripción    |
|-------------|-------|----------------|
| `$idPedido` | `int` | ID del pedido  |

**Retorna:** `float` con el total del pedido.

---

### MetodoPagoModel

**Archivo:** `app/Models/MetodoPagoModel.php`  
**Tabla:** `metodos_pago`

---

#### `getActivos`

```php
public function getActivos(): array
```

Obtiene todos los métodos de pago con `activo = 1` ordenados alfabéticamente.

**Retorna:** `array` de métodos de pago activos.

---

## Services

### ComprobanteService

**Archivo:** `app/Services/ComprobanteService.php`  
Recopila los datos necesarios para mostrar el comprobante de un pago. No interactúa con HTTP ni devuelve vistas.

---

#### `obtenerDatos`

```php
public function obtenerDatos(int $idPago): array
```

Reúne todos los datos del comprobante de un pago: pago con método, pedido con detalle y la lista de ítems. Calcula el total sumando los subtotales.

| Parámetro | Tipo  | Descripción           |
|-----------|-------|-----------------------|
| `$idPago` | `int` | ID del pago a mostrar |

**Retorna:** `array` con las claves:

| Clave    | Tipo         | Descripción                              |
|----------|--------------|------------------------------------------|
| `ok`     | `bool`       | `false` si el pago no existe             |
| `pago`   | `array\|null`| Datos del pago con nombre del método     |
| `pedido` | `array\|null`| Datos del pedido con mesa y usuario      |
| `items`  | `array`      | Líneas de detalle con nombre del producto |
| `total`  | `float`      | Suma de subtotales de los ítems          |

---

### PrepararPagoService

**Archivo:** `app/Services/PrepararPagoService.php`  
Lógica de negocio para la fase de preparación del formulario de pago.

---

#### `preparar`

```php
public function preparar(int $idPedido): array
```

Coordina la preparación completa del formulario de pago. Verifica que el pedido exista y esté cerrado, detecta pagos previos para evitar duplicados, y recopila los datos que el formulario necesita.

| Parámetro   | Tipo  | Descripción               |
|-------------|-------|---------------------------|
| `$idPedido` | `int` | ID del pedido a pagar     |

**Retorna:** `array` con las claves:

| Clave             | Tipo          | Descripción                                          |
|-------------------|---------------|------------------------------------------------------|
| `ok`              | `bool`        | `true` si el pedido está listo para pagar            |
| `error`           | `string\|null`| Mensaje de error cuando `ok = false`                 |
| `pagoExistenteId` | `int\|null`   | ID del pago previo si ya fue registrado              |
| `data`            | `array\|null` | `{pedido, items, total, metodos}` cuando `ok = true` |

---

#### `calcularTotal`

```php
public function calcularTotal(array $items): float
```

Suma los `subtotal` de cada ítem para obtener el total del pedido.

| Parámetro | Tipo    | Descripción                                  |
|-----------|---------|----------------------------------------------|
| `$items`  | `array` | Líneas de detalle con el campo `subtotal`     |

**Retorna:** `float` con el total calculado.

---

### RegistrarPagoService

**Archivo:** `app/Services/RegistrarPagoService.php`  
Lógica de negocio del registro efectivo del pago.

---

#### `registrar`

```php
public function registrar(int $idPedido, int $idMetodoPago): array
```

Registra el pago de un pedido cerrado. Calcula el total server-side desde los detalles del pedido, valida los datos contra las reglas del modelo y persiste el pago junto con la actualización del estado del pedido dentro de una única transacción.

| Parámetro      | Tipo  | Descripción                         |
|----------------|-------|-------------------------------------|
| `$idPedido`    | `int` | ID del pedido que se está pagando   |
| `$idMetodoPago`| `int` | ID del método de pago seleccionado  |

**Retorna:** `array` con las claves:

| Clave             | Tipo          | Descripción                                            |
|-------------------|---------------|--------------------------------------------------------|
| `ok`              | `bool`        | `true` si el pago fue registrado exitosamente          |
| `idPago`          | `int\|null`   | ID del pago insertado cuando `ok = true`               |
| `error`           | `string\|null`| Mensaje genérico de error cuando `ok = false`          |
| `errors`          | `array\|null` | Errores de validación del modelo cuando `ok = false`   |
| `pagoExistenteId` | `int\|null`   | Non-null si ya había un pago registrado                |

---

#### `validarDatosRegistro` *(private)*

```php
private function validarDatosRegistro(array $registro): ?array
```

Valida el array del registro contra las reglas definidas en `PagoModel`. Retorna un array de falla con los errores del modelo, o `null` si la validación pasa.

| Parámetro   | Tipo    | Descripción                          |
|-------------|---------|--------------------------------------|
| `$registro` | `array` | Datos del pago a validar             |

**Retorna:** `array` de falla con errores, o `null` si es válido.

---

#### `persistirPago` *(private)*

```php
private function persistirPago(array $registro, int $idPedido): array
```

Inserta el registro en `pagos` y actualiza el `id_estado_pedido` del pedido a `pagado` dentro de una única transacción de base de datos.

| Parámetro   | Tipo    | Descripción               |
|-------------|---------|---------------------------|
| `$registro` | `array` | Datos del pago validados  |
| `$idPedido` | `int`   | ID del pedido a actualizar |

**Retorna:** `array` de resultado con `ok`, `idPago` y campos de error.

---

#### `obtenerOCrearEstado` *(private)*

```php
private function obtenerOCrearEstado(string $nombre): int
```

Busca el ID de un estado en `estados_pedido` por su nombre. Si no existe, lo inserta y devuelve el ID generado.

| Parámetro | Tipo     | Descripción                      |
|-----------|----------|----------------------------------|
| `$nombre` | `string` | Nombre del estado (ej. `'pagado'`) |

**Retorna:** `int` — ID del estado.
