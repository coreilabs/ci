<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<div class="card card-outline card-primary">
    <div class="card-header"><h3 class="card-title">Modelo PDF</h3></div>
    <div class="card-body">
        <form method="post" action="<?= base_url('administrativo-clinico/modelos/' . $template['id']) ?>">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group col-md-5"><label>Nome</label><input name="name" class="form-control" value="<?= esc($template['name']) ?>"></div>
                <div class="form-group col-md-4"><label>Categoria</label><input name="category" class="form-control" value="<?= esc($template['category']) ?>"></div>
                <div class="form-group col-md-3"><label>Versao</label><input type="number" name="version" class="form-control" value="<?= esc($template['version']) ?>"></div>
            </div>
            <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" name="is_required_admission" value="1" class="custom-control-input" id="required" <?= $template['is_required_admission'] ? 'checked' : '' ?>>
                <label class="custom-control-label" for="required">Gerar na admissao</label>
            </div>
            <div id="editor" style="height: 420px; background: #fff; color: #111;"><?= $template['body'] ?></div>
            <input type="hidden" name="body" id="body">
            <button class="btn btn-success mt-3">Salvar</button>
            <a href="<?= base_url('administrativo-clinico') ?>" class="btn btn-secondary mt-3">Voltar</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
var quill = new Quill('#editor', { theme: 'snow' });
$('form').on('submit', function () { $('#body').val(quill.root.innerHTML); });
</script>
<?= $this->endSection() ?>
