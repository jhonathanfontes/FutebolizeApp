<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->post('/push/subscribe', 'PushController::subscribe');
$routes->get('/push/send', 'PushController::send');
