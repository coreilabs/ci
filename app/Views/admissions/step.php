<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php
$labels = [
    'responsavel' => 'Responsável',
    'paciente' => 'Acolhido',
    'financeiro' => 'Financeiro Inicial',
    'confirmacao' => 'Confirmação',
];
$current = $payload[$step] ?? [];
$fieldValue = static fn (string $name, $default = '') => old($name, $current[$name] ?? $default);
$moneyValue = static function (string $name) use ($current): string {
    $value = old($name, $current[$name] ?? '');
    if ($value === '' || $value === null) {
        return '';
    }

    if (is_numeric($value)) {
        return 'R$ ' . number_format((float) $value, 2, ',', '.');
    }

    return (string) $value;
};
?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Admissão #<?= esc($draft['id']) ?> - <?= esc($labels[$step]) ?></h3>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <?php foreach ($steps as $item): ?>
                <span class="badge <?= $item === $step ? 'badge-primary' : 'badge-secondary' ?> mr-1"><?= esc($labels[$item]) ?></span>
            <?php endforeach; ?>
        </div>

        <form method="post" action="<?= base_url('admissoes/' . $draft['id'] . '/' . $step) ?>">
            <?= csrf_field() ?>

            <?php if ($step === 'responsavel'): ?>
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label>Nome do Responsável</label>
                        <input type="text" name="name" class="form-control" required autocomplete="name" placeholder="Nome completo" value="<?= esc($fieldValue('name')) ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>CPF do Responsável</label>
                        <input type="text" name="cpf" class="form-control js-cpf" inputmode="numeric" autocomplete="off" placeholder="000.000.000-00" value="<?= esc($fieldValue('cpf')) ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Nacionalidade</label>
                        <input type="text" name="nationality" class="form-control" placeholder="brasileiro(a)" value="<?= esc($fieldValue('nationality', 'brasileiro(a)')) ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Parentesco</label>
                        <input type="text" name="relationship" class="form-control" placeholder="Ex.: mãe, irmão, cônjuge" value="<?= esc($fieldValue('relationship')) ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Telefone</label>
                        <input type="tel" name="phone" class="form-control js-phone" inputmode="tel" autocomplete="tel" placeholder="(00) 00000-0000" value="<?= esc($fieldValue('phone')) ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>E-mail</label>
                        <input type="email" name="email" class="form-control" autocomplete="email" placeholder="nome@email.com" value="<?= esc($fieldValue('email')) ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>CEP</label>
                        <input type="text" name="zip_code" class="form-control js-cep" inputmode="numeric" autocomplete="postal-code" placeholder="00000-000" value="<?= esc($fieldValue('zip_code')) ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Endereço</label>
                        <input type="text" name="address" class="form-control" autocomplete="street-address" placeholder="Rua, número, bairro e cidade" value="<?= esc($fieldValue('address')) ?>">
                    </div>
                </div>
            <?php elseif ($step === 'paciente'): ?>
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label>Nome do Acolhido</label>
                        <input type="text" name="name" class="form-control" required autocomplete="name" placeholder="Nome completo" value="<?= esc($fieldValue('name')) ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>CPF do Acolhido</label>
                        <input type="text" name="cpf" class="form-control js-cpf" inputmode="numeric" autocomplete="off" placeholder="000.000.000-00" value="<?= esc($fieldValue('cpf')) ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Nacionalidade</label>
                        <input type="text" name="nationality" class="form-control" placeholder="brasileiro(a)" value="<?= esc($fieldValue('nationality', 'brasileiro(a)')) ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Data de Nascimento</label>
                        <input type="date" name="birth_date" class="form-control" value="<?= esc($fieldValue('birth_date')) ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Telefone</label>
                        <input type="tel" name="phone" class="form-control js-phone" inputmode="tel" autocomplete="tel" placeholder="(00) 00000-0000" value="<?= esc($fieldValue('phone')) ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>CEP</label>
                        <input type="text" name="zip_code" class="form-control js-cep" inputmode="numeric" autocomplete="postal-code" placeholder="00000-000" value="<?= esc($fieldValue('zip_code')) ?>">
                    </div>
                    <div class="form-group col-md-5">
                        <label>Endereço</label>
                        <input type="text" name="address" class="form-control" autocomplete="street-address" placeholder="Rua, número, bairro e cidade" value="<?= esc($fieldValue('address')) ?>">
                    </div>
                </div>
            <?php elseif ($step === 'financeiro'): ?>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Data de Admissão</label>
                        <input type="date" name="admission_date" class="form-control" required value="<?= esc($fieldValue('admission_date', date('Y-m-d'))) ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Valor Matrícula</label>
                        <input type="text" name="registration_amount" class="form-control js-money" inputmode="decimal" autocomplete="off" placeholder="R$ 0,00" value="<?= esc($moneyValue('registration_amount')) ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Valor Mensalidade</label>
                        <input type="text" name="monthly_amount" class="form-control js-money" inputmode="decimal" autocomplete="off" placeholder="R$ 0,00" value="<?= esc($moneyValue('monthly_amount')) ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Captador</label>
                        <input type="text" name="captor_name" class="form-control" placeholder="Nome do captador" value="<?= esc($fieldValue('captor_name')) ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Permanência em Meses</label>
                        <input type="number" min="1" max="<?= esc($maxContractMonths) ?>" step="1" name="stay_months" class="form-control" required placeholder="Máximo <?= esc($maxContractMonths) ?>" value="<?= esc(min((int) $fieldValue('stay_months', min(3, (int) $maxContractMonths)), (int) $maxContractMonths)) ?>">
                        <small class="text-muted">Limite configurado: <?= esc($maxContractMonths) ?> meses.</small>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Dia da Cobrança</label>
                        <input type="number" min="1" max="28" step="1" name="billing_day" class="form-control" required placeholder="1 a 28" value="<?= esc($fieldValue('billing_day', 10)) ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>CID</label>
                        <input type="text" name="cid_code" class="form-control text-uppercase" placeholder="Ex.: F19" value="<?= esc($fieldValue('cid_code')) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Observações</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Observações financeiras ou administrativas"><?= esc($fieldValue('notes')) ?></textarea>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Revise o contrato gerado. Se precisar, corrija o texto antes de concluir a admissão.</div>
                <div class="template-variable-box mb-3">
                    <strong>Variáveis disponíveis:</strong>
                    <?php foreach ($templateVariables as $variable): ?>
                        <code><?= esc($variable) ?></code>
                    <?php endforeach; ?>
                </div>
                <div id="editor" class="quill-editor admission-contract-editor"><?= $contract ?></div>
                <input type="hidden" name="contract_body" id="contract_body">
                <div class="custom-control custom-checkbox mt-3">
                    <input type="checkbox" class="custom-control-input" id="create_family_portal" name="create_family_portal" value="1">
                    <label class="custom-control-label admission-family-portal-label" for="create_family_portal">Criar Área da Família com contrato e termos para download</label>
                </div>
                <div class="custom-control custom-checkbox mt-2">
                    <input type="checkbox" class="custom-control-input" id="generate_adendo" name="generate_adendo" value="1">
                    <label class="custom-control-label admission-family-portal-label" for="generate_adendo">Gerar Adendo Contratual nesta admissão</label>
                </div>
            <?php endif; ?>

            <button class="btn btn-success mt-3"><?= $step === 'confirmacao' ? 'Concluir Admissão' : 'Salvar e Continuar' ?></button>
            <a href="<?= base_url('admissoes') ?>" class="btn btn-secondary mt-3">Voltar</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/imask@7.6.1/dist/imask.min.js"></script>
<?php if ($step === 'confirmacao'): ?>
<?= $this->include('partials/quill_assets') ?>
<script>
var quill = new Quill('#editor', {
    theme: 'snow',
    modules: { toolbar: window.fullQuillToolbar }
});

function normalizeAdmissionEditorColors() {
    quill.root.style.backgroundColor = '#ffffff';
    quill.root.style.color = '#111111';
    quill.root.querySelectorAll('*').forEach(function (node) {
        node.style.color = '#111111';
        if (! node.style.backgroundColor || node.style.backgroundColor === 'transparent') {
            node.style.backgroundColor = '#ffffff';
        }
    });
}

normalizeAdmissionEditorColors();
quill.on('text-change', normalizeAdmissionEditorColors);
$('form').on('submit', function () {
    normalizeAdmissionEditorColors();
    $('#contract_body').val(quill.root.innerHTML);
});
</script>
<?php endif; ?>
<script>
function onlyDigits(value) {
    return (value || '').replace(/\D+/g, '');
}

function isValidCpf(value) {
    var cpf = onlyDigits(value);
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
        return false;
    }

    for (var t = 9; t < 11; t++) {
        var sum = 0;
        for (var i = 0; i < t; i++) {
            sum += parseInt(cpf.charAt(i), 10) * ((t + 1) - i);
        }
        var digit = ((10 * sum) % 11) % 10;
        if (parseInt(cpf.charAt(t), 10) !== digit) {
            return false;
        }
    }

    return true;
}

function setCpfValidity(input) {
    var hasValue = onlyDigits(input.value).length > 0;
    var valid = ! hasValue || isValidCpf(input.value);
    input.classList.toggle('is-invalid', ! valid);

    var feedback = input.parentNode.querySelector('.invalid-feedback');
    if (! feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = 'Informe um CPF válido.';
        input.parentNode.appendChild(feedback);
    }

    return valid;
}

document.querySelectorAll('.js-cpf').forEach(function (el) {
    IMask(el, { mask: '000.000.000-00' });
    el.addEventListener('blur', function () { setCpfValidity(el); });
    el.addEventListener('input', function () { if (el.classList.contains('is-invalid')) { setCpfValidity(el); } });
});

document.querySelectorAll('.js-phone').forEach(function (el) {
    IMask(el, { mask: [{ mask: '(00) 0000-0000' }, { mask: '(00) 00000-0000' }] });
});

document.querySelectorAll('.js-cep').forEach(function (el) {
    IMask(el, { mask: '00000-000' });
});

document.querySelectorAll('.js-money').forEach(function (el) {
    IMask(el, {
        mask: 'R$ num',
        lazy: false,
        blocks: {
            num: {
                mask: Number,
                scale: 2,
                thousandsSeparator: '.',
                radix: ',',
                mapToRadix: ['.'],
                padFractionalZeros: true
            }
        }
    });
});

document.querySelector('form').addEventListener('submit', function (event) {
    var invalidCpf = false;
    document.querySelectorAll('.js-cpf').forEach(function (el) {
        if (! setCpfValidity(el)) {
            invalidCpf = true;
        }
    });

    if (invalidCpf) {
        event.preventDefault();
        event.stopPropagation();
    }
});
</script>
<?= $this->endSection() ?>
