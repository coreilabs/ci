<?php
$currentSegment = service('uri')->getSegment(1);
$adminSegments = [
    'painel-administrativo',
    'administrativo-clinico',
    'cronograma',
    'usuarios',
];
$adminMenuOpen = in_array($currentSegment, $adminSegments, true);
$canAdminArea = hasPermission('clinical_admin.manage')
    || hasPermission('settings.manage')
    || hasPermission('permissions.manage')
    || hasPermission('users.manage')
    || hasPermission('schedule.manage')
    || hasPermission('coordinator.view');
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="<?= base_url('painel') ?>" class="brand-link">
        <span class="brand-text font-weight-light galada-regular text-xl">Amor Fraterno</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image"></div>
            <div class="info">
                <a href="#" class="d-block">Ola <?= esc(session('user.name')) ?>.</a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" data-accordion="false" role="menu">
                <?php if (hasPermission('dashboard.view')): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('painel') ?>" class="nav-link <?= $currentSegment === 'painel' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Painel</p>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-header">COMUNIDADE TERAPEUTICA</li>

                <?php if (hasPermission('admissions.manage') || hasPermission('dashboard.view')): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('admissoes') ?>" class="nav-link <?= $currentSegment === 'admissoes' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-hospital-user"></i>
                            <p>Admissoes</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('treatments.view') || hasPermission('dashboard.view')): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('tratamentos') ?>" class="nav-link <?= $currentSegment === 'tratamentos' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-notes-medical"></i>
                            <p>Tratamentos</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('divisao-pacientes') ?>" class="nav-link <?= $currentSegment === 'divisao-pacientes' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user-md"></i>
                            <p>Divisao de Acolhidos</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('coordinator.view') || hasPermission('incidents.manage')): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('coordenacao') ?>" class="nav-link <?= $currentSegment === 'coordenacao' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-bell"></i>
                            <p>Coordenacao</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('finance.manage') || hasPermission('dashboard.view')): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('financeiro') ?>" class="nav-link <?= $currentSegment === 'financeiro' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p>Financeiro</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('payables.manage') || hasPermission('finance.manage')): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('contas-a-pagar') ?>" class="nav-link <?= $currentSegment === 'contas-a-pagar' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-receipt"></i>
                            <p>Contas a Pagar</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasPermission('calendar.manage') || hasPermission('dashboard.view')): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('agenda') ?>" class="nav-link <?= $currentSegment === 'agenda' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Agenda</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($canAdminArea): ?>
                    <li class="nav-header">ADMINISTRACAO</li>
                    <li class="nav-item has-treeview <?= $adminMenuOpen ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $adminMenuOpen ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Administracao<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview" style="<?= $adminMenuOpen ? 'display: block;' : '' ?>">
                            <?php if (hasPermission('settings.manage') || hasPermission('permissions.manage')): ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('painel-administrativo') ?>" class="nav-link <?= $currentSegment === 'painel-administrativo' ? 'active' : '' ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Painel Administrativo</p>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('clinical_admin.manage')): ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('administrativo-clinico') ?>" class="nav-link <?= $currentSegment === 'administrativo-clinico' ? 'active' : '' ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Modelos e Documentos</p>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('schedule.manage') || hasPermission('coordinator.view') || hasPermission('settings.manage')): ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('cronograma') ?>" class="nav-link <?= $currentSegment === 'cronograma' ? 'active' : '' ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Cronograma</p>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('users.manage')): ?>
                                <li class="nav-item">
                                    <a href="<?= base_url('usuarios') ?>" class="nav-link <?= $currentSegment === 'usuarios' ? 'active' : '' ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Usuarios</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>
