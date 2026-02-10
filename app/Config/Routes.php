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

// Rutas de participantes (CRUD completo)
$routes->get('participantes', 'ParticipanteController::index');
$routes->get('participantes/create', 'ParticipanteController::create');
$routes->post('participantes/store', 'ParticipanteController::store');
$routes->get('participantes/edit/(:num)', 'ParticipanteController::edit/$1');
$routes->post('participantes/update/(:num)', 'ParticipanteController::update/$1');
$routes->get('participantes/delete/(:num)', 'ParticipanteController::delete/$1');
$routes->get('sync', 'SyncController::index');
$routes->get('sync/test', 'SyncController::testConnection');
$routes->post('sync/sync-now', 'SyncController::syncNow');
$routes->get('reportes', 'ReporteController::index');
$routes->post('reportes/generar', 'ReporteController::generar');
$routes->get('reportes/exportar-excel', 'ReporteController::exportarExcel');
// Redirigimos PDF→CSV para evitar dependencias de PDF
$routes->get('reportes/exportar-pdf', 'ReporteController::exportarCSV');
$routes->get('reportes/exportar-csv', 'ReporteController::exportarCSV');
    });