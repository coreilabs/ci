<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">

<div class="login-box">
    <div class="card">
        <div class="card-body login-card-body">

            <form action="<?= base_url('login') ?>" method="post">
                <input type="email" name="email" class="form-control mb-2" placeholder="Email">
                <input type="password" name="password" class="form-control mb-2" placeholder="Senha">

                <button class="btn btn-primary btn-block">Entrar</button>
            </form>

        </div>
    </div>
</div>

</body>
</html>