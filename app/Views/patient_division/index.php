<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Divisão De Acolhidos - Psicologia</h3>
    </div>
    <div class="card-body table-responsive">
        <?php if ($isAdmin && empty($psychologists)): ?>
            <div class="alert alert-warning">
                Cadastre ou ajuste o cargo de Psicologia para conter "psic" no nome ou identificador do cargo.
            </div>
        <?php endif; ?>

        <div class="alert alert-info">
            A lista prioriza quem está há mais tempo sem atendimento psicológico. Prazo máximo configurado: <?= esc($slaDays) ?> dias.
        </div>

        <table id="divisionTable" class="table table-bordered table-hover table-clickable">
            <thead>
                <tr>
                    <th>Acolhido</th>
                    <th>Responsável</th>
                    <th>Admissão</th>
                    <th>Psicólogo</th>
                    <th>Ultimo atendimento</th>
                    <th>Proximo atendimento</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <?php
                    $assignedToMe = (int) ($patient['psychologist_id'] ?? 0) === $currentUserId;
                    $hasPsychologist = ! empty($patient['psychologist_id']);
                    $lastBase = $patient['last_psychology_at'] ?: $patient['admission_date'];
                    $daysWithoutCare = (int) floor((time() - strtotime($lastBase)) / 86400);
                    $overdue = $hasPsychologist && $daysWithoutCare >= $slaDays;
                    ?>
                    <tr class="<?= $overdue ? 'table-danger' : '' ?>">
                        <td><?= esc($patient['patient_name']) ?></td>
                        <td><?= esc($patient['guardian_name']) ?></td>
                        <td><?= esc(human_date($patient['admission_date'])) ?></td>
                        <td>
                            <?php if ($hasPsychologist): ?>
                                <span class="badge badge-info"><?= esc($patient['psychologist_name']) ?></span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Sem Psicólogo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $patient['last_psychology_at'] ? esc(human_datetime($patient['last_psychology_at'])) : '<span class="badge badge-warning">Nunca atendido</span>' ?>
                            <?php if ($overdue): ?><span class="badge badge-danger">+<?= esc($daysWithoutCare) ?> dias</span><?php endif; ?>
                        </td>
                        <td>
                            <?= $patient['next_attendance_at'] ? esc(human_datetime($patient['next_attendance_at'])) : '<span class="text-muted">Não agendado</span>' ?>
                        </td>
                        <td>
                            <?php if ($canManageDivision && (! $hasPsychologist || $assignedToMe || $isAdmin)): ?>
                                <form method="post" action="<?= base_url('divisao-pacientes/' . $patient['id'] . '/atribuir') ?>" class="form-inline stack-mobile">
                                    <?= csrf_field() ?>
                                    <?php if ($isAdmin): ?>
                                        <select name="professional_user_id" class="form-control form-control-sm mr-2" required>
                                            <option value="">Psicólogo</option>
                                            <?php foreach ($psychologists as $psychologist): ?>
                                                <option value="<?= $psychologist['id'] ?>" <?= (int) $psychologist['id'] === (int) ($patient['psychologist_id'] ?? 0) ? 'selected' : '' ?>>
                                                    <?= esc($psychologist['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>
                                    <input type="datetime-local" name="next_attendance_at" class="form-control form-control-sm mr-2"
                                           value="<?= $patient['next_attendance_at'] ? esc(date('Y-m-d\TH:i', strtotime($patient['next_attendance_at']))) : '' ?>">
                                    <button class="btn btn-primary btn-sm"><?= $hasPsychologist ? 'Atualizar' : 'Atribuir' ?></button>
                                </form>

                                <?php if ($hasPsychologist): ?>
                                    <form method="post" action="<?= base_url('divisao-pacientes/' . $patient['id'] . '/liberar') ?>" class="d-inline-block mt-1">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-outline-danger btn-sm">Liberar</button>
                                    </form>
                                <?php endif; ?>
                            <?php elseif ($hasPsychologist): ?>
                                <span class="text-muted">Acolhido já está na lista de outro psicólogo.</span>
                            <?php else: ?>
                                <span class="text-muted">Apenas psicólogos e administradores podem dividir acolhidos.</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($patients)): ?>
                    <tr><td colspan="7" class="text-muted text-center">Nenhum acolhido ativo.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
    $('#divisionTable').DataTable({
        responsive: true,
        autoWidth: false,
        order: [],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json' }
    });
});
</script>
<?= $this->endSection() ?>
