<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Configure o 404 global 
$routes->set404Override('App\Controllers\Errors::notFound');

require APPPATH . 'Routes/web.php';

$routes->group('api', static function ($routes) {
    require APPPATH . 'Routes/api.php';
});

$routes->group('app', static function ($routes) {
    require APPPATH . 'Routes/app.php';
});
