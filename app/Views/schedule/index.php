<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Nova Atividade</h3></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('cronograma') ?>">
                    <?= csrf_field() ?>
                    <input name="day_label" class="form-control mb-2" placeholder="Dia ou período" required>
                    <div class="form-row">
                        <div class="col-6"><input type="time" name="starts_at" class="form-control mb-2"></div>
                        <div class="col-6"><input type="time" name="ends_at" class="form-control mb-2"></div>
                    </div>
                    <input name="activity" class="form-control mb-2" placeholder="Atividade" required>
                    <input name="audience" class="form-control mb-2" placeholder="Público / setor">
                    <input type="number" name="sort_order" class="form-control mb-2" placeholder="Ordem" value="0">
                    <textarea name="notes" class="form-control mb-2" rows="3" placeholder="Observações"></textarea>
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="schedule_active" name="active" value="1" checked>
                        <label class="custom-control-label" for="schedule_active">Ativo</label>
                    </div>
                    <button class="btn btn-primary btn-mobile">Adicionar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Cronograma</h3></div>
            <div class="card-body">
                <?php foreach ($items as $item): ?>
                    <form method="post" action="<?= base_url('cronograma/' . $item['id']) ?>" class="schedule-row border rounded p-3 mb-3">
                        <?= csrf_field() ?>
                        <div class="form-row">
                            <div class="col-md-3"><input name="day_label" class="form-control mb-2" value="<?= esc($item['day_label']) ?>" required></div>
                            <div class="col-md-2"><input type="time" name="starts_at" class="form-control mb-2" value="<?= esc($item['starts_at']) ?>"></div>
                            <div class="col-md-2"><input type="time" name="ends_at" class="form-control mb-2" value="<?= esc($item['ends_at']) ?>"></div>
                            <div class="col-md-3"><input name="activity" class="form-control mb-2" value="<?= esc($item['activity']) ?>" required></div>
                            <div class="col-md-2"><input type="number" name="sort_order" class="form-control mb-2" value="<?= esc($item['sort_order']) ?>"></div>
                        </div>
                        <input name="audience" class="form-control mb-2" value="<?= esc($item['audience']) ?>" placeholder="Público / setor">
                        <textarea name="notes" class="form-control mb-2" rows="2"><?= esc($item['notes']) ?></textarea>
                        <div class="action-bar">
                            <div class="custom-control custom-checkbox mr-2">
                                <input type="checkbox" class="custom-control-input" id="schedule_active_<?= $item['id'] ?>" name="active" value="1" <?= $item['active'] ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="schedule_active_<?= $item['id'] ?>">Ativo</label>
                            </div>
                            <button class="btn btn-success btn-sm">Salvar</button>
                            <button type="submit"
                                    form="delete_schedule_<?= $item['id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Excluir esta atividade?')">Excluir</button>
                        </div>
                    </form>
                    <form id="delete_schedule_<?= $item['id'] ?>" method="post" action="<?= base_url('cronograma/' . $item['id'] . '/excluir') ?>">
                        <?= csrf_field() ?>
                    </form>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <p class="text-muted mb-0">Nenhuma atividade cadastrada.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
