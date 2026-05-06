<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h3>Novo Usuário</h3>
    </div>

    <div class="card-body">

        <form method="post" action="<?= base_url('usuarios/store') ?>">

            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="name" class="form-control">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="form-group">
                <label>Cargo</label>
                <select name="role_id" class="form-control">
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>">
                            <?= esc($r['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="btn btn-success mt-3">Salvar</button>

        </form>

    </div>
</div>

<?= $this->endSection() ?>