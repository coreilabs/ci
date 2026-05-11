<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card card-outline card-primary">
    <div class="card-header"><h3 class="card-title">Financeiro</h3></div>
    <div class="card-body">
        <form class="form-inline mb-3" method="post" action="<?= base_url('financeiro/gerar-mensalidades') ?>">
            <?= csrf_field() ?>
            <input type="month" name="competence" class="form-control mr-2" value="<?= date('Y-m') ?>">
            <button class="btn btn-primary">Gerar mensalidades</button>
        </form>
        <table class="table table-bordered table-hover">
            <thead><tr><th>Paciente</th><th>Competencia</th><th>Descricao</th><th>Valor</th><th>Status</th><th>Acoes</th></tr></thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td><?= esc($entry['patient_name']) ?></td>
                        <td><?= esc(human_month($entry['competence'])) ?></td>
                        <td><?= esc($entry['description']) ?></td>
                        <td>R$ <?= number_format((float) $entry['amount'], 2, ',', '.') ?></td>
                        <td><?= esc($entry['status']) ?></td>
                        <td><?php if ($entry['status'] !== 'paid'): ?><a href="<?= base_url('financeiro/' . $entry['id'] . '/pagar') ?>" class="btn btn-success btn-sm">Pagar</a><?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
