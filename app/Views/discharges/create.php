<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card card-outline card-warning">
    <div class="card-header"><h3 class="card-title">Alta - <?= esc($treatment['patient_name']) ?></h3></div>
    <div class="card-body">
        <form method="post" action="<?= base_url('tratamentos/' . $treatment['id'] . '/alta') ?>">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Tipo</label>
                    <select name="type" class="form-control">
                        <option value="pedido">Alta a pedido</option>
                        <option value="terapeutica">Alta terapeutica</option>
                        <option value="administrativa">Alta administrativa</option>
                    </select>
                </div>
                <div class="form-group col-md-4"><label>Data</label><input type="date" name="discharged_at" class="form-control" value="<?= date('Y-m-d') ?>"></div>
            </div>
            <div class="form-group"><label>Relatorio final</label><textarea name="summary" rows="7" class="form-control" required></textarea></div>
            <button class="btn btn-warning">Registrar alta</button>
            <a href="<?= base_url('tratamentos/' . $treatment['id']) ?>" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
