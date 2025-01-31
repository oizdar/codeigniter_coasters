<?php

use App\Controllers\Coasters\CreateCoaster;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', function ($routes) {
    $routes->post('coasters', [CreateCoaster::class, '__invoke']);
});
