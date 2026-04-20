<?php

/**
 * FILTROS DE AUTENTICACIÓN
 * 
 * Crear este archivo en: app/Filters/AuthFilter.php
 */

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If nothing is returned,
     * RequestInterface or ResponseInterface will be required
     * by the router to continue.
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar si el usuario está autenticado
        if (!session()->get('autenticado')) {
            // Guardar la URL actual para redirigir después del login
            session()->set('redirect_url', current_url());
            
            return redirect()->to('/auth/login')
                ->with('info', 'Necesitas iniciar sesión para acceder.');
        }
    }

    /**
     * Allows After filters to inspect and potentially modify the response
     * object as needed. This function does not need to do anything and has no
     * required parameters, other than expecting the response object should
     * be passed to it:
     *
     *      A typical implementation might look partially like:
     *
     *      public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
     *      {
     *          // Do something with the request and response here
     *      }
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     *
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}

/**
 * ========================================
 * FILTRO DE ROLES (AUTORIZACIÓN)
 * 
 * Crear este archivo en: app/Filters/AdminFilter.php
 * ========================================
 */

class AdminFilter implements FilterInterface
{
    /**
     * Verificar si el usuario tiene rol de administrador
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Primero verificar que esté autenticado
        if (!session()->get('autenticado')) {
            return redirect()->to('/auth/login');
        }

        // Verificar que tenga rol de admin (id_rol = 1)
        if (session()->get('id_rol') != 1) {
            return redirect()->to('/dashboard')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}

/**
 * ========================================
 * FILTRO DE ROL ESPECÍFICO
 * 
 * Crear este archivo en: app/Filters/RoleFilter.php
 * ========================================
 */

class RoleFilter implements FilterInterface
{
    /**
     * Verificar si el usuario tiene uno de los roles especificados
     * 
     * Uso en routes:
     * $routes->get('admin/users', 'Admin::users', ['filter' => 'role:1,2']);
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Primero verificar que esté autenticado
        if (!session()->get('autenticado')) {
            return redirect()->to('/auth/login');
        }

        // Si no se especificaron roles, permitir acceso
        if (empty($arguments)) {
            return;
        }

        $rolesPermitidos = explode(',', $arguments[0]);
        $rolUsuario = session()->get('id_rol');

        if (!in_array($rolUsuario, $rolesPermitidos)) {
            return redirect()->to('/dashboard')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}

/**
 * ========================================
 * REGISTRAR FILTROS EN app/Config/Filters.php
 * ========================================
 * 
 * Abre app/Config/Filters.php y busca la sección $filters['aliases']
 * Agrega o asegúrate de que tengas:
 * 
 * public array $aliases = [
 *     'csrf'     => CSRF::class,
 *     'toolbar'  => DebugToolbar::class,
 *     'honeypot' => Honeypot::class,
 *     'invalidate' => InvalidatePage::class,
 *     'auth'     => AuthFilter::class,              // <- Agregar esta línea
 *     'admin'    => AdminFilter::class,             // <- Agregar esta línea
 *     'role'     => RoleFilter::class,              // <- Agregar esta línea
 * ];
 * 
 * Luego en app/Config/Routes.php puedes usar:
 * 
 * // Ruta protegida por autenticación
 * $routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
 * 
 * // Ruta protegida por rol de admin
 * $routes->get('admin/users', 'Admin::users', ['filter' => 'admin']);
 * 
 * // Ruta protegida por roles específicos (1=Admin, 2=Manager)
 * $routes->get('reportes', 'Reportes::index', ['filter' => 'role:1,2']);
 */
