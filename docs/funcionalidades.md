# Funcionalidades del Sistema BarPOS

---

## Autenticación

1. **Formulario de inicio de sesión**
   Pantalla principal de acceso al sistema. El usuario ingresa su email o nombre de usuario junto con su contraseña. Si las credenciales son válidas, se crea la sesión y se redirige al dashboard; de lo contrario, se muestra un mensaje de error en el mismo formulario.

2. **Inicio de sesión**
   Procesamiento de las credenciales ingresadas en el formulario de login. El sistema valida contra la base de datos (acepta email o username), genera la sesión con los datos del usuario (nombre, apellido, email, rol) y redirige al panel principal.

3. **Formulario de registro de usuario**
   Pantalla para crear una nueva cuenta. El usuario completa los campos: nombre, apellido, DNI, fecha de nacimiento, nombre de usuario, email, contraseña y confirmación de contraseña. El sistema verifica que el DNI, username y email no estén ya registrados.

4. **Registro de usuario**
   Procesamiento del formulario de registro. El sistema valida los datos, hashea la contraseña y guarda el nuevo usuario en la base de datos con el rol por defecto. Al finalizar redirige al login con un mensaje de éxito.

5. **Cerrar sesión**
   Destruye la sesión activa del usuario y lo redirige a la página de inicio. Se accede desde cualquier pantalla a través del botón de logout en la barra de navegación.

---

## Dashboard

6. **Panel de control principal**
   Vista central del sistema que muestra indicadores clave en tiempo real: ventas del día, pedidos abiertos, mesas ocupadas y alertas de stock bajo. Incluye una grilla visual del estado de cada mesa, los 5 productos más vendidos del día, los últimos 8 pedidos registrados y las alertas de inventario crítico.

---

## Gestión de Mesas

7. **Listar mesas**
   Muestra todas las mesas del establecimiento con su estado actual (libre, ocupada, reservada o inactiva). Incluye contadores por estado y, para las mesas ocupadas, el pedido activo asociado. Se accede desde el menú lateral en la sección "Mesas".

8. **Crear mesa**
   Formulario para dar de alta una nueva mesa. El sistema sugiere automáticamente el próximo número disponible. El usuario ingresa el número de mesa y su capacidad máxima (cantidad de personas). La nueva mesa queda en estado "libre" al guardarse.

9. **Cambiar estado de mesa**
   Desde el listado de mesas, cada mesa dispone de botones para cambiar su estado a: libre, ocupada, reservada o inactiva. El sistema impide cambiar a "libre" o "inactiva" si la mesa tiene un pedido activo abierto.

10. **Eliminar mesa**
    Desde el listado de mesas, el botón de eliminar borra permanentemente el registro. El sistema previene la eliminación si la mesa tiene pedidos activos asociados para evitar pérdida de datos.

---

## Gestión de Productos

11. **Listar productos**
    Tabla con todos los productos activos del sistema. Muestra nombre, categoría, descripción, precio de venta y si el producto se vende en barra o controla stock. Incluye acciones para editar y desactivar cada producto.

12. **Agregar un producto**
    Formulario para crear un nuevo producto. Campos: nombre, descripción, categoría (listado de categorías activas), precio de venta y checkboxes para indicar si se vende en barra y si controla stock. El producto se guarda como activo por defecto.

13. **Editar un producto**
    Formulario pre-llenado con los datos actuales del producto seleccionado. Permite modificar todos sus campos: nombre, descripción, categoría, precio de venta, flags y estado (activo/inactivo). Se accede desde el botón "Editar" en el listado de productos.

14. **Desactivar un producto**
    Realiza un soft-delete sobre el producto: lo marca como inactivo (activo = 0) sin borrarlo de la base de datos. El producto deja de aparecer en el listado normal y en los formularios de pedido, pero conserva el historial. Se accede desde el botón correspondiente en el listado.

---

## Gestión de Categorías de Productos

15. **Listar categorías**
    Tabla con todas las categorías activas. Muestra nombre, descripción y la cantidad de productos asociados a cada una. Incluye acciones para editar y desactivar.

16. **Añadir una categoría**
    Formulario para crear una nueva categoría de productos. Campos: nombre y descripción. La categoría se guarda como activa por defecto y queda disponible de inmediato al crear o editar productos.

17. **Editar una categoría**
    Formulario pre-llenado con los datos de la categoría seleccionada. Permite modificar el nombre, la descripción y el estado (activa/inactiva). Se accede desde el botón "Editar" en el listado de categorías.

18. **Desactivar una categoría**
    Realiza un soft-delete sobre la categoría: la marca como inactiva (activa = 0). La categoría deja de mostrarse en los formularios de productos y en el listado normal, pero permanece en la base de datos para mantener la integridad histórica.

---

## Gestión de Inventario

19. **Ver inventario**
    Vista general del stock de todos los productos que controlan inventario. Muestra para cada producto: cantidad disponible, cantidad mínima requerida y fecha de última actualización. Los registros con stock por debajo del mínimo se destacan visualmente como alertas.

20. **Crear registro de stock**
    Formulario para agregar un nuevo registro de inventario. El usuario selecciona un producto (de los que tienen activada la opción "controla stock") e ingresa la cantidad disponible y la cantidad mínima antes de generar alerta. Se accede desde el botón "Agregar" en el listado de inventario.

21. **Editar registro de stock**
    Formulario para actualizar las cantidades de un registro de inventario existente. Muestra el nombre del producto (no editable) y permite modificar la cantidad disponible y la cantidad mínima. Al guardar, se registra automáticamente la fecha y hora de la última actualización.

22. **Ver alertas de stock bajo**
    Vista dedicada que filtra y muestra únicamente los productos cuya cantidad disponible es menor a la cantidad mínima configurada. Sirve como referencia rápida para gestionar reposición de insumos. Se accede desde la sección "Alertas" dentro del módulo de inventario.

---

## Gestión de Pedidos

23. **Listar pedidos activos**
    Vista de todos los pedidos abiertos (sin fecha de cierre). Muestra: identificador, tipo de pedido (mesa, barra o take away), número de mesa si aplica, usuario que lo creó, cantidad de ítems, total acumulado y estado. También incluye un resumen de pedidos cerrados pendientes de pago.

24. **Crear pedido**
    Formulario para registrar un nuevo pedido. El usuario selecciona el tipo (mesa, barra o take away), la mesa disponible si corresponde, y agrega productos al carrito con su cantidad. También puede agregar observaciones. Al guardar, la mesa queda marcada como ocupada automáticamente.

25. **Ver detalles de un pedido**
    Vista detallada de un pedido específico. Muestra la información del encabezado (estado, mesa, usuario, fechas), el listado de ítems con cantidades, precios unitarios y subtotales, el total del pedido, los botones para cerrar o reabrir el pedido, el formulario para agregar nuevos ítems y, si ya fue pagado, la información del pago.

26. **Agregar ítem a un pedido**
    Desde la vista de detalles de un pedido abierto, el usuario selecciona un producto, indica la cantidad y opcionalmente agrega una observación. El sistema calcula el subtotal automáticamente y actualiza el total del pedido. Solo disponible mientras el pedido esté abierto.

27. **Eliminar ítem de un pedido**
    Desde la vista de detalles de un pedido abierto, el botón de eliminar junto a cada ítem lo quita del pedido. El total se actualiza automáticamente. Solo disponible mientras el pedido esté abierto.

28. **Cerrar pedido**
    Desde la vista de detalles, el botón "Cerrar pedido" registra la fecha de cierre y cambia el estado del pedido a "cerrado". La mesa asociada vuelve al estado "libre". Un pedido cerrado no puede recibir más ítems y queda listo para ser cobrado.

29. **Reabrir pedido**
    Desde la vista de detalles de un pedido cerrado, el botón "Reabrir" revierte el cierre: elimina la fecha de cierre, restaura el estado a "abierto" y vuelve a marcar la mesa como ocupada. Solo es posible si el pedido aún no tiene un pago registrado.

30. **Historial de pedidos**
    Pantalla que muestra todos los pedidos cerrados de una fecha específica (por defecto el día actual). Permite navegar a fechas anteriores usando el selector de fecha. Muestra tipo de pedido, mesa, usuario, ítems, total y si el pedido fue pagado o está pendiente.

---

## Gestión de Pagos

31. **Formulario de cobro**
    Pantalla para registrar el pago de un pedido cerrado. Muestra el resumen del pedido con todos sus ítems y el total a cobrar. El usuario selecciona el método de pago (efectivo, tarjeta, etc.) disponible. Si el pedido ya fue pagado previamente, redirige directamente al comprobante existente.

32. **Procesar pago (cobrar)**
    Procesamiento del formulario de cobro. El sistema valida el método de pago seleccionado, registra el pago en la base de datos con monto, fecha y método, y redirige al comprobante. Si hay error de validación, vuelve al formulario con el mensaje correspondiente.

33. **Comprobante de pago (recibo)**
    Pantalla que muestra el recibo del pago registrado. Incluye: número de comprobante, fecha y hora del pago, método utilizado, datos del pedido, listado de ítems cobrados y total. La vista está diseñada para poder imprimirse directamente desde el navegador.

---

## Facturación y Reportes

34. **Resumen de facturación del día**
    Dashboard de facturación con los indicadores del día: total vendido, cantidad de pagos registrados, ticket promedio, método de pago más utilizado y desglose de ingresos por método de pago. Incluye una comparativa porcentual con respecto al día anterior y una tabla con todos los pagos del día. El día a consultar puede cambiarse mediante un selector de fecha.

35. **Detalle de factura**
    Vista completa de una factura individual (identificada como FAC-{id}). Muestra el número de factura, datos del pago, información del pedido asociado, listado de ítems cobrados, total, vuelto si aplica y el usuario que procesó el pago.

---

## Página de Inicio

36. **Landing page**
    Página pública de presentación del sistema. Se muestra al acceder a la raíz del sitio (`/`). Ofrece enlaces directos al formulario de inicio de sesión y al formulario de registro. No requiere autenticación para visualizarse.
