<?php

use App\Controllers\Coasters\CreateCoaster;
use App\Controllers\Coasters\CreateCoasterWagon;
use App\Controllers\Coasters\UpdateCoaster;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->addPlaceholder('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

$routes->group('api', function ($routes) {
    $routes->post('coasters', [CreateCoaster::class, '__invoke']);
    $routes->put('coasters/(:uuid)', [UpdateCoaster::class, '__invoke']);
    $routes->post('coasters/(:uuid)/wagons', [CreateCoasterWagon::class, '__invoke']);
});
