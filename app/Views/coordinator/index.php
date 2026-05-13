<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="row">
    <div class="col-lg-5">
        <div class="card card-outline card-warning">
            <div class="card-header"><h3 class="card-title">Registrar Ocorrência</h3></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('coordenacao/ocorrencias') ?>">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label>Acolhido</label>
                        <select name="treatment_id" class="form-control" required>
                            <option value="">Selecione</option>
                            <?php foreach ($acolhidos as $acolhido): ?>
                                <option value="<?= $acolhido['id'] ?>"><?= esc($acolhido['patient_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select name="incident_type_id" class="form-control">
                            <option value="">Ocorrencia geral</option>
                            <?php foreach ($incidentTypes as $type): ?>
                                <option value="<?= $type['id'] ?>"><?= esc($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Titulo</label>
                        <input name="title" class="form-control" required placeholder="Ex.: desrespeitou amigo">
                    </div>
                    <div class="form-group">
                        <label>Data e hora</label>
                        <input type="datetime-local" name="occurred_at" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    <div class="form-group">
                        <label>Detalhes</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <button class="btn btn-warning btn-mobile"><i class="fas fa-bell"></i> Registrar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Acolhidos ativos</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-sm js-table-clickable">
                    <thead><tr><th>Acolhido</th><th>Responsável</th><th>Admissão</th></tr></thead>
                    <tbody>
                        <?php foreach ($acolhidos as $acolhido): ?>
                            <tr data-href="<?= base_url('tratamentos/' . $acolhido['id']) ?>">
                                <td><?= esc($acolhido['patient_name']) ?></td>
                                <td><?= esc($acolhido['guardian_name']) ?></td>
                                <td><?= esc(human_date($acolhido['admission_date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Últimas Ocorrências</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Acolhido</th><th>Ocorrência</th><th>Quando</th><th>Por</th></tr></thead>
                    <tbody>
                        <?php foreach ($incidents as $incident): ?>
                            <tr>
                                <td><?= esc($incident['acolhido_name']) ?></td>
                                <td><?= esc($incident['title']) ?></td>
                                <td><?= esc(human_datetime($incident['occurred_at'])) ?></td>
                                <td><?= esc($incident['created_by_name'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
