<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card card-outline card-primary">
    <div class="card-header"><h3 class="card-title">Financeiro</h3></div>
    <div class="card-body">
        <form class="form-inline mb-3 stack-mobile" method="post" action="<?= base_url('financeiro/gerar-mensalidades') ?>">
            <?= csrf_field() ?>
            <input type="month" name="competence" class="form-control mr-2" value="<?= date('Y-m') ?>">
            <button class="btn btn-primary btn-mobile"><i class="fas fa-calendar-plus"></i> Gerar mensalidades</button>
        </form>

        <form class="filters-box mb-3" method="get" action="<?= base_url('financeiro') ?>">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Acolhido</label>
                    <select name="treatment_id" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($acolhidos as $acolhido): ?>
                            <option value="<?= $acolhido['id'] ?>" <?= (string) ($filters['treatment_id'] ?? '') === (string) $acolhido['id'] ? 'selected' : '' ?>>
                                <?= esc($acolhido['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Competência</label>
                    <input type="month" name="competence" class="form-control" value="<?= esc($filters['competence'] ?? '') ?>">
                </div>
                <div class="form-group col-md-2">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">Todos</option>
                        <option value="open" <?= ($filters['status'] ?? '') === 'open' ? 'selected' : '' ?>>Aberto</option>
                        <option value="paid" <?= ($filters['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Pago</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Tipo</label>
                    <select name="type" class="form-control">
                        <option value="">Todos</option>
                        <option value="mensalidade" <?= ($filters['type'] ?? '') === 'mensalidade' ? 'selected' : '' ?>>Mensalidade</option>
                        <option value="matricula" <?= ($filters['type'] ?? '') === 'matricula' ? 'selected' : '' ?>>Matrícula</option>
                    </select>
                </div>
                <div class="form-group col-md-3 d-flex align-items-end">
                    <div class="custom-control custom-checkbox mr-3">
                        <input type="checkbox" class="custom-control-input" id="overdue" name="overdue" value="1" <?= ! empty($filters['overdue']) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="overdue">Vencidas/inadimplentes</label>
                    </div>
                </div>
            </div>
            <button class="btn btn-info btn-mobile"><i class="fas fa-filter"></i> Filtrar</button>
            <a class="btn btn-secondary btn-mobile" href="<?= base_url('financeiro') ?>">Limpar</a>
            <a class="btn btn-danger btn-mobile" target="_blank" href="<?= base_url('financeiro/pdf?' . http_build_query($filters ?? [])) ?>"><i class="fas fa-file-pdf"></i> PDF</a>
        </form>

        <table id="financeTable" class="table table-bordered table-hover table-clickable">
            <thead><tr><th>Acolhido</th><th>Competência</th><th>Descrição</th><th>Vencimento</th><th>Valor</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                    <?php $late = $entry['status'] === 'open' && $entry['due_date'] && $entry['due_date'] < date('Y-m-d'); ?>
                    <tr class="<?= $late ? 'table-danger finance-overdue' : '' ?>">
                        <td><?= esc($entry['patient_name']) ?></td>
                        <td><?= esc(human_month($entry['competence'])) ?></td>
                        <td><?= esc($entry['description']) ?></td>
                        <td><?= esc(human_date($entry['due_date'])) ?></td>
                        <td>R$ <?= number_format((float) $entry['amount'], 2, ',', '.') ?></td>
                        <td>
                            <span class="badge <?= $entry['status'] === 'paid' ? 'badge-success' : ($late ? 'badge-danger' : 'badge-warning') ?>">
                                <?= $entry['status'] === 'paid' ? 'Pago' : ($late ? 'Vencido' : 'Aberto') ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($entry['status'] !== 'paid'): ?>
                                <form method="post" action="<?= base_url('financeiro/' . $entry['id'] . '/pagar') ?>" class="form-inline">
                                    <?= csrf_field() ?>
                                    <input name="paid_amount" class="form-control form-control-sm mr-1 money-mini" value="<?= number_format((float) $entry['amount'], 2, ',', '.') ?>">
                                    <button class="btn btn-success btn-sm"><i class="fas fa-check"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
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
    $('#financeTable').DataTable({
        responsive: true,
        autoWidth: false,
        order: [[3, 'asc']],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json' }
    });
});
</script>
<?= $this->endSection() ?>
