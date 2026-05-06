<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 🔐 Autenticação
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attempt');
$routes->get('logout', 'AuthController::logout');

// 🔒 Rotas protegidas
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // 📊 Dashboard
    $routes->get('painel', 'DashboardController::index', [
        'filter' => 'permission:dashboard.view'
    ]);

    // 👥 Usuários (CRUD completo)
    $routes->group('usuarios', ['filter' => 'permission:users.manage'], function ($routes) {

        $routes->get('/', 'UsersController::index');
        $routes->get('create', 'UsersController::create');
        $routes->post('store', 'UsersController::store');

        // ✏️ editar
        $routes->get('edit/(:num)', 'UsersController::edit/$1');
        $routes->post('update/(:num)', 'UsersController::update/$1');

        // 🗑 deletar
        $routes->get('delete/(:num)', 'UsersController::delete/$1');
    });

});