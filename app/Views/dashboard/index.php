<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php $formatMoney = static fn ($value) => 'R$ ' . number_format((float) $value, 2, ',', '.'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= esc($activeTreatments ?? 0) ?></h3>
                    <p>Tratamentos ativos</p>
                </div>
                <div class="icon"><i class="fas fa-notes-medical"></i></div>
                <a href="<?= base_url('tratamentos') ?>" class="small-box-footer">Abrir tratamentos <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?= $formatMoney($monthRevenue ?? 0) ?></h3>
                    <p>Receita recebida no mês</p>
                </div>
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                <a href="<?= base_url('financeiro') ?>" class="small-box-footer">Ver financeiro <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?= esc($openCharges ?? 0) ?></h3>
                    <p>Cobranças abertas</p>
                </div>
                <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <a href="<?= base_url('financeiro') ?>" class="small-box-footer">Total <?= $formatMoney($openAmount ?? 0) ?></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?= esc($lateCharges ?? 0) ?></h3>
                    <p>Inadimplências</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                <a href="<?= base_url('financeiro') ?>" class="small-box-footer">Priorizar cobranças <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pacientes cadastrados</span>
                    <span class="info-box-number"><?= esc($totalPatients ?? 0) ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-purple"><i class="fas fa-user-md"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Sem psicólogo definido</span>
                    <span class="info-box-number"><?= esc($unassignedPsychology ?? 0) ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-teal"><i class="fas fa-file-signature"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Documentos pendentes</span>
                    <span class="info-box-number"><?= esc($pendingDocuments ?? 0) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Agenda de hoje</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('agenda') ?>" class="btn btn-tool"><i class="fas fa-calendar-alt"></i></a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>Horário</th><th>Paciente</th><th>Categoria</th><th>Profissional</th></tr></thead>
                        <tbody>
                            <?php foreach ($todayEvents as $event): ?>
                                <tr>
                                    <td><?= esc(human_time($event['starts_at'])) ?></td>
                                    <td><?= esc($event['patient_name'] ?? 'Clínica') ?></td>
                                    <td><span class="badge badge-secondary"><?= esc($event['category']) ?></span></td>
                                    <td><?= esc($event['professional_name'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($todayEvents)): ?>
                                <tr><td colspan="4" class="text-center text-muted">Nenhum evento para hoje.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Últimas evoluções</h3></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Paciente</th><th>Tipo</th><th>Profissional</th><th>Data</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentRecords as $record): ?>
                                <tr>
                                    <td><?= esc($record['patient_name']) ?></td>
                                    <td><span class="badge badge-info"><?= esc($record['type']) ?></span></td>
                                    <td><?= esc($record['professional_name'] ?? '-') ?></td>
                                    <td><?= esc(human_datetime($record['recorded_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentRecords)): ?>
                                <tr><td colspan="4" class="text-center text-muted">Nenhuma evolução registrada.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Atendimentos psicológicos da semana</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('divisao-pacientes') ?>" class="btn btn-tool"><i class="fas fa-user-plus"></i></a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Paciente</th><th>Psicólogo</th><th>Data</th></tr></thead>
                        <tbody>
                            <?php foreach ($weeklyPsychology as $event): ?>
                                <tr>
                                    <td><?= esc($event['patient_name'] ?? '-') ?></td>
                                    <td><?= esc($event['professional_name'] ?? '-') ?></td>
                                    <td><?= esc(human_datetime($event['starts_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($weeklyPsychology)): ?>
                                <tr><td colspan="3" class="text-center text-muted">Nenhum atendimento psicológico na semana.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card card-outline card-success">
                <div class="card-header"><h3 class="card-title">Carga da psicologia</h3></div>
                <div class="card-body">
                    <?php foreach ($psychologyWorkload as $item): ?>
                        <?php $total = max(1, (int) ($activeTreatments ?? 1)); ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span><?= esc($item['professional_name']) ?></span>
                                <span><?= esc($item['total']) ?> paciente(s)</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: <?= min(100, ((int) $item['total'] / $total) * 100) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($psychologyWorkload)): ?>
                        <p class="text-muted mb-0">Nenhum paciente vinculado à psicologia.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card card-outline card-warning">
                <div class="card-header"><h3 class="card-title">Próximas cobranças</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Paciente</th><th>Vencimento</th><th>Valor</th></tr></thead>
                        <tbody>
                            <?php foreach ($upcomingCharges as $charge): ?>
                                <tr>
                                    <td><?= esc($charge['patient_name']) ?></td>
                                    <td><?= esc(human_date($charge['due_date'])) ?></td>
                                    <td><?= esc($formatMoney($charge['amount'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($upcomingCharges)): ?>
                                <tr><td colspan="3" class="text-center text-muted">Nenhuma cobrança aberta.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
