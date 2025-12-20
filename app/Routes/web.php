<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rota para servir arquivos privados de forma segura (ex: de writable/uploads).
$routes->get('files/(:any)', 'FilesController::serve/$1');

// Rota para servir assets de desenvolvimento de forma segura.
// Esta rota só deve funcionar em ambiente de desenvolvimento,
// conforme a lógica dentro do ResourcesController.
$routes->get('resources/(:any)', 'ResourcesController::serve/$1');


$routes->get('/login', 'Web\UsuarioController::login');
$routes->get('/logout', 'Web\UsuarioController::logout');
$routes->get('/criar-conta', 'Web\UsuarioController::criarConta');


$routes->get('/', 'Home::index');
$routes->get('/home/teste', 'Web\HomeController::index');

