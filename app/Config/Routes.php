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


