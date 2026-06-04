<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('dashboard', 'DashboardController::index');

// Auth routes
$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('authenticate', 'Auth::authenticate');
    $routes->get('register', 'Auth::register');
    $routes->post('store', 'Auth::store');
    $routes->get('logout', 'Auth::logout');

});


/** Rutas para direccionar a la gestión de las mesas */
$routes->get('mesas', 'MesaController::mostrarResumen');
$routes->get('mesas/crear', 'MesaController::crearMesa');
$routes->post('mesas/guardar', 'MesaController::validarYalmacenar');
$routes->post('mesas/cambiar-estado/(:num)', 'MesaController::cambiarEstado/$1');
$routes->delete('mesas/eliminar/(:num)', 'MesaController::eliminarMesa/$1');

/** Rutas para la gestión de productos */
$routes->get('productos', 'ProductoController::mostrarResumen');
$routes->get('productos/crear', 'ProductoController::crearProducto');
$routes->post('productos/guardar', 'ProductoController::validarYalmacenar');
$routes->get('productos/editar/(:num)', 'ProductoController::editarProducto/$1');
$routes->post('productos/actualizar/(:num)', 'ProductoController::actualizarProducto/$1');
$routes->post('productos/desactivar/(:num)', 'ProductoController::desactivarProducto/$1');

/** Rutas para la gestión del inventario */
$routes->get('inventario', 'InventarioController::index');
$routes->get('inventario/crear', 'InventarioController::create');
$routes->post('inventario/guardar', 'InventarioController::store');
$routes->get('inventario/editar/(:num)', 'InventarioController::edit/$1');
$routes->post('inventario/actualizar/(:num)', 'InventarioController::update/$1');
$routes->get('inventario/alertas', 'InventarioController::alertas');

/** Rutas para la gestión de pedidos */
$routes->get('pedidos', 'PedidoController::mostrarResumen');
$routes->get('pedidos/crear', 'PedidoController::crearPedido');
$routes->post('pedidos/guardar', 'PedidoController::validarYguardarPedido');
$routes->get('pedidos/detalles/(:num)', 'PedidoController::mostrarPedidoConDetalles/$1');
$routes->post('pedidos/cerrar/(:num)', 'PedidoController::cerrarPedido/$1');
$routes->post('pedidos/reabrir/(:num)', 'PedidoController::reabrirPedido/$1');
$routes->post('pedidos/agregar-detalle/(:num)', 'PedidoController::agregarDetalle/$1');
$routes->post('pedidos/eliminar-detalle/(:num)/(:num)', 'PedidoController::eliminarDetalle/$1/$2');
$routes->get('pedidos/historial', 'PedidoController::mostrarHistorialDePedidos');

/** Rutas para la gestión de pagos */
$routes->get('pagos/formulario/(:num)',  'PagoController::mostrarFormularioDePago/$1');
$routes->post('pagos/procesar/(:num)',   'PagoController::procesarRegistroDePago/$1');
$routes->get('pagos/comprobante/(:num)', 'PagoController::mostrarComprobanteDePago/$1');

/** Rutas de facturación */
$routes->get('facturacion', 'FacturacionController::index');
$routes->get('facturacion/detalle/(:num)', 'FacturacionController::detalle/$1');

/** Rutas para la gestión de categorías */
$routes->get('categorias', 'CategoriaController::mostrarResumen');
$routes->get('categorias/crear', 'CategoriaController::crearCategoria');
$routes->post('categorias/guardar', 'CategoriaController::validarYguardar');
$routes->get('categorias/editar/(:num)', 'CategoriaController::editarCategoria/$1');
$routes->post('categorias/actualizar/(:num)', 'CategoriaController::actualizarCategoria/$1');
$routes->post('categorias/desactivar/(:num)', 'CategoriaController::desactivarCategoria/$1');


