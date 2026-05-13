<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isEdit = ! empty($record);
$selectedType = $record['type'] ?? array_key_first($types);
?>

<div class="card card-outline card-success">
    <div class="card-header"><h3 class="card-title"><?= $isEdit ? 'Editar registro' : 'Prontuario' ?> - <?= esc($treatment['patient_name']) ?></h3></div>
    <div class="card-body">
        <form method="post" action="<?= esc($action) ?>">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Tipo</label>
                    <select name="type" id="recordType" class="form-control" required>
                        <?php foreach ($types as $value => $label): ?>
                            <option value="<?= esc($value) ?>" <?= $selectedType === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Data</label>
                    <input type="datetime-local" name="recorded_at" class="form-control" value="<?= $record['recorded_at'] ?? null ? esc(date('Y-m-d\TH:i', strtotime($record['recorded_at']))) : '' ?>">
                </div>
                <div class="form-group col-md-6">
                    <label>Titulo</label>
                    <input name="title" class="form-control" required value="<?= esc($record['title'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Evolucao</label>
                <textarea name="content" class="form-control" rows="5" required><?= esc($record['content'] ?? '') ?></textarea>
            </div>
            <div class="card card-outline card-info" id="saeBox">
                <div class="card-header"><h3 class="card-title">SAE - Sistematizacao da Assistencia de Enfermagem</h3></div>
                <div class="card-body">
                    <textarea name="sae_collection" class="form-control mb-2" rows="2" placeholder="Coleta de dados"><?= esc($record['sae_collection'] ?? '') ?></textarea>
                    <textarea name="sae_diagnosis" class="form-control mb-2" rows="2" placeholder="Diagnóstico de enfermagem"><?= esc($record['sae_diagnosis'] ?? '') ?></textarea>
                    <textarea name="sae_planning" class="form-control mb-2" rows="2" placeholder="Planejamento"><?= esc($record['sae_planning'] ?? '') ?></textarea>
                    <textarea name="sae_execution" class="form-control mb-2" rows="2" placeholder="Execução"><?= esc($record['sae_execution'] ?? '') ?></textarea>
                    <textarea name="sae_evaluation" class="form-control" rows="2" placeholder="Avaliação"><?= esc($record['sae_evaluation'] ?? '') ?></textarea>
                </div>
            </div>
            <?php if (! $isEdit): ?>
                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" name="create_event" value="1" class="custom-control-input" id="create_event">
                    <label for="create_event" class="custom-control-label">Exibir tambem na agenda</label>
                </div>
            <?php endif; ?>
            <button class="btn btn-success btn-mobile"><?= $isEdit ? 'Atualizar' : 'Salvar' ?></button>
            <a href="<?= base_url('tratamentos/' . $treatment['id']) ?>" class="btn btn-secondary btn-mobile">Voltar</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleSae() {
    $('#saeBox').toggle($('#recordType').val() === 'enfermagem');
}
$('#recordType').on('change', toggleSae);
toggleSae();
</script>
<?= $this->endSection() ?>
