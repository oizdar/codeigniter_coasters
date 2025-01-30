<?php

use App\Controllers\Coasters\CreateCoasters;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', function ($routes) {
    $routes->post('coasters', [CreateCoasters::class, '__invoke']);
});
