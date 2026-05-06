<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card card-outline card-primary">

    <div class="card-header">
        <h3 class="card-title">Editar Usuário</h3>
    </div>

    <div class="card-body">

        <form method="post"
              action="<?= base_url('users/update/' . $user['id']) ?>"
              id="formUser">

            <!-- NOME -->
            <div class="form-group">
                <label>Nome</label>
                <input type="text"
                       name="name"
                       value="<?= esc($user['name']) ?>"
                       class="form-control"
                       required>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label>Email</label>
                <input type="email"
                       name="email"
                       id="email"
                       value="<?= esc($user['email']) ?>"
                       class="form-control"
                       required>
            </div>

            <!-- CONFIRMAR EMAIL -->
            <div class="form-group">
                <label>Confirmar Email</label>
                <input type="email"
                       id="email_confirm"
                       class="form-control"
                       placeholder="Repita o email"
                       required>
                <small id="emailError" class="text-danger d-none">
                    Os emails não conferem
                </small>
            </div>

            <hr>

            <!-- SENHA -->
            <div class="form-group">
                <label>Nova Senha (opcional)</label>
                <input type="password"
                       name="password"
                       id="password"
                       class="form-control"
                       placeholder="Digite nova senha">
            </div>

            <!-- CONFIRMAR SENHA -->
            <div class="form-group">
                <label>Confirmar Senha</label>
                <input type="password"
                       id="password_confirm"
                       class="form-control"
                       placeholder="Repita a senha">
                <small id="passwordError" class="text-danger d-none">
                    As senhas não conferem
                </small>
            </div>

            <hr>

            <!-- CARGO -->
            <div class="form-group">
                <label>Cargo</label>
                <select name="role_id" class="form-control" required>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>"
                            <?= $r['id'] == $user['role_id'] ? 'selected' : '' ?>>
                            <?= esc($r['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Atualizar
            </button>

        </form>

    </div>
</div>

<!-- VALIDACAO CLIENT SIDE -->
<script>
const email = document.getElementById('email');
const emailConfirm = document.getElementById('email_confirm');
const emailError = document.getElementById('emailError');

const password = document.getElementById('password');
const passwordConfirm = document.getElementById('password_confirm');
const passwordError = document.getElementById('passwordError');

const form = document.getElementById('formUser');

/* EMAIL */
function validateEmail() {
    if (email.value !== emailConfirm.value) {
        emailError.classList.remove('d-none');
        return false;
    } else {
        emailError.classList.add('d-none');
        return true;
    }
}

/* SENHA */
function validatePassword() {
    // só valida se usuário começou a digitar senha
    if (password.value.length === 0 && passwordConfirm.value.length === 0) {
        passwordError.classList.add('d-none');
        return true;
    }

    if (password.value !== passwordConfirm.value) {
        passwordError.classList.remove('d-none');
        return false;
    } else {
        passwordError.classList.add('d-none');
        return true;
    }
}

email.addEventListener('input', validateEmail);
emailConfirm.addEventListener('input', validateEmail);

password.addEventListener('input', validatePassword);
passwordConfirm.addEventListener('input', validatePassword);

form.addEventListener('submit', function (e) {
    const emailOk = validateEmail();
    const passOk = validatePassword();

    if (!emailOk || !passOk) {
        e.preventDefault();
    }
});
</script>

<?= $this->endSection() ?>