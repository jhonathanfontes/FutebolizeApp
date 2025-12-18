<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// API Routes
$routes->get('test-response', function () {
    return service('responseService')->success([
        'teste' => 'ok'
    ]);
});

    // Rotas para a versão 1 da API
    $routes->group('v1', function ($routes) {

        // Rotas para o recurso "usuario"
        $routes->group('usuario', function ($routes) {
            $routes->get('/', 'Api\UsuarioController::getAll');
            $routes->get('(:num)', 'Api\UsuarioController::show/$1');
        });

        // Fim do grupo de rotas v1
    });





    // These two were in a group, but are API routes
    $routes->post('upload', 'Api\UploadController::upload');
    $routes->get('download', 'Api\UploadController::download');
