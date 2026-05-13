<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">

<div class="card card-outline card-info">
    <div class="card-header"><h3 class="card-title">Atendimentos Psicológicos Da Semana</h3></div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead><tr><th>Acolhido</th><th>Psicólogo</th><th>Data</th></tr></thead>
            <tbody>
                <?php foreach ($weeklyPsychologicalEvents as $event): ?>
                    <tr>
                        <td><?= esc($event['patient_name'] ?? 'Acolhido') ?></td>
                        <td><?= esc($event['professional_name'] ?? 'Não Informado') ?></td>
                        <td><?= esc(human_datetime($event['starts_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($weeklyPsychologicalEvents)): ?>
                    <tr><td colspan="3" class="text-muted text-center">Nenhum atendimento psicológico agendado para esta semana.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Novo Evento</h3></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('agenda') ?>">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label>Título</label>
                        <input name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Internação</label>
                        <select name="treatment_id" class="form-control">
                            <option value="">Clínica</option>
                            <?php foreach ($treatments as $treatment): ?>
                                <option value="<?= $treatment['id'] ?>"><?= esc($treatment['patient_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Categoria</label>
                        <select name="category" class="form-control">
                            <option value="medico">Médico</option>
                            <option value="psicologico">Psicológico</option>
                            <option value="terapeutico">Terapêutico</option>
                            <option value="enfermagem">Enfermagem</option>
                            <option value="financeiro">Financeiro</option>
                            <option value="administrativo">Administrativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Profissional</label>
                        <select name="professional_user_id" class="form-control">
                            <option value="">Não Informado</option>
                            <?php foreach ($professionals as $professional): ?>
                                <option value="<?= $professional['id'] ?>"><?= esc($professional['name']) ?><?= $professional['role_name'] ? ' - ' . esc($professional['role_name']) : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Início</label>
                        <input type="datetime-local" name="starts_at" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Fim</label>
                        <input type="datetime-local" name="ends_at" class="form-control">
                    </div>
                    <button class="btn btn-primary btn-mobile">Salvar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <span class="badge badge-info">Financeiro</span>
                    <span class="badge badge-success">Psicológico</span>
                    <span class="badge badge-warning">Ocorrência</span>
                    <span class="badge badge-secondary">Outros</span>
                </div>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade calendar-day-modal" id="dayModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dayModalTitle">Ocorrências Do Dia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="dayEventList" class="list-group"></div>
                <div id="eventActions" class="calendar-event-actions mt-3 d-none">
                    <h5 id="eventActionTitle"></h5>
                    <p id="eventActionMeta" class="text-muted"></p>
                    <input type="hidden" id="selected_event_id">
                    <div id="financeActions" class="d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Nova Data De Vencimento</label>
                                <input type="date" id="billing_new_due_date" class="form-control mb-2">
                            </div>
                            <div class="col-md-6">
                                <label>Valor Pago</label>
                                <input id="billing_paid_amount" class="form-control mb-2" placeholder="Valor Pago">
                            </div>
                        </div>
                        <label>Mensagem Para WhatsApp</label>
                        <textarea id="billing_whatsapp_message" class="form-control mb-2" rows="4"></textarea>
                        <div class="action-bar">
                            <button type="button" class="btn btn-success btn-sm" id="markPaidBtn">Marcar Como Pago</button>
                            <button type="button" class="btn btn-primary btn-sm" id="rescheduleBtn">Reagendar</button>
                            <a class="btn btn-success btn-sm d-none" target="_blank" id="waBillingBtn">
                                <i class="fab fa-whatsapp"></i> Enviar Cobrança Via wa.me
                            </a>
                        </div>
                        <small class="text-muted d-block mt-2" id="waBillingHelp"></small>
                    </div>
                    <div id="psychologyActions" class="d-none">
                        <form method="post" id="callCoordinatorForm">
                            <?= csrf_field() ?>
                            <button class="btn btn-info btn-sm">Alertar Coordenadores</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Fechar</button></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/pt-br.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var allEvents = [];
    var marker = '•';
    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        buttonText: { today: 'Hoje', month: 'Mês', week: 'Semana', day: 'Dia', list: 'Lista' },
        editable: true,
        eventDisplay: 'block',
        dayMaxEvents: 1,
        moreLinkText: function (num) {
            return '+' + num + (num === 1 ? ' ocorrência' : ' ocorrências');
        },
        moreLinkHint: function (num) {
            return 'Mostrar ' + num + (num === 1 ? ' ocorrência a mais' : ' ocorrências a mais');
        },
        moreLinkDidMount: function (info) {
            var text = 'Mostrar ' + info.num + (info.num === 1 ? ' ocorrência a mais' : ' ocorrências a mais');
            info.el.setAttribute('title', text);
            info.el.setAttribute('aria-label', text);
        },
        moreLinkClick: function (info) {
            openDay(formatCalendarDate(info.date));
            return false;
        },
        events: function (info, success, failure) {
            fetch('<?= base_url('agenda/eventos') ?>')
                .then(function (response) { return response.json(); })
                .then(function (items) {
                    allEvents = items || [];
                    success(allEvents.map(function (event) {
                        return Object.assign({}, event, { title: marker, display: 'block' });
                    }));
                })
                .catch(failure);
        },
        dateClick: function (info) { openDay(info.dateStr); },
        eventClick: function (info) {
            openDay(info.event.startStr.slice(0, 10));
        },
        eventDrop: saveEventDate,
        eventResize: saveEventDate
    });

    calendar.render();

    function formatCalendarDate(date) {
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        return date.getFullYear() + '-' + month + '-' + day;
    }

    function openDay(dateStr) {
        $('#dayModalTitle').text('Ocorrências De ' + dateStr.split('-').reverse().join('/'));
        $('#eventActions').addClass('d-none');
        var events = allEvents.filter(function (event) { return (event.start || '').slice(0, 10) === dateStr; });
        var html = events.map(function (event) {
            var props = event.extendedProps || {};
            var color = props.category === 'financeiro' ? 'info' : (props.category === 'psicologico' ? 'success' : (props.category === 'ocorrencia' ? 'warning' : 'secondary'));
            var title = event.title === marker && props.patient_name ? props.patient_name : event.title;
            return '<button type="button" class="list-group-item list-group-item-action day-event" data-id="' + event.id + '">' +
                '<span class="badge badge-' + color + ' mr-2">' + (props.category || 'Evento') + '</span>' +
                title +
                '</button>';
        }).join('');
        $('#dayEventList').html(html || '<p class="text-muted mb-0">Nenhuma ocorrência neste dia.</p>');
        $('#dayModal').modal('show');
    }

    $(document).on('click', '.day-event', function () {
        $('.day-event').removeClass('active selected');
        $(this).addClass('active selected');

        var eventId = String($(this).data('id'));
        var event = allEvents.find(function (item) { return String(item.id) === eventId; });
        if (! event) { return; }

        var props = event.extendedProps || {};
        $('#selected_event_id').val(event.id);
        $('#eventActionTitle').text(event.title === marker ? (props.patient_name || 'Evento') : event.title);
        $('#eventActionMeta').text((props.patient_name || 'Clínica') + ' | ' + (props.category || 'Evento'));
        $('#financeActions, #psychologyActions').addClass('d-none');
        $('#waBillingBtn').addClass('d-none').removeAttr('href');
        $('#waBillingHelp').text('');

        if (props.source_type === 'finance') {
            var today = new Date().toISOString().slice(0, 10);
            var isOverdue = props.financial_status !== 'paid' && props.due_date && props.due_date < today;
            var isMonthly = /mensalidade/i.test((props.financial_description || '') + ' ' + (event.title || ''));

            $('#billing_new_due_date').val(props.due_date || '');
            $('#billing_paid_amount').val(props.formatted_amount || props.amount || '');
            $('#billing_whatsapp_message').val(props.billing_whatsapp_message || '');
            if (isOverdue && isMonthly) {
                updateWaBillingButton(props.guardian_phone || '');
            } else {
                $('#waBillingHelp').text('Envio manual via wa.me disponível para mensalidades vencidas em aberto.');
            }
            $('#financeActions').removeClass('d-none');
        }

        if (props.source_type === 'psychology_assignment' && props.treatment_id) {
            $('#callCoordinatorForm').attr('action', '<?= base_url('tratamentos') ?>/' + props.treatment_id + '/chamar-coordenacao');
            $('#psychologyActions').removeClass('d-none');
        }

        $('#eventActions').removeClass('d-none');
    });

    $('#billing_whatsapp_message').on('input', function () {
        var eventId = String($('#selected_event_id').val());
        var event = allEvents.find(function (item) { return String(item.id) === eventId; });
        var props = event ? (event.extendedProps || {}) : {};
        var today = new Date().toISOString().slice(0, 10);
        var isOverdue = props.financial_status !== 'paid' && props.due_date && props.due_date < today;
        var isMonthly = /mensalidade/i.test((props.financial_description || '') + ' ' + ((event && event.title) || ''));
        if (isOverdue && isMonthly) {
            updateWaBillingButton(props.guardian_phone || '');
        }
    });

    function updateWaBillingButton(phone) {
        var message = $('#billing_whatsapp_message').val() || '';
        $('#waBillingBtn').addClass('d-none').removeAttr('href');
        if (! phone) {
            $('#waBillingHelp').text('Responsável sem telefone cadastrado.');
            return;
        }
        if (! message.trim()) {
            $('#waBillingHelp').text('Informe uma mensagem para enviar.');
            return;
        }

        $('#waBillingBtn')
            .attr('href', 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message))
            .removeClass('d-none');
        $('#waBillingHelp').text('O WhatsApp será aberto manualmente com número e mensagem preenchidos.');
    }

    function saveEventDate(info) {
        var data = new FormData();
        data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        data.append('starts_at', info.event.start ? info.event.start.toISOString().slice(0, 19).replace('T', ' ') : '');
        data.append('ends_at', info.event.end ? info.event.end.toISOString().slice(0, 19).replace('T', ' ') : '');
        fetch('<?= base_url('agenda/eventos') ?>/' + info.event.id + '/mover', { method: 'POST', body: data })
            .then(function (response) { if (! response.ok) { info.revert(); } calendar.refetchEvents(); })
            .catch(function () { info.revert(); });
    }

    function postEventAction(action, fields) {
        var data = new FormData();
        data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        Object.keys(fields || {}).forEach(function (key) { data.append(key, fields[key]); });
        return fetch('<?= base_url('agenda/eventos') ?>/' + $('#selected_event_id').val() + '/' + action, { method: 'POST', body: data })
            .then(function (response) {
                if (! response.ok) { throw new Error('Falha ao atualizar.'); }
                $('#eventActions').addClass('d-none');
                calendar.refetchEvents();
            });
    }

    $('#markPaidBtn').on('click', function () {
        postEventAction('pagar', { paid_amount: $('#billing_paid_amount').val() });
    });

    $('#rescheduleBtn').on('click', function () {
        postEventAction('reagendar', { due_date: $('#billing_new_due_date').val() });
    });
});
</script>
<?= $this->endSection() ?>
