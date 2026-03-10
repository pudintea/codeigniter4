<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Health::index');

// API V1 GROUP
/*
Controllers/
  Api/
    V1/
      Users.php
*/
$routes->group('api', function($routes) {
    $routes->group('v1', ['namespace' => 'App\Controllers\Api\V1'], function($routes) {
        $routes->group('users', function($routes) {
            $routes->get('/', 'Users::index');
            $routes->get('(:num)', 'Users::show/$1');
            $routes->post('/', 'Users::create');
            $routes->put('(:num)', 'Users::update/$1');
            $routes->delete('(:num)', 'Users::delete/$1');
        });
    });
});
