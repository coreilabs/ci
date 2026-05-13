<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php
$statusLabels = [
    'active' => 'Ativo',
    'discharged' => 'Alta',
];
$statusLabel = $statusLabels[$treatment['status']] ?? $treatment['status'];
$stayMonths = (int) ($treatment['stay_months'] ?? 1);
$stayMonthsLabel = $stayMonths === 1 ? 'mês' : 'meses';
?>

<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h3 class="card-title"><?= esc($treatment['patient_name']) ?></h3>
        <div class="action-bar">
            <?php if ($treatment['status'] === 'active'): ?>
                <a class="btn btn-success btn-sm btn-mobile" href="<?= base_url('tratamentos/' . $treatment['id'] . '/prontuario/novo') ?>"><i class="fas fa-notes-medical"></i> Prontuário</a>
                <form method="post" action="<?= base_url('tratamentos/' . $treatment['id'] . '/chamar-coordenacao') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-info btn-sm btn-mobile"><i class="fas fa-bell"></i> Chamar Coordenação</button>
                </form>
                <a class="btn btn-warning btn-sm btn-mobile" href="<?= base_url('tratamentos/' . $treatment['id'] . '/alta') ?>">Alta</a>
            <?php elseif ($canSeeAllRecords): ?>
                <form method="post" action="<?= base_url('tratamentos/' . $treatment['id'] . '/reativar') ?>" class="d-inline" onsubmit="return confirm('Criar nova internação ativa com os dados deste acolhido?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-success btn-sm btn-mobile"><i class="fas fa-redo"></i> Reativar Internação</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><b>Internação:</b> #<?= esc($treatment['id']) ?></div>
            <div class="col-md-3"><b>Responsável:</b> <?= esc($treatment['guardian_name']) ?></div>
            <div class="col-md-3"><b>Status:</b> <?= esc($statusLabel) ?></div>
            <div class="col-md-3"><b>Mensalidade:</b> R$ <?= number_format((float) $treatment['monthly_amount'], 2, ',', '.') ?></div>
        </div>
        <form class="form-inline mt-3 stack-mobile" method="post" action="<?= base_url('tratamentos/' . $treatment['id'] . '/dia-cobranca') ?>">
            <?= csrf_field() ?>
            <label class="mr-2">Dia da Cobrança</label>
            <input type="number" min="1" max="28" name="billing_day" class="form-control form-control-sm mr-2" value="<?= esc($treatment['billing_day'] ?? 10) ?>">
            <button class="btn btn-primary btn-sm">Atualizar Mensalidades Futuras</button>
            <span class="text-muted ml-2">Permanência: <?= esc($stayMonths) ?> <?= esc($stayMonthsLabel) ?></span>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Linha Do Tempo</h3></div>
            <div class="card-body">
                <?php if (! $canSeeAllRecords): ?>
                    <p class="text-muted">Você visualiza apenas as evoluções registradas pelo seu usuário.</p>
                <?php endif; ?>
                <?php foreach ($records as $record): ?>
                    <div class="timeline-item border-bottom pb-2 mb-3">
                        <?php if ($canSeeAllRecords || (int) $record['user_id'] === (int) session('user.id')): ?>
                            <a class="btn btn-outline-info btn-xs float-right" href="<?= base_url('prontuario/' . $record['id'] . '/editar') ?>">Editar</a>
                        <?php endif; ?>
                        <b><?= esc($record['title']) ?></b>
                        <span class="badge badge-secondary"><?= esc($record['type']) ?></span>
                        <?php if ($canSeeAllRecords && ! empty($record['professional_name'])): ?>
                            <span class="badge badge-info"><?= esc($record['professional_name']) ?></span>
                        <?php endif; ?>
                        <div class="text-muted"><?= esc(human_datetime($record['recorded_at'])) ?></div>
                        <p><?= nl2br(esc($record['content'])) ?></p>
                        <?php if ($record['type'] === 'enfermagem' && ($record['sae_collection'] || $record['sae_diagnosis'] || $record['sae_planning'] || $record['sae_execution'] || $record['sae_evaluation'])): ?>
                            <div class="small text-muted">
                                <b>SAE:</b>
                                <?= esc(trim(($record['sae_collection'] ?? '') . ' ' . ($record['sae_diagnosis'] ?? '') . ' ' . ($record['sae_planning'] ?? '') . ' ' . ($record['sae_execution'] ?? '') . ' ' . ($record['sae_evaluation'] ?? ''))) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($records)): ?><p class="text-muted">Nenhum registro clínico.</p><?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Ocorrências E Penalidades</h3></div>
            <div class="card-body">
                <?php foreach ($incidents as $incident): ?>
                    <div class="border-bottom pb-2 mb-2">
                        <b><?= esc($incident['title']) ?></b>
                        <span class="badge badge-warning"><?= esc($incident['status']) ?></span>
                        <div class="text-muted"><?= esc(human_datetime($incident['occurred_at'])) ?> por <?= esc($incident['created_by_name'] ?? '-') ?></div>
                        <p class="mb-0"><?= nl2br(esc($incident['description'])) ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($incidents)): ?><p class="text-muted">Nenhuma ocorrência registrada.</p><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Contratos E Documentos</h3></div>
            <div class="card-body">
                <?php if (! empty($familyAccess)): ?>
                    <div class="alert alert-info">
                        <b>Área da Família:</b> <?= esc($familyAccess['access_url']) ?><br>
                        <b>Senha:</b> <?= esc($familyAccess['initial_password']) ?>
                        <?php if (! empty($familyAccess['last_sent_at'])): ?>
                            <br><span>Último envio: <?= esc(human_datetime($familyAccess['last_sent_at'])) ?> para <?= esc($familyAccess['last_sent_to']) ?></span>
                        <?php endif; ?>
                        <form method="post" action="<?= base_url('portal-familiar/' . $familyAccess['id'] . '/enviar-whatsapp') ?>" class="mt-2">
                            <?= csrf_field() ?>
                            <button class="btn btn-success btn-sm btn-mobile"><i class="fab fa-whatsapp"></i> Enviar acesso por WhatsApp</button>
                        </form>
                        <?php
                        $waPhone = preg_replace('/\D+/', '', (string) ($guardian['phone'] ?? ''));
                        if ($waPhone && strpos($waPhone, '55') !== 0) {
                            $waPhone = '55' . $waPhone;
                        }
                        $waMessage = rawurlencode('Olá, segue o acesso da Área da Família do acolhido ' . $treatment['patient_name'] . ': ' . $familyAccess['access_url'] . ' Senha: ' . $familyAccess['initial_password']);
                        ?>
                        <?php if ($waPhone): ?>
                            <a class="btn btn-success btn-sm btn-mobile mt-2" target="_blank" href="https://wa.me/<?= esc($waPhone) ?>?text=<?= $waMessage ?>">
                                <i class="fab fa-whatsapp"></i> Enviar Via wa.me
                            </a>
                        <?php endif; ?>
                        <?php
                        $selectedFamilyFiles = [];
                        foreach ($familyFiles ?? [] as $file) {
                            $selectedFamilyFiles[] = $file['file_type'] . ':' . $file['file_id'];
                        }
                        ?>
                        <form method="post" action="<?= base_url('portal-familiar/' . $familyAccess['id'] . '/arquivos') ?>" class="mt-3">
                            <?= csrf_field() ?>
                            <b>Arquivos disponíveis:</b>
                            <?php foreach ($contracts as $contract): ?>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="family_contract_<?= $contract['id'] ?>" name="files[]" value="contract:<?= $contract['id'] ?>" <?= empty($selectedFamilyFiles) || in_array('contract:' . $contract['id'], $selectedFamilyFiles, true) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="family_contract_<?= $contract['id'] ?>">Contrato - <?= esc($contract['title']) ?></label>
                                </div>
                            <?php endforeach; ?>
                            <?php foreach ($documents as $document): ?>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="family_document_<?= $document['id'] ?>" name="files[]" value="document:<?= $document['id'] ?>" <?= empty($selectedFamilyFiles) || in_array('document:' . $document['id'], $selectedFamilyFiles, true) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="family_document_<?= $document['id'] ?>"><?= esc($document['name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                            <button class="btn btn-primary btn-sm mt-2">Salvar Arquivos</button>
                        </form>
                    </div>
                <?php endif; ?>
                <?php foreach ($contracts as $contract): ?>
                    <div class="border-bottom pb-2 mb-2">
                        <?= esc($contract['title']) ?>
                        <a class="btn btn-outline-info btn-xs float-right" target="_blank" href="<?= base_url('contratos/' . $contract['id'] . '/pdf') ?>">PDF</a>
                    </div>
                <?php endforeach; ?>
                <?php foreach ($documents as $document): ?>
                    <form class="border-bottom pb-2 mb-2" method="post" enctype="multipart/form-data" action="<?= base_url('documentos/' . $document['id'] . '/assinado') ?>">
                        <?= csrf_field() ?>
                        <?= esc($document['name']) ?> <span class="badge badge-secondary">v<?= esc($document['version']) ?></span>
                        <a class="btn btn-outline-info btn-xs ml-2" target="_blank" href="<?= base_url('documentos/' . $document['id'] . '/pdf') ?>">PDF</a>
                        <input type="file" name="signed_file" class="form-control-file mt-2">
                        <button class="btn btn-outline-success btn-xs mt-1">Upload Assinado</button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Financeiro</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-bordered">
                    <thead><tr><th>Competência</th><th>Descrição</th><th>Valor</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($financial as $entry): ?>
                            <tr>
                                <td><?= esc(human_month($entry['competence'])) ?></td>
                                <td><?= esc($entry['description']) ?></td>
                                <td>R$ <?= number_format((float) $entry['amount'], 2, ',', '.') ?></td>
                                <td><?= $entry['status'] === 'paid' ? 'Pago' : 'Aberto' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
