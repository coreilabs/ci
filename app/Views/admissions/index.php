<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Admissoes</h3>
        <a href="<?= base_url('admissoes/iniciar') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Nova admissao
        </a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Etapa</th>
                    <th>Atualizado em</th>
                    <th width="120">Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($drafts as $draft): ?>
                    <tr>
                        <td><?= esc($draft['id']) ?></td>
                        <td><?= esc($draft['step']) ?></td>
                        <td><?= esc($draft['updated_at']) ?></td>
                        <td><a class="btn btn-info btn-sm" href="<?= base_url('admissoes/' . $draft['id'] . '/' . $draft['step']) ?>">Continuar</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($drafts)): ?>
                    <tr><td colspan="4" class="text-muted">Nenhum rascunho aberto.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
