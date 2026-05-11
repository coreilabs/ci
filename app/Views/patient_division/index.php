<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Divisão de pacientes - Psicologia</h3>
    </div>
    <div class="card-body table-responsive">
        <?php if ($isAdmin && empty($psychologists)): ?>
            <div class="alert alert-warning">
                Cadastre ou ajuste o cargo dos psicólogos para conter "psic" no nome ou identificador do cargo.
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Responsável</th>
                    <th>Admissão</th>
                    <th>Psicólogo</th>
                    <th>Próximo atendimento</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <?php
                    $assignedToMe = (int) ($patient['psychologist_id'] ?? 0) === $currentUserId;
                    $hasPsychologist = ! empty($patient['psychologist_id']);
                    ?>
                    <tr>
                        <td><?= esc($patient['patient_name']) ?></td>
                        <td><?= esc($patient['guardian_name']) ?></td>
                        <td><?= esc(human_date($patient['admission_date'])) ?></td>
                        <td>
                            <?php if ($hasPsychologist): ?>
                                <span class="badge badge-info"><?= esc($patient['psychologist_name']) ?></span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Sem psicólogo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $patient['next_attendance_at'] ? esc(human_datetime($patient['next_attendance_at'])) : '<span class="text-muted">Não agendado</span>' ?>
                        </td>
                        <td>
                            <?php if ($canManageDivision && (! $hasPsychologist || $assignedToMe || $isAdmin)): ?>
                                <form method="post" action="<?= base_url('divisao-pacientes/' . $patient['id'] . '/atribuir') ?>" class="form-inline">
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
                                    <button class="btn btn-primary btn-sm"><?= $hasPsychologist ? 'Atualizar' : 'Adicionar à lista' ?></button>
                                </form>

                                <?php if ($hasPsychologist): ?>
                                    <form method="post" action="<?= base_url('divisao-pacientes/' . $patient['id'] . '/liberar') ?>" class="d-inline-block mt-1">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-outline-danger btn-sm">Liberar</button>
                                    </form>
                                <?php endif; ?>
                            <?php elseif ($hasPsychologist): ?>
                                <span class="text-muted">Paciente já está na lista de outro psicólogo.</span>
                            <?php else: ?>
                                <span class="text-muted">Apenas psicólogos e administradores podem dividir pacientes.</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($patients)): ?>
                    <tr><td colspan="6" class="text-muted text-center">Nenhum paciente ativo.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
