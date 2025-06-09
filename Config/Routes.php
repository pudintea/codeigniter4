<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/dashboard', 'Dashboard::index', ['filter'=>'pdnislogin']);
$routes->match(['GET','POST'],'/login', 'Bo::login');
$routes->get('/logout', 'Bo::logout');

$routes->group('users', ['filter'=>'pdnislogin'], function($routes) {
    $routes->get('/', 'UsersController::index');
    $routes->get('/tambah', 'UsersController::tambah');
});
