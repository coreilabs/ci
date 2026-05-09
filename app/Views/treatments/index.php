<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Tratamentos</h3>
        <a href="<?= base_url('admissoes/iniciar') ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Admissao</a>
    </div>
    <div class="card-body">
        <table id="treatmentsTable" class="table table-bordered table-hover w-100">
            <thead>
                <tr>
                    <th></th>
                    <th>Paciente</th>
                    <th>Responsavel</th>
                    <th>Admissao</th>
                    <th>Status</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($treatments as $treatment): ?>
                    <tr>
                        <td><i class="fas fa-plus expand-row"></i></td>
                        <td><?= esc($treatment['patient_name']) ?></td>
                        <td><?= esc($treatment['guardian_name']) ?></td>
                        <td><?= esc($treatment['admission_date']) ?></td>
                        <td><?= esc($treatment['status']) ?></td>
                        <td><a href="<?= base_url('tratamentos/' . $treatment['id']) ?>" class="btn btn-info btn-sm">Abrir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
    var table = $('#treatmentsTable').DataTable({ responsive: false, autoWidth: false, language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json' }, columnDefs: [{ targets: 0, orderable: false }] });
    function updateColumns() {
        var mobile = window.innerWidth <= 768;
        table.column(0).visible(mobile);
        table.column(2).visible(!mobile);
        table.column(3).visible(!mobile);
        table.columns.adjust();
    }
    updateColumns();
    $(window).on('resize', updateColumns);
    $('#treatmentsTable tbody').on('click', '.expand-row', function () {
        var tr = $(this).closest('tr'), row = table.row(tr), data = row.data();
        if (row.child.isShown()) { row.child.hide(); $(this).removeClass('fa-minus').addClass('fa-plus'); return; }
        row.child('<div><b>Responsavel:</b> '+data[2]+'<br><b>Admissao:</b> '+data[3]+'<br><b>Status:</b> '+data[4]+'</div>').show();
        $(this).removeClass('fa-plus').addClass('fa-minus');
    });
});
</script>
<?= $this->endSection() ?>
