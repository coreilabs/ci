<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php
$stepLabels = [
    'responsavel' => 'Responsável',
    'paciente' => 'Acolhido',
    'financeiro' => 'Financeiro Inicial',
    'confirmacao' => 'Confirmação',
];
?>

<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Admissões</h3>
        <a href="<?= base_url('admissoes/iniciar') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Nova Admissão
        </a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Etapa</th>
                    <th>Atualizado Em</th>
                    <th width="160">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($drafts as $draft): ?>
                    <tr>
                        <td><?= esc($draft['id']) ?></td>
                        <td><?= esc($stepLabels[$draft['step']] ?? ucfirst((string) $draft['step'])) ?></td>
                        <td><?= esc(human_datetime($draft['updated_at'])) ?></td>
                        <td>
                            <a class="btn btn-info btn-sm" href="<?= base_url('admissoes/' . $draft['id'] . '/' . $draft['step']) ?>">Continuar</a>
                            <form method="post" action="<?= base_url('admissoes/' . $draft['id'] . '/excluir') ?>" class="d-inline" onsubmit="return confirm('Excluir esta admissão?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                        </td>
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
