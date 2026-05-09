<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">

<div class="row">
    <div class="col-lg-4">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Novo evento</h3></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('agenda') ?>">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label>Titulo</label>
                        <input name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tratamento</label>
                        <select name="treatment_id" class="form-control">
                            <option value="">Clinica</option>
                            <?php foreach ($treatments as $treatment): ?>
                                <option value="<?= $treatment['id'] ?>"><?= esc($treatment['patient_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Categoria</label>
                        <select name="category" class="form-control">
                            <option value="medico">Medico</option>
                            <option value="psicologico">Psicologico</option>
                            <option value="terapeutico">Terapeutico</option>
                            <option value="enfermagem">Enfermagem</option>
                            <option value="financeiro">Financeiro</option>
                            <option value="administrativo">Administrativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Inicio</label>
                        <input type="datetime-local" name="starts_at" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Fim</label>
                        <input type="datetime-local" name="ends_at" class="form-control">
                    </div>
                    <button class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body"><div id="calendar"></div></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        editable: true,
        events: '<?= base_url('agenda/eventos') ?>',
        eventDrop: saveEventDate,
        eventResize: saveEventDate
    }).render();

    function saveEventDate(info) {
        var data = new FormData();
        data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        data.append('starts_at', info.event.start ? info.event.start.toISOString().slice(0, 19).replace('T', ' ') : '');
        data.append('ends_at', info.event.end ? info.event.end.toISOString().slice(0, 19).replace('T', ' ') : '');

        fetch('<?= base_url('agenda/eventos') ?>/' + info.event.id + '/mover', { method: 'POST', body: data })
            .then(function (response) { if (!response.ok) { info.revert(); } })
            .catch(function () { info.revert(); });
    }
});
</script>
<?= $this->endSection() ?>
