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

$routes->match(['GET', 'POST'], 'familia/(:segment)', 'FamilyPortalController::login/$1');
$routes->get('familia/(:segment)/arquivos', 'FamilyPortalController::files/$1');
$routes->get('familia/(:segment)/contratos/(:num)/pdf', 'FamilyPortalController::contractPdf/$1/$2');
$routes->get('familia/(:segment)/documentos/(:num)/pdf', 'FamilyPortalController::documentPdf/$1/$2');


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
        'filter' => 'csrf'
    ]);

    // EDIT
    $routes->get('usuarios/edit/(:num)', 'UsersController::edit/$1', [
        'filter' => 'permission:users.manage'
    ]);

    // UPDATE
    $routes->post('usuarios/update/(:num)', 'UsersController::update/$1', [
        'filter' => 'csrf'
    ]);

    // DELETE
    $routes->post('usuarios/delete/(:num)', 'UsersController::delete/$1', [
        'filter' => 'csrf'
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
    $routes->post('admissoes/(:num)/excluir', 'AdmissionController::delete/$1', [
        'filter' => 'csrf'
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
    $routes->get('prontuario/(:num)/editar', 'RecordsController::edit/$1', [
        'filter' => 'permission:records.manage,dashboard.view'
    ]);
    $routes->post('prontuario/(:num)/editar', 'RecordsController::update/$1', [
        'filter' => 'csrf'
    ]);
    $routes->get('tratamentos/(:num)/alta', 'DischargesController::create/$1', [
        'filter' => 'permission:treatments.view,dashboard.view'
    ]);
    $routes->post('tratamentos/(:num)/alta', 'DischargesController::store/$1', [
        'filter' => 'csrf'
    ]);
    $routes->post('tratamentos/(:num)/reativar', 'TreatmentsController::reactivate/$1', [
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
    $routes->get('financeiro/pdf', 'FinanceController::pdf', [
        'filter' => 'permission:finance.manage,dashboard.view'
    ]);
    $routes->post('financeiro/gerar-mensalidades', 'FinanceController::generateMonthly', [
        'filter' => 'csrf'
    ]);
    $routes->post('financeiro/(:num)/pagar', 'FinanceController::pay/$1', [
        'filter' => 'csrf'
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

    $routes->get('cronograma', 'ScheduleController::index', [
        'filter' => 'permission:schedule.manage,coordinator.view,settings.manage,users.manage'
    ]);
    $routes->post('cronograma', 'ScheduleController::store', [
        'filter' => 'csrf'
    ]);
    $routes->post('cronograma/(:num)', 'ScheduleController::update/$1', [
        'filter' => 'csrf'
    ]);
    $routes->post('cronograma/(:num)/excluir', 'ScheduleController::delete/$1', [
        'filter' => 'csrf'
    ]);

    $routes->get('coordenacao', 'CoordinatorController::index', [
        'filter' => 'permission:coordinator.view,incidents.manage,users.manage'
    ]);
    $routes->post('coordenacao/ocorrencias', 'CoordinatorController::storeIncident', [
        'filter' => 'csrf'
    ]);
    $routes->get('notificacoes/pendentes', 'NotificationsController::pending', [
        'filter' => 'permission:dashboard.view,coordinator.view'
    ]);
    $routes->post('notificacoes/(:num)/lida', 'NotificationsController::markRead/$1', [
        'filter' => 'csrf'
    ]);
    $routes->post('tratamentos/(:num)/chamar-coordenacao', 'NotificationsController::callCoordinator/$1', [
        'filter' => 'csrf'
    ]);

    $routes->get('documentos/(:num)/pdf', 'DocumentsController::pdf/$1', [
        'filter' => 'permission:documents.manage,dashboard.view'
    ]);
    $routes->post('documentos/(:num)/assinado', 'DocumentsController::uploadSigned/$1', [
        'filter' => 'csrf'
    ]);
    $routes->post('portal-familiar/(:num)/enviar-whatsapp', 'PortalController::sendFamilyAccess/$1', [
        'filter' => 'csrf'
    ]);
    $routes->post('portal-familiar/(:num)/arquivos', 'PortalController::updateFiles/$1', [
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

    $routes->get('painel-administrativo', 'AdminPanelController::index', [
        'filter' => 'permission:settings.manage,permissions.manage,users.manage'
    ]);
    $routes->post('painel-administrativo/configuracoes', 'AdminPanelController::updateSettings', [
        'filter' => 'csrf'
    ]);
    $routes->post('painel-administrativo/permissoes', 'AdminPanelController::updatePermissions', [
        'filter' => 'csrf'
    ]);
    $routes->post('painel-administrativo/tipos-ocorrencia', 'AdminPanelController::storeIncidentType', [
        'filter' => 'csrf'
    ]);
    $routes->post('painel-administrativo/tipos-ocorrencia/(:num)', 'AdminPanelController::updateIncidentType/$1', [
        'filter' => 'csrf'
    ]);
    $routes->post('painel-administrativo/tipos-ocorrencia/(:num)/excluir', 'AdminPanelController::deleteIncidentType/$1', [
        'filter' => 'csrf'
    ]);

    $routes->get('contas-a-pagar', 'PayablesController::index', [
        'filter' => 'permission:payables.manage,finance.manage,users.manage'
    ]);
    $routes->post('contas-a-pagar', 'PayablesController::store', [
        'filter' => 'csrf'
    ]);
    $routes->post('contas-a-pagar/(:num)/pagar', 'PayablesController::pay/$1', [
        'filter' => 'csrf'
    ]);

});
