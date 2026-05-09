<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card card-outline card-success">
    <div class="card-header"><h3 class="card-title">Prontuario - <?= esc($treatment['patient_name']) ?></h3></div>
    <div class="card-body">
        <form method="post" action="<?= base_url('tratamentos/' . $treatment['id'] . '/prontuario') ?>">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Tipo</label>
                    <select name="type" class="form-control" required>
                        <option value="medico">Medico</option>
                        <option value="psicologico">Psicologico</option>
                        <option value="terapeutico">Terapeutico</option>
                        <option value="enfermagem">Enfermagem/SAE</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Data</label>
                    <input type="datetime-local" name="recorded_at" class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <label>Titulo</label>
                    <input name="title" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Registro</label>
                <textarea name="content" class="form-control" rows="5" required></textarea>
            </div>
            <div class="card card-outline card-info">
                <div class="card-header"><h3 class="card-title">SAE</h3></div>
                <div class="card-body">
                    <textarea name="sae_collection" class="form-control mb-2" rows="2" placeholder="Coleta"></textarea>
                    <textarea name="sae_diagnosis" class="form-control mb-2" rows="2" placeholder="Diagnostico"></textarea>
                    <textarea name="sae_planning" class="form-control mb-2" rows="2" placeholder="Planejamento"></textarea>
                    <textarea name="sae_execution" class="form-control mb-2" rows="2" placeholder="Execucao"></textarea>
                    <textarea name="sae_evaluation" class="form-control" rows="2" placeholder="Avaliacao"></textarea>
                </div>
            </div>
            <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" name="create_event" value="1" class="custom-control-input" id="create_event">
                <label for="create_event" class="custom-control-label">Exibir tambem na agenda</label>
            </div>
            <button class="btn btn-success">Salvar</button>
            <a href="<?= base_url('tratamentos/' . $treatment['id']) ?>" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
