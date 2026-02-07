<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

// Autenticación (Shield)
$routes->get('/login', '\CodeIgniter\Shield\Controllers\LoginController::loginView');
$routes->post('/login', '\CodeIgniter\Shield\Controllers\LoginController::loginAction');
$routes->get('/register', '\CodeIgniter\Shield\Controllers\RegisterController::registerView');
$routes->post('/register', '\CodeIgniter\Shield\Controllers\RegisterController::registerAction');
$routes->get('/logout', '\CodeIgniter\Shield\Controllers\LoginController::logoutAction');

// Rutas protegidas del admin
$routes->group('admin', ['filter' => 'session'], function($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
});