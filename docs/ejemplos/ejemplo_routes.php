<?php
/**
 * RUTAS DE AUTENTICACIÓN
 * 
 * Este es un ejemplo de cómo configurar las rutas en app/Config/Routes.php
 * Basado en la implementación completa del modelo y controlador Auth
 */

// ========================================
// RUTAS DE AUTENTICACIÓN
// ========================================

$routes->group('auth', function($routes) {
    // Formulario de login
    $routes->get('login', 'Auth::login', ['as' => 'auth_login']);
    
    // Procesar autenticación
    $routes->post('authenticate', 'Auth::authenticate', ['as' => 'auth_authenticate']);
    
    // Formulario de registro
    $routes->get('register', 'Auth::register', ['as' => 'auth_register']);
    
    // Procesar registro
    $routes->post('store', 'Auth::store', ['as' => 'auth_store']);
    
    // Cerrar sesión
    $routes->get('logout', 'Auth::logout', ['as' => 'auth_logout']);
});

// ========================================
// RUTAS PROTEGIDAS (requieren autenticación)
// ========================================

// Ejemplo - Rutas del dashboard que requieren login
// Estas rutas pueden usar un filtro para verificar autenticación
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('perfil', 'User::perfil');
    $routes->post('perfil/update', 'User::updatePerfil');
    $routes->post('perfil/update-password', 'User::updatePassword');
});

// ========================================
// EJEMPLO DE IMPLEMENTACIÓN EN app/Config/Routes.php
// ========================================

/*

Si tu archivo app/Config/Routes.php se ve así:

<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

// $routes->setDefaultNamespace('App\Controllers');
// $routes->setDefaultController('Home');
// $routes->setDefaultMethod('index');
// $routes->setTranslateURIDashes(false);

// Load custom routing
// $routes->add('customers/(:num)', 'Customers::view/$1');
// $routes->get('posts', 'Posts::list');

$routes->setAutoRoute(true);

return $routes;

// Deberías agregar esto ANTES de return $routes:

// ========================================
// RUTAS DE AUTENTICACIÓN
// ========================================

$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('authenticate', 'Auth::authenticate');
    $routes->get('register', 'Auth::register');
    $routes->post('store', 'Auth::store');
    $routes->get('logout', 'Auth::logout');
});

// Rutas protegidas por filtro de autenticación
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('perfil', 'User::perfil');
});

return $routes;

*/

// ========================================
// DIRECTORIOS IMPORTANTES
// ========================================

/*

Estructura de directorios sugerida:

app/
├── Controllers/
│   ├── Auth.php                    <- Controlador de autenticación
│   ├── Dashboard.php               <- Controlador del dashboard
│   └── BaseController.php
├── Filters/
│   ├── AuthFilter.php              <- Filtro de autenticación
│   └── AdminFilter.php             <- Filtro de rol admin
├── Models/
│   ├── UsuarioModel.php            <- Modelo mejorado (ya existe)
│   └── RolModel.php                <- Modelo de roles (opcional)
└── Views/
    ├── auth/
    │   ├── login.php               <- Vista de login (ya existe)
    │   └── register.php            <- Vista de registro
    ├── errors/
    └── dashboard.php

*/
