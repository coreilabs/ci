<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Nova conta</h3></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('contas-a-pagar') ?>">
                    <?= csrf_field() ?>
                    <input name="payee_name" class="form-control mb-2" placeholder="Favorecido" required>
                    <input name="description" class="form-control mb-2" placeholder="Descrição" required>
                    <input name="category" class="form-control mb-2" placeholder="Categoria" value="Despesa">
                    <input type="month" name="competence" class="form-control mb-2">
                    <input type="date" name="due_date" class="form-control mb-2">
                    <input name="amount" class="form-control mb-2" placeholder="Valor" required>
                    <button class="btn btn-primary btn-mobile">Salvar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card card-outline card-warning">
            <div class="card-header"><h3 class="card-title">Contas a pagar</h3></div>
            <div class="card-body">
                <form method="get" class="filters-box mb-3">
                    <select name="status" class="form-control d-inline-block w-auto mr-2">
                        <option value="">Todos</option>
                        <option value="open" <?= ($filters['status'] ?? '') === 'open' ? 'selected' : '' ?>>Aberto</option>
                        <option value="paid" <?= ($filters['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Pago</option>
                    </select>
                    <label class="mr-2"><input type="checkbox" name="overdue" value="1" <?= ! empty($filters['overdue']) ? 'checked' : '' ?>> Vencidas</label>
                    <button class="btn btn-info btn-sm">Filtrar</button>
                </form>
                <table id="payablesTable" class="table table-bordered table-hover">
                    <thead><tr><th>Favorecido</th><th>Descrição</th><th>Vencimento</th><th>Valor</th><th>Status</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($entries as $entry): ?>
                            <?php $late = $entry['status'] === 'open' && $entry['due_date'] && $entry['due_date'] < date('Y-m-d'); ?>
                            <tr class="<?= $late ? 'table-danger finance-overdue' : '' ?>">
                                <td><?= esc($entry['payee_name']) ?></td>
                                <td><?= esc($entry['description']) ?></td>
                                <td><?= esc(human_date($entry['due_date'])) ?></td>
                                <td>R$ <?= number_format((float) $entry['amount'], 2, ',', '.') ?></td>
                                <td><?= $entry['status'] === 'paid' ? 'Pago' : ($late ? 'Vencido' : 'Aberto') ?></td>
                                <td>
                                    <?php if ($entry['status'] !== 'paid'): ?>
                                        <form method="post" action="<?= base_url('contas-a-pagar/' . $entry['id'] . '/pagar') ?>">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-success btn-sm">Pagar</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
    $('#payablesTable').DataTable({
        responsive: true,
        autoWidth: false,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json' }
    });
});
</script>
<?= $this->endSection() ?>
