<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="card card-outline card-primary">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h3 class="card-title mb-0">Usuários</h3>

            <?php if (hasPermission('users.manage')): ?>
                <a href="<?= base_url('usuarios/create') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Novo
                </a>
            <?php endif; ?>

        </div>

        <div class="card-body p-0">

            <table class="table table-hover table-striped mb-0">

                <thead class="thead-dark">
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Cargo</th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= esc($u['name']) ?></td>
                            <td><?= esc($u['email']) ?></td>
                            <td><?= esc($u['role_name']) ?></td>

                            <td class="text-center">

                                <?php if (hasPermission('users.manage')): ?>

                                    <a href="<?= base_url('usuarios/edit/' . $u['id']) ?>"
                                       class="btn btn-sm btn-warning"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="<?= base_url('usuarios/delete/' . $u['id']) ?>"
                                       class="btn btn-sm btn-danger"
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                        <i class="fas fa-trash"></i>
                                    </a>

                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Nenhum usuário encontrado
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?= $this->endSection() ?>