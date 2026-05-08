<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 🔐 AUTENTICAÇÃO

$routes->get('/', 'AuthController::login');

$routes->get('login', 'AuthController::login');

$routes->post('login', 'AuthController::attempt');

$routes->get('logout', 'AuthController::logout');


// 🔒 ROTAS PROTEGIDAS

$routes->group('', ['filter' => 'auth'], function ($routes) {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    $routes->get('painel', 'DashboardController::index', [
        'filter' => 'permission:dashboard.view'
    ]);


    /*
    |--------------------------------------------------------------------------
    | USUÁRIOS
    |--------------------------------------------------------------------------
    */

    // LISTAGEM
    $routes->get('usuarios', 'UsersController::index', [
        'filter' => 'permission:users.view,users.manage'
    ]);

    // AJAX DATATABLES
    $routes->post('usuarios/ajax-list', 'UsersController::ajaxList', [
        'filter' => 'permission:users.view,users.manage'
    ]);

    // FORM CREATE
    $routes->get('usuarios/create', 'UsersController::create', [
        'filter' => 'permission:users.manage'
    ]);

    // STORE
    $routes->post('usuarios/store', 'UsersController::store', [
        'filter' => 'permission:users.manage'
    ]);

    // EDIT
    $routes->get('usuarios/edit/(:num)', 'UsersController::edit/$1', [
        'filter' => 'permission:users.manage'
    ]);

    // UPDATE
    $routes->post('usuarios/update/(:num)', 'UsersController::update/$1', [
        'filter' => 'permission:users.manage'
    ]);

    // DELETE
    $routes->get('usuarios/delete/(:num)', 'UsersController::delete/$1', [
        'filter' => 'permission:users.manage'
    ]);

});