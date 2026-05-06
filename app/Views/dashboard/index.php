<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="row">

        <!-- CARD 1 -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>1</h3>
                    <p>Usuários</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <!-- CARD 2 -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>OK</h3>
                    <p>Sistema ativo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        </div>

    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Bem-vindo</h3>
        </div>
        <div class="card-body">
            Olá, <b><?= esc(session('user.name')) ?></b>
        </div>
    </div>

</div>

<?= $this->endSection() ?>