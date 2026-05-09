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

<div class="modal fade" id="billingModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cobranca mensal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="billing_event_id">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Paciente</dt>
                    <dd class="col-sm-8" id="billing_patient"></dd>
                    <dt class="col-sm-4">Valor</dt>
                    <dd class="col-sm-8" id="billing_amount"></dd>
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8" id="billing_status"></dd>
                    <dt class="col-sm-4">Vencimento</dt>
                    <dd class="col-sm-8" id="billing_due"></dd>
                </dl>
                <div class="form-group mt-3">
                    <label>Nova data de vencimento</label>
                    <input type="date" id="billing_new_due_date" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="markPaidBtn">Marcar como pago</button>
                <button type="button" class="btn btn-primary" id="rescheduleBtn">Reagendar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        editable: true,
        events: '<?= base_url('agenda/eventos') ?>',
        eventDrop: saveEventDate,
        eventResize: saveEventDate,
        eventClick: openEvent
    });

    calendar.render();

    function saveEventDate(info) {
        var data = new FormData();
        data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        data.append('starts_at', info.event.start ? info.event.start.toISOString().slice(0, 19).replace('T', ' ') : '');
        data.append('ends_at', info.event.end ? info.event.end.toISOString().slice(0, 19).replace('T', ' ') : '');

        fetch('<?= base_url('agenda/eventos') ?>/' + info.event.id + '/mover', { method: 'POST', body: data })
            .then(function (response) { if (!response.ok) { info.revert(); } })
            .catch(function () { info.revert(); });
    }

    function openEvent(info) {
        var props = info.event.extendedProps || {};
        if (props.source_type !== 'finance') {
            return;
        }

        $('#billing_event_id').val(info.event.id);
        $('#billing_patient').text(props.patient_name || 'Paciente');
        $('#billing_amount').text(formatMoney(props.amount || 0));
        $('#billing_status').text(props.financial_status === 'paid' ? 'Pago' : 'Aberto');
        $('#billing_due').text(props.due_date || '');
        $('#billing_new_due_date').val(props.due_date || '');
        $('#markPaidBtn').prop('disabled', props.financial_status === 'paid');
        $('#billingModal').modal('show');
    }

    function formatMoney(value) {
        return Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function postEventAction(action, fields) {
        var eventId = $('#billing_event_id').val();
        var data = new FormData();
        data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        Object.keys(fields || {}).forEach(function (key) { data.append(key, fields[key]); });

        return fetch('<?= base_url('agenda/eventos') ?>/' + eventId + '/' + action, { method: 'POST', body: data })
            .then(function (response) {
                if (!response.ok) { throw new Error('Falha ao atualizar cobranca.'); }
                $('#billingModal').modal('hide');
                calendar.refetchEvents();
            });
    }

    $('#markPaidBtn').on('click', function () {
        postEventAction('pagar');
    });

    $('#rescheduleBtn').on('click', function () {
        postEventAction('reagendar', { due_date: $('#billing_new_due_date').val() });
    });
});
</script>
<?= $this->endSection() ?>
