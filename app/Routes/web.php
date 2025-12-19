<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rota para servir assets de desenvolvimento de forma segura.
// Esta rota só deve funcionar em ambiente de desenvolvimento,
// conforme a lógica dentro do ResourcesController.
$routes->get('resources/(:any)', 'ResourcesController::serve/$1');


$routes->get('/', 'Home::index');
$routes->get('/home/teste', 'Web\HomeController::index');

