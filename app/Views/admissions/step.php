<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php
$labels = [
    'responsavel' => 'Responsavel',
    'paciente' => 'Paciente',
    'financeiro' => 'Financeiro inicial',
    'confirmacao' => 'Confirmacao',
];
$current = $payload[$step] ?? [];
?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Admissao #<?= esc($draft['id']) ?> - <?= esc($labels[$step]) ?></h3>
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
                        <label>Nome</label>
                        <input name="name" class="form-control" required value="<?= esc($current['name'] ?? '') ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>CPF</label>
                        <input name="cpf" class="form-control js-cpf" value="<?= esc($current['cpf'] ?? '') ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Parentesco</label>
                        <input name="relationship" class="form-control" value="<?= esc($current['relationship'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Telefone</label>
                        <input name="phone" class="form-control js-phone" value="<?= esc($current['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($current['email'] ?? '') ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Endereco</label>
                        <input name="address" class="form-control" value="<?= esc($current['address'] ?? '') ?>">
                    </div>
                </div>
            <?php elseif ($step === 'paciente'): ?>
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label>Nome</label>
                        <input name="name" class="form-control" required value="<?= esc($current['name'] ?? '') ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>CPF</label>
                        <input name="cpf" class="form-control js-cpf" value="<?= esc($current['cpf'] ?? '') ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Nascimento</label>
                        <input type="date" name="birth_date" class="form-control" value="<?= esc($current['birth_date'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Telefone</label>
                        <input name="phone" class="form-control js-phone" value="<?= esc($current['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group col-md-8">
                        <label>Endereco</label>
                        <input name="address" class="form-control" value="<?= esc($current['address'] ?? '') ?>">
                    </div>
                </div>
            <?php elseif ($step === 'financeiro'): ?>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Data de admissao</label>
                        <input type="date" name="admission_date" class="form-control" required value="<?= esc($current['admission_date'] ?? date('Y-m-d')) ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Matricula</label>
                        <input name="registration_amount" class="form-control js-money" value="<?= esc($current['registration_amount'] ?? '') ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Mensalidade</label>
                        <input name="monthly_amount" class="form-control js-money" value="<?= esc($current['monthly_amount'] ?? '') ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Captador</label>
                        <input name="captor_name" class="form-control" value="<?= esc($current['captor_name'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Permanencia em meses</label>
                        <input type="number" min="1" name="stay_months" class="form-control" required value="<?= esc($current['stay_months'] ?? 6) ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Dia da cobranca</label>
                        <input type="number" min="1" max="28" name="billing_day" class="form-control" required value="<?= esc($current['billing_day'] ?? 10) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Observacoes</label>
                    <textarea name="notes" class="form-control" rows="4"><?= esc($current['notes'] ?? '') ?></textarea>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Revise o contrato gerado. Se precisar, corrija o texto antes de concluir a admissao.</div>
                <div id="editor" style="height: 420px; background: #fff; color: #111;"><?= $contract ?></div>
                <input type="hidden" name="contract_body" id="contract_body">
            <?php endif; ?>

            <button class="btn btn-success mt-3"><?= $step === 'confirmacao' ? 'Concluir admissao' : 'Salvar e continuar' ?></button>
            <a href="<?= base_url('admissoes') ?>" class="btn btn-secondary mt-3">Voltar</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/imask@7.6.1/dist/imask.min.js"></script>
<?php if ($step === 'confirmacao'): ?>
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
var quill = new Quill('#editor', { theme: 'snow' });
$('form').on('submit', function () {
    $('#contract_body').val(quill.root.innerHTML);
});
</script>
<?php endif; ?>
<script>
document.querySelectorAll('.js-cpf').forEach(function (el) { IMask(el, { mask: '000.000.000-00' }); });
document.querySelectorAll('.js-phone').forEach(function (el) { IMask(el, { mask: [{ mask: '(00) 0000-0000' }, { mask: '(00) 00000-0000' }] }); });
document.querySelectorAll('.js-money').forEach(function (el) {
    IMask(el, { mask: 'R$ num', blocks: { num: { mask: Number, scale: 2, thousandsSeparator: '.', radix: ',', mapToRadix: ['.'], padFractionalZeros: true } } });
});
</script>
<?= $this->endSection() ?>
