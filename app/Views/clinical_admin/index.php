<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Modelos PDF</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>Nome</th><th>Categoria</th><th>Versão</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($templates as $template): ?>
                            <tr>
                                <td><?= esc($template['name']) ?></td>
                                <td><?= esc($template['category']) ?></td>
                                <td><?= esc($template['version']) ?></td>
                                <td><a class="btn btn-info btn-sm" href="<?= base_url('administrativo-clinico/modelos/' . $template['id']) ?>">Editar</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
