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
            <ul class="nav nav-pills nav-sidebar flex-column">
                <?php if (hasPermission('dashboard.view')): ?>
                <li class="nav-item">
                    <a href="<?= base_url('painel') ?>" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Painel</p>
                    </a>
                </li>

                <li class="nav-header">MINI HIS</li>

                <li class="nav-item">
                    <a href="<?= base_url('admissoes') ?>" class="nav-link">
                        <i class="nav-icon fas fa-hospital-user"></i>
                        <p>Admissoes</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('tratamentos') ?>" class="nav-link">
                        <i class="nav-icon fas fa-notes-medical"></i>
                        <p>Tratamentos</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('financeiro') ?>" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Financeiro</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('agenda') ?>" class="nav-link">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Agenda</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('administrativo-clinico') ?>" class="nav-link">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Administrativo</p>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (hasPermission('users.manage')): ?>
                <li class="nav-header">SEGURANCA</li>
                <li class="nav-item">
                    <a href="<?= base_url('usuarios') ?>" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Usuarios</p>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>
