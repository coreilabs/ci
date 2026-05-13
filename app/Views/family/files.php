<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <title>Área da Família</title>
</head>
<body class="bg-light p-3">
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Área da Família</h3>
            <small class="text-muted">Documentos, cronograma e informações administrativas.</small>
        </div>
        <?php if (! empty($whatsappGroupLink)): ?>
            <a class="btn btn-success mt-2" target="_blank" href="<?= esc($whatsappGroupLink) ?>">Grupo no WhatsApp</a>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Arquivos para download</h3></div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach ($contracts as $contract): ?>
                            <a class="list-group-item list-group-item-action" target="_blank" href="<?= base_url('familia/' . $token . '/contratos/' . $contract['id'] . '/pdf') ?>">Contrato de admissão - <?= esc($contract['title']) ?></a>
                        <?php endforeach; ?>
                        <?php foreach ($documents as $document): ?>
                            <a class="list-group-item list-group-item-action" target="_blank" href="<?= base_url('familia/' . $token . '/documentos/' . $document['id'] . '/pdf') ?>"><?= esc($document['name']) ?></a>
                        <?php endforeach; ?>
                    </div>
                    <?php if (empty($contracts) && empty($documents)): ?>
                        <p class="text-muted mb-0">Nenhum arquivo disponível no momento.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <?php if (! empty($scheduleItems)): ?>
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Cronograma</h3></div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Dia</th><th>Horário</th><th>Atividade</th></tr></thead>
                            <tbody>
                                <?php foreach ($scheduleItems as $item): ?>
                                    <tr>
                                        <td><?= esc($item['day_label']) ?></td>
                                        <td><?= esc(($item['starts_at'] ? substr($item['starts_at'], 0, 5) : '') . ($item['ends_at'] ? ' - ' . substr($item['ends_at'], 0, 5) : '')) ?></td>
                                        <td>
                                            <?= esc($item['activity']) ?>
                                            <?php if (! empty($item['notes'])): ?>
                                                <br><small class="text-muted"><?= esc($item['notes']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (! empty($showQuestions)): ?>
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Perguntas para Administração</h3></div>
                    <div class="card-body">
                        <p><?= nl2br(esc($questionsText ?: 'Envie suas perguntas para a administração pelo contato oficial da comunidade.')) ?></p>
                        <?php if (! empty($whatsappGroupLink)): ?>
                            <a class="btn btn-success" target="_blank" href="<?= esc($whatsappGroupLink) ?>">Abrir WhatsApp</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
