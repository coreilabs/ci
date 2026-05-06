<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sistema</title>

    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Galada&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

</head>

<body class="hold-transition sidebar-mini dark-mode">

<div class="wrapper">

    <?= $this->include('layouts/navbar') ?>
    <?= $this->include('layouts/sidebar') ?>

    <!-- CONTEÚDO -->
    <div class="content-wrapper p-3">
        <?= $this->renderSection('content') ?>
    </div>

    <?= $this->include('layouts/footer') ?>

</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>
</html>