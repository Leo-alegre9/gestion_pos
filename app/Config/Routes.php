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
$routes->get('mesas', 'MesaController::index');
$routes->get('mesas/crear', 'MesaController::create');
$routes->post('mesas/guardar', 'MesaController::store');
$routes->post('mesas/cambiar-estado/(:num)', 'MesaController::cambiarEstado/$1');
$routes->delete('mesas/eliminar/(:num)', 'MesaController::delete/$1');

/** Rutas para la gestión de productos */
$routes->get('productos', 'ProductoController::index');
$routes->get('productos/crear', 'ProductoController::create');
$routes->post('productos/guardar', 'ProductoController::store');
$routes->get('productos/editar/(:num)', 'ProductoController::edit/$1');
$routes->post('productos/actualizar/(:num)', 'ProductoController::update/$1');
$routes->post('productos/desactivar/(:num)', 'ProductoController::deactivate/$1');

/** Rutas para la gestión del inventario */
$routes->get('inventario', 'InventarioController::index');
$routes->get('inventario/crear', 'InventarioController::create');
$routes->post('inventario/guardar', 'InventarioController::store');
$routes->get('inventario/editar/(:num)', 'InventarioController::edit/$1');
$routes->post('inventario/actualizar/(:num)', 'InventarioController::update/$1');
$routes->get('inventario/alertas', 'InventarioController::alertas');

/** Rutas para la gestión de pedidos */
$routes->get('pedidos', 'PedidoController::index');
$routes->get('pedidos/crear', 'PedidoController::create');
$routes->post('pedidos/guardar', 'PedidoController::store');
$routes->get('pedidos/detalles/(:num)', 'PedidoController::show/$1');
$routes->post('pedidos/cerrar/(:num)', 'PedidoController::cerrar/$1');
$routes->post('pedidos/agregar-detalle/(:num)', 'PedidoController::agregarDetalle/$1');
$routes->post('pedidos/eliminar-detalle/(:num)/(:num)', 'PedidoController::eliminarDetalle/$1/$2');
$routes->get('pedidos/historial', 'PedidoController::historial');

/** Rutas para la gestión de pagos */
$routes->get('pagos/pagar/(:num)', 'PagoController::pagar/$1');
$routes->post('pagos/registrar/(:num)', 'PagoController::store/$1');
$routes->get('pagos/recibo/(:num)', 'PagoController::recibo/$1');

/** Rutas de facturación */
$routes->get('facturacion', 'FacturacionController::index');
$routes->get('facturacion/detalle/(:num)', 'FacturacionController::detalle/$1');

/** Rutas para la gestión de categorías */
$routes->get('categorias', 'CategoriaController::index');
$routes->get('categorias/crear', 'CategoriaController::create');
$routes->post('categorias/guardar', 'CategoriaController::store');
$routes->get('categorias/editar/(:num)', 'CategoriaController::edit/$1');
$routes->post('categorias/actualizar/(:num)', 'CategoriaController::update/$1');
$routes->post('categorias/desactivar/(:num)', 'CategoriaController::deactivate/$1');


