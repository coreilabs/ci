<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>Sistema</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ADMINLTE -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">

    <!-- DATATABLES -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet"
          href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

    <!-- GOOGLE FONTS -->
    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Galada&display=swap"
          rel="stylesheet">

    <!-- CSS PERSONALIZADO -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dark.css') ?>">

</head>

<body class="hold-transition sidebar-mini layout-fixed dark-mode">

<div class="wrapper">

    <?= $this->include('layouts/navbar') ?>

    <?= $this->include('layouts/sidebar') ?>

    <div class="content-wrapper p-3">

        <?= $this->renderSection('content') ?>

    </div>

    <?= $this->include('layouts/footer') ?>

</div>

<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- BOOTSTRAP 4.6 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ADMINLTE -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<!-- DATATABLES -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<!-- DATATABLES RESPONSIVE -->
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<!-- SCRIPTS DAS VIEWS -->
<?= $this->renderSection('scripts') ?>

<script>
(function () {
    if (!window.fetch) { return; }
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';

    function notify(item) {
        if (window.Notification && Notification.permission === 'granted') {
            new Notification(item.title || 'Notificacao', { body: item.body || '' });
        }
        $(document).Toasts && $(document).Toasts('create', {
            class: 'bg-info',
            title: item.title || 'Notificacao',
            body: item.body || ''
        });

        var data = new FormData();
        data.append(csrfName, csrfHash);
        fetch('<?= base_url('notificacoes') ?>/' + item.user_notification_id + '/lida', { method: 'POST', body: data });
    }

    if (window.Notification && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    function poll() {
        fetch('<?= base_url('notificacoes/pendentes') ?>')
            .then(function (response) { return response.ok ? response.json() : []; })
            .then(function (items) { (items || []).forEach(notify); })
            .catch(function () {});
    }

    poll();
    setInterval(poll, 30000);
})();

$(document).on('click', 'tr[data-href]', function (event) {
    if ($(event.target).closest('a, button, input, select, textarea, form').length) {
        return;
    }
    window.location = $(this).data('href');
});
</script>

</body>

</html>
