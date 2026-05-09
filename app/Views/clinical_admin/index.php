<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Cadastro administrativo</h3></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('administrativo-clinico/registros') ?>">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label>Categoria</label>
                        <select name="category" class="form-control">
                            <option value="juridico">Juridico</option>
                            <option value="contabilidade">Contabilidade</option>
                            <option value="agua">Tratamento de agua</option>
                            <option value="piscina">Limpeza de piscina</option>
                            <option value="residuos">Residuos infectantes</option>
                            <option value="sanitario">Vigilancia sanitaria</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Nome</label><input name="name" class="form-control" required></div>
                    <div class="form-group"><label>Vencimento</label><input type="date" name="due_date" class="form-control"></div>
                    <div class="form-group"><label>Status</label><input name="status" class="form-control" value="active"></div>
                    <button class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Modelos PDF</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>Nome</th><th>Categoria</th><th>Versao</th><th>Acoes</th></tr></thead>
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
        <div class="card">
            <div class="card-header"><h3 class="card-title">Registros administrativos</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>Categoria</th><th>Nome</th><th>Vencimento</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($records as $record): ?>
                            <tr>
                                <td><?= esc($record['category']) ?></td>
                                <td><?= esc($record['name']) ?></td>
                                <td><?= esc($record['due_date']) ?></td>
                                <td><?= esc($record['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
