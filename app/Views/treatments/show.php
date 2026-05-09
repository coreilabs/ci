<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><?= esc($treatment['patient_name']) ?></h3>
        <div>
            <?php if ($treatment['status'] === 'active'): ?>
                <a class="btn btn-success btn-sm" href="<?= base_url('tratamentos/' . $treatment['id'] . '/prontuario/novo') ?>">Prontuario</a>
                <a class="btn btn-warning btn-sm" href="<?= base_url('tratamentos/' . $treatment['id'] . '/alta') ?>">Alta</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><b>Tratamento:</b> #<?= esc($treatment['id']) ?></div>
            <div class="col-md-3"><b>Responsavel:</b> <?= esc($treatment['guardian_name']) ?></div>
            <div class="col-md-3"><b>Status:</b> <?= esc($treatment['status']) ?></div>
            <div class="col-md-3"><b>Mensalidade:</b> R$ <?= number_format((float) $treatment['monthly_amount'], 2, ',', '.') ?></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Timeline unica</h3></div>
            <div class="card-body">
                <?php foreach ($records as $record): ?>
                    <div class="timeline-item border-bottom pb-2 mb-3">
                        <b><?= esc($record['title']) ?></b>
                        <span class="badge badge-secondary"><?= esc($record['type']) ?></span>
                        <div class="text-muted"><?= esc($record['recorded_at']) ?></div>
                        <p><?= nl2br(esc($record['content'])) ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($records)): ?><p class="text-muted">Nenhum registro clinico.</p><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Contratos e documentos</h3></div>
            <div class="card-body">
                <?php foreach ($contracts as $contract): ?>
                    <div class="border-bottom pb-2 mb-2">
                        <?= esc($contract['title']) ?>
                        <a class="btn btn-outline-info btn-xs float-right" target="_blank" href="<?= base_url('contratos/' . $contract['id'] . '/pdf') ?>">PDF</a>
                    </div>
                <?php endforeach; ?>
                <?php foreach ($documents as $document): ?>
                    <form class="border-bottom pb-2 mb-2" method="post" enctype="multipart/form-data" action="<?= base_url('documentos/' . $document['id'] . '/assinado') ?>">
                        <?= csrf_field() ?>
                        <?= esc($document['name']) ?> <span class="badge badge-secondary">v<?= esc($document['version']) ?></span>
                        <a class="btn btn-outline-info btn-xs ml-2" target="_blank" href="<?= base_url('documentos/' . $document['id'] . '/pdf') ?>">PDF</a>
                        <input type="file" name="signed_file" class="form-control-file mt-2">
                        <button class="btn btn-outline-success btn-xs mt-1">Upload assinado</button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Financeiro</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-bordered">
                    <thead><tr><th>Competencia</th><th>Descricao</th><th>Valor</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($financial as $entry): ?>
                            <tr>
                                <td><?= esc($entry['competence']) ?></td>
                                <td><?= esc($entry['description']) ?></td>
                                <td>R$ <?= number_format((float) $entry['amount'], 2, ',', '.') ?></td>
                                <td><?= esc($entry['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
