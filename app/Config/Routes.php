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

    /*
    |--------------------------------------------------------------------------
    | MINI HIS / ERP CLINICO
    |--------------------------------------------------------------------------
    */

    $routes->get('admissoes', 'AdmissionController::index', [
        'filter' => 'permission:admissions.manage,dashboard.view'
    ]);
    $routes->get('admissoes/iniciar', 'AdmissionController::start', [
        'filter' => 'permission:admissions.manage,dashboard.view'
    ]);
    $routes->get('admissoes/(:num)/(:segment)', 'AdmissionController::step/$1/$2', [
        'filter' => 'permission:admissions.manage,dashboard.view'
    ]);
    $routes->post('admissoes/(:num)/(:segment)', 'AdmissionController::save/$1/$2', [
        'filter' => 'csrf'
    ]);

    $routes->get('tratamentos', 'TreatmentsController::index', [
        'filter' => 'permission:treatments.view,dashboard.view'
    ]);
    $routes->get('tratamentos/(:num)', 'TreatmentsController::show/$1', [
        'filter' => 'permission:treatments.view,dashboard.view'
    ]);
    $routes->post('tratamentos/(:num)/dia-cobranca', 'TreatmentsController::updateBillingDay/$1', [
        'filter' => 'csrf'
    ]);
    $routes->get('tratamentos/(:num)/prontuario/novo', 'RecordsController::create/$1', [
        'filter' => 'permission:records.manage,dashboard.view'
    ]);
    $routes->post('tratamentos/(:num)/prontuario', 'RecordsController::store/$1', [
        'filter' => 'csrf'
    ]);
    $routes->get('tratamentos/(:num)/alta', 'DischargesController::create/$1', [
        'filter' => 'permission:treatments.view,dashboard.view'
    ]);
    $routes->post('tratamentos/(:num)/alta', 'DischargesController::store/$1', [
        'filter' => 'csrf'
    ]);

    $routes->get('divisao-pacientes', 'PatientDivisionController::index', [
        'filter' => 'permission:treatments.view,dashboard.view'
    ]);
    $routes->post('divisao-pacientes/(:num)/atribuir', 'PatientDivisionController::assign/$1', [
        'filter' => 'csrf'
    ]);
    $routes->post('divisao-pacientes/(:num)/liberar', 'PatientDivisionController::release/$1', [
        'filter' => 'csrf'
    ]);

    $routes->get('financeiro', 'FinanceController::index', [
        'filter' => 'permission:finance.manage,dashboard.view'
    ]);
    $routes->post('financeiro/gerar-mensalidades', 'FinanceController::generateMonthly', [
        'filter' => 'csrf'
    ]);
    $routes->get('financeiro/(:num)/pagar', 'FinanceController::pay/$1', [
        'filter' => 'permission:finance.manage,dashboard.view'
    ]);

    $routes->get('agenda', 'CalendarController::index', [
        'filter' => 'permission:calendar.manage,dashboard.view'
    ]);
    $routes->get('agenda/eventos', 'CalendarController::events', [
        'filter' => 'permission:calendar.manage,dashboard.view'
    ]);
    $routes->post('agenda', 'CalendarController::store', [
        'filter' => 'csrf'
    ]);
    $routes->post('agenda/eventos/(:num)/mover', 'CalendarController::move/$1', [
        'filter' => 'csrf'
    ]);
    $routes->post('agenda/eventos/(:num)/pagar', 'CalendarController::payFinancialEvent/$1', [
        'filter' => 'csrf'
    ]);
    $routes->post('agenda/eventos/(:num)/reagendar', 'CalendarController::rescheduleFinancialEvent/$1', [
        'filter' => 'csrf'
    ]);

    $routes->get('documentos/(:num)/pdf', 'DocumentsController::pdf/$1', [
        'filter' => 'permission:documents.manage,dashboard.view'
    ]);
    $routes->post('documentos/(:num)/assinado', 'DocumentsController::uploadSigned/$1', [
        'filter' => 'csrf'
    ]);
    $routes->get('contratos/(:num)/pdf', 'ContractsController::pdf/$1', [
        'filter' => 'permission:documents.manage,dashboard.view'
    ]);

    $routes->get('administrativo-clinico', 'AdminClinicalController::index', [
        'filter' => 'permission:clinical_admin.manage,dashboard.view'
    ]);
    $routes->get('administrativo-clinico/modelos/(:num)', 'AdminClinicalController::editTemplate/$1', [
        'filter' => 'permission:clinical_admin.manage,dashboard.view'
    ]);
    $routes->post('administrativo-clinico/modelos/(:num)', 'AdminClinicalController::updateTemplate/$1', [
        'filter' => 'csrf'
    ]);

});
