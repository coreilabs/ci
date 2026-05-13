<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <title>Área da Família</title>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center"><b>Área da Família</b></div>
        <div class="card-body">
            <?= view('partials/alerts') ?>
            <form method="post" action="<?= base_url('familia/' . $token) ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Senha unica</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary btn-block">Entrar</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
