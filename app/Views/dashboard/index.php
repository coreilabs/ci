<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= esc($activeTreatments ?? 0) ?></h3>
                    <p>Tratamentos ativos</p>
                </div>
                <div class="icon"><i class="fas fa-notes-medical"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>R$ <?= number_format((float) ($monthRevenue ?? 0), 2, ',', '.') ?></h3>
                    <p>Receita do mes</p>
                </div>
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?= esc($openCharges ?? 0) ?></h3>
                    <p>Cobrancas abertas</p>
                </div>
                <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?= esc($lateCharges ?? 0) ?></h3>
                    <p>Inadimplencias</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Mini HIS</h3></div>
        <div class="card-body">
            Paciente -> Tratamento -> Ecossistema operacional. A admissao cria o tratamento, contrato, termos obrigatorios e lancamentos iniciais vinculados ao mesmo eixo.
        </div>
    </div>
</div>

<?= $this->endSection() ?>
