<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="<?= base_url('dashboard') ?>" class="brand-link">
        <span class="brand-text font-weight-light galada-regular text-xl">Amor Fraterno</span>
    </a>

    <div class="sidebar">

          <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <!-- <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image"> -->
        </div>
        <div class="info">
          <a href="#" class="d-block">Olá <?= esc(session('user.name')) ?>.</a>
        </div>
      </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column">

                <!-- Dashboard -->
                <?php if (hasPermission('dashboard.view')): ?>
                <li class="nav-item">
                    <a href="<?= base_url('painel') ?>" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Painel</p>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Usuários -->
                <?php if (hasPermission('users.manage')): ?>
                <li class="nav-item">
                    <a href="<?= base_url('usuarios') ?>" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Usuários</p>
                    </a>
                </li>
                <?php endif; ?>

            </ul>
        </nav>

    </div>

</aside>