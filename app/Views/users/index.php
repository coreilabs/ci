<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="card card-outline card-primary">

        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">

            <h3 class="card-title mb-2 mb-md-0">
                Usuários
            </h3>

            <?php if (hasPermission('users.manage')): ?>

                <a href="<?= base_url('usuarios/create') ?>"
                   class="btn btn-primary btn-sm">

                    <i class="fas fa-plus"></i> Novo

                </a>

            <?php endif; ?>

        </div>

        <div class="card-body p-2">

            <table id="tabelaUsuarios"
                   class="table table-bordered table-striped table-hover w-100">

                <thead>

                    <tr>

                        <th width="40"></th>

                        <th>Nome</th>

                        <th>Email</th>

                        <th>Cargo</th>

                        <th width="120">Ações</th>

                    </tr>

                </thead>

                <tbody></tbody>

            </table>

        </div>

    </div>

</div>

<style>

    .dataTables_wrapper {

        width: 100%;

    }

    table.dataTable {

        width: 100% !important;

        margin-top: 0 !important;

    }

    #tabelaUsuarios td,
    #tabelaUsuarios th {

        vertical-align: middle;

    }

    /* COLUNA DO + */

    #tabelaUsuarios tbody > tr:not(.child) > td:first-child {

        width: 40px !important;

        min-width: 40px !important;

        max-width: 40px !important;

        padding: 0 !important;

        text-align: center !important;

        vertical-align: middle !important;

    }

    /* BOTÃO EXPANDIR */

    .expand-row {

        width: 100%;

        min-height: 40px;

        display: flex !important;

        align-items: center;

        justify-content: center;

        cursor: pointer;

    }

    /* ÍCONE */

    .expand-row-icon {

        color: #01aaaf !important;

        font-size: 15px;

        transition: .2s ease;

    }

    .expand-row-icon.fa-minus {

        color: #dc3545 !important;

    }

    /* CHILD ROW */

    tr.child {

        background: transparent !important;

    }

    .child-table {

        width: 100% !important;

        table-layout: auto !important;

        border-collapse: collapse;

    }

    .child-table tr {

        border-bottom: 1px solid rgba(255,255,255,.05);

    }

    .child-table td {

        padding: 8px 10px !important;

        border: 0 !important;

        vertical-align: top;

        white-space: normal !important;

        word-break: normal !important;

        overflow-wrap: break-word !important;

    }

    /* LABEL */

    .child-label {

        width: 1% !important;

        white-space: nowrap !important;

        font-weight: 700;

        padding-right: 12px !important;

    }

    .child-table tr td:first-child {

        width: 1% !important;

        white-space: nowrap !important;

    }

    .child-table tr td:last-child {

        width: auto !important;

    }

    /* MOBILE */

    @media (max-width: 768px) {

        #tabelaUsuarios td,
        #tabelaUsuarios th {

            white-space: normal !important;

            word-break: normal !important;

            overflow-wrap: break-word !important;

        }

        .btn-group-sm > .btn,
        .btn-sm {

            padding: .20rem .35rem;

            font-size: .70rem;

        }

        .child-table td {

            font-size: 14px;

        }

    }

</style>

<?= $this->section('scripts') ?>

<script>

$(function () {

    var table = $('#tabelaUsuarios').DataTable({

        processing: true,

        serverSide: true,

        responsive: false,

        autoWidth: false,

        paging: true,

        searching: true,

        ordering: true,

        info: true,

        lengthChange: true,

        pageLength: 10,

        ajax: {

            url: "<?= base_url('usuarios/ajax-list') ?>",

            type: "POST"

        },

        order: [[1, 'asc']],

        columnDefs: [

            {
                orderable: false,
                searchable: false,
                targets: [0, 4]
            }

        ],

        language: {

            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json'

        }

    });

    function isMobile() {

        return window.innerWidth <= 768;

    }

    function updateColumns() {

        if (isMobile()) {

            // MOBILE

            table.column(0).visible(true);

            table.column(2).visible(false);

            table.column(3).visible(false);

        } else {

            // DESKTOP

            table.column(0).visible(false);

            table.column(2).visible(true);

            table.column(3).visible(true);

        }

        table.columns.adjust().draw(false);

    }

    updateColumns();

    $(window).on('resize', function () {

        updateColumns();

    });

    $('#tabelaUsuarios tbody').on('click', '.expand-row', function () {

        if (!isMobile()) {
            return;
        }

        var tr = $(this).closest('tr');

        var row = table.row(tr);

        var icon = $(this).find('i');

        var data = row.data();

        if (row.child.isShown()) {

            row.child.hide();

            tr.removeClass('shown');

            icon.removeClass('fa-minus')
                .addClass('fa-plus');

        } else {

           row.child(

    '<div class="child-wrapper">'+

        '<div class="child-item">'+
            '<span class="child-label">Email:</span>'+
            '<span class="child-value">'+data[2]+'</span>'+
        '</div>'+

        '<div class="child-item">'+
            '<span class="child-label">Cargo:</span>'+
            '<span class="child-value">'+data[3]+'</span>'+
        '</div>'+

    '</div>'

).show();

            tr.addClass('shown');

            icon.removeClass('fa-plus')
                .addClass('fa-minus');

        }

    });

});

</script>

<?= $this->endSection() ?>

<?= $this->endSection() ?>