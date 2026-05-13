<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; line-height: 1.45; }
        header { border-bottom: 1px solid #999; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #999; padding-top: 8px; font-size: 10px; text-align: center; }
        h1 { font-size: 20px; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #aaa; padding: 5px; }
        .pdf-brand-image { max-width: 100%; max-height: 90px; }
        .ql-align-center { text-align: center; }
        .ql-align-right { text-align: right; }
        .ql-align-justify { text-align: justify; }
        .ql-size-small { font-size: .75em; }
        .ql-size-large { font-size: 1.5em; }
        .ql-size-huge { font-size: 2.5em; }
        .ql-indent-1 { padding-left: 3em; }
        .ql-indent-2 { padding-left: 6em; }
        .ql-indent-3 { padding-left: 9em; }
        .ql-indent-4 { padding-left: 12em; }
        blockquote { border-left: 4px solid #ccc; margin: 0 0 10px 0; padding-left: 12px; color: #333; }
        pre, code { background: #f4f4f4; padding: 4px; font-family: DejaVu Sans Mono, monospace; }
        img { max-width: 100%; height: auto; }
    </style>
</head>
<body>
    <?php
    $settings = class_exists(\App\Models\AppSettingModel::class) ? new \App\Models\AppSettingModel() : null;
    $headerImage = $settings ? $settings->value('pdf_header_image', '') : '';
    $footerImage = $settings ? $settings->value('pdf_footer_image', '') : '';
    ?>
    <header>
        <?php if ($headerImage): ?>
            <img class="pdf-brand-image" src="<?= esc($headerImage) ?>">
        <?php else: ?>
            <strong>Comunidade Terapeutica Amor Fraterno</strong><br>
            Documento gerado pelo sistema
        <?php endif; ?>
    </header>
    <footer>
        <?php if ($footerImage): ?>
            <img class="pdf-brand-image" src="<?= esc($footerImage) ?>">
        <?php else: ?>
            Assinatura do responsavel: __________________________________________
        <?php endif; ?>
    </footer>
    <main>
        <?= $body ?>
    </main>
</body>
</html>
