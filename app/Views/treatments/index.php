<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php
$statusLabels = [
    'active' => 'Ativo',
    'discharged' => 'Alta',
];
?>

<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h3 class="card-title">Internacoes</h3>
        <a href="<?= base_url('admissoes/iniciar') ?>" class="btn btn-primary btn-sm btn-mobile"><i class="fas fa-plus"></i> Admissão</a>
    </div>
    <div class="card-body">
        <form class="filters-box mb-3" method="get" action="<?= base_url('tratamentos') ?>">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Acolhido</label>
                    <input name="acolhido" class="form-control" value="<?= esc($filters['acolhido'] ?? '') ?>">
                </div>
                <div class="form-group col-md-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">Todos</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Ativos</option>
                        <option value="discharged" <?= ($filters['status'] ?? '') === 'discharged' ? 'selected' : '' ?>>Alta/inativos</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Contratos concluidos em</label>
                    <input type="month" name="contract_end_month" class="form-control" value="<?= esc($filters['contract_end_month'] ?? '') ?>">
                </div>
                <div class="form-group col-md-2 d-flex align-items-end">
                    <button class="btn btn-info btn-mobile"><i class="fas fa-filter"></i> Filtrar</button>
                </div>
            </div>
        </form>

        <table id="treatmentsTable" class="table table-bordered table-hover table-clickable w-100">
            <thead>
                <tr>
                    <th>Acolhido</th>
                    <th>Responsável</th>
                    <th>Admissão</th>
                    <th>Conclusao prevista</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($treatments as $treatment): ?>
                    <?php
                    $statusLabel = $statusLabels[$treatment['status']] ?? $treatment['status'];
                    $endDate = date('Y-m-d', strtotime($treatment['admission_date'] . ' +' . (int) ($treatment['stay_months'] ?? 1) . ' month'));
                    ?>
                    <tr data-href="<?= base_url('tratamentos/' . $treatment['id']) ?>">
                        <td><?= esc($treatment['patient_name']) ?></td>
                        <td><?= esc($treatment['guardian_name']) ?></td>
                        <td><?= esc(human_date($treatment['admission_date'])) ?></td>
                        <td><?= esc(human_date($endDate)) ?></td>
                        <td><span class="badge <?= $treatment['status'] === 'active' ? 'badge-success' : 'badge-secondary' ?>"><?= esc($statusLabel) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
    $('#treatmentsTable').DataTable({
        responsive: true,
        autoWidth: false,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json' }
    });
});
</script>
<?= $this->endSection() ?>
