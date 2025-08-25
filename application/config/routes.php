<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
// Rutas de autenticación
$route['auth/register'] = 'auth/register';
$route['auth/login'] = 'auth/login';
$route['auth/logout'] = 'auth/logout';
$route['auth/seleccionar_rol'] = 'auth/seleccionar_rol';

// Dashboards por rol
$route['dashboardadmin'] = 'dashboard/admin';
$route['dashboard/cliente'] = 'dashboard/cliente';
$route['dashboard/empleado'] = 'dashboard/empleado';

$route['dashboard'] = 'dashboard';

// Ruta por defecto
$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


//cliente

$route['cliente'] = 'dashboard/cliente';
$route['cliente/productos'] = 'cliente/productos';
$route['cliente/comprar/(:num)'] = 'cliente/comprar/$1';
$route['cliente/mis_compras'] = 'cliente/mis_compras';
$route['cliente/perfil'] = 'cliente/perfil';
$route['cliente/actualizar_perfil'] = 'cliente/actualizar_perfil';
$route['cliente/ajax_obtener_datos_dashboard'] = 'cliente/ajax_obtener_datos_dashboard';
$route['cliente/ajax_buscar_productos'] = 'cliente/ajax_buscar_productos';