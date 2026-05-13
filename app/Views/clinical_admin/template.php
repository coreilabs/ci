<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card card-outline card-primary">
    <div class="card-header"><h3 class="card-title">Modelo PDF</h3></div>
    <div class="card-body">
        <form method="post" action="<?= base_url('administrativo-clinico/modelos/' . $template['id']) ?>">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group col-md-5"><label>Nome</label><input name="name" class="form-control" value="<?= esc($template['name']) ?>"></div>
                <div class="form-group col-md-4"><label>Categoria</label><input name="category" class="form-control" value="<?= esc($template['category']) ?>"></div>
                <div class="form-group col-md-3"><label>Versão</label><input type="number" name="version" class="form-control" value="<?= esc($template['version']) ?>"></div>
            </div>
            <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" name="is_required_admission" value="1" class="custom-control-input" id="required" <?= $template['is_required_admission'] ? 'checked' : '' ?>>
                <label class="custom-control-label" for="required">Gerar na admissão</label>
            </div>
            <?php
            $variables = [
                '{{responsavel}}', '{{responsavel_cpf}}', '{{responsavel_telefone}}', '{{responsavel_email}}',
                '{{responsavel_endereco}}', '{{responsavel_cep}}', '{{responsavel_nacionalidade}}',
                '{{acolhido}}', '{{acolhido_cpf}}', '{{acolhido_nascimento}}', '{{acolhido_telefone}}',
                '{{acolhido_endereco}}', '{{acolhido_cep}}', '{{acolhido_nacionalidade}}',
                '{{cid}}', '{{admissao}}', '{{admissao_extenso}}', '{{matricula}}',
                '{{matricula_extenso}}', '{{mensalidade}}', '{{mensalidade_extenso}}',
                '{{permanencia_meses}}', '{{permanencia_meses_extenso}}', '{{dia_cobranca}}',
                '{{vencimentos_mensalidades}}',
            ];
            ?>
            <div class="template-variable-box mb-3">
                <strong>Variáveis disponíveis:</strong>
                <?php foreach ($variables as $variable): ?>
                    <code><?= esc($variable) ?></code>
                <?php endforeach; ?>
            </div>
            <div id="editor" class="quill-editor"><?= $template['body'] ?></div>
            <input type="hidden" name="body" id="body">
            <button class="btn btn-success mt-3">Salvar</button>
            <a href="<?= base_url('administrativo-clinico') ?>" class="btn btn-secondary mt-3">Voltar</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('partials/quill_assets') ?>
<script>
var quill = new Quill('#editor', {
    theme: 'snow',
    modules: { toolbar: window.fullQuillToolbar }
});
function normalizeTemplateEditorColors() {
    quill.root.style.backgroundColor = '#ffffff';
    quill.root.style.color = '#111111';
    quill.root.querySelectorAll('*').forEach(function (node) {
        node.style.color = '#111111';
        if (! node.style.backgroundColor || node.style.backgroundColor === 'transparent') {
            node.style.backgroundColor = '#ffffff';
        }
    });
}
normalizeTemplateEditorColors();
quill.on('text-change', normalizeTemplateEditorColors);
$('form').on('submit', function () {
    normalizeTemplateEditorColors();
    $('#body').val(quill.root.innerHTML);
});
</script>
<?= $this->endSection() ?>
