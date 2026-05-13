<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<div class="row">
    <div class="col-md-3">
        <a class="info-box bg-info" href="<?= base_url('usuarios') ?>">
            <span class="info-box-icon"><i class="fas fa-users"></i></span>
            <span class="info-box-content"><span class="info-box-text">Usuários</span><span class="info-box-number">Acessos e perfis</span></span>
        </a>
    </div>
    <div class="col-md-3">
        <a class="info-box bg-success" href="<?= base_url('administrativo-clinico') ?>">
            <span class="info-box-icon"><i class="fas fa-file-signature"></i></span>
            <span class="info-box-content"><span class="info-box-text">Modelos</span><span class="info-box-number">Contratos e termos</span></span>
        </a>
    </div>
    <div class="col-md-3">
        <a class="info-box bg-primary" href="<?= base_url('cronograma') ?>">
            <span class="info-box-icon"><i class="fas fa-calendar-week"></i></span>
            <span class="info-box-content"><span class="info-box-text">Cronograma</span><span class="info-box-number">Rotina familiar</span></span>
        </a>
    </div>
    <div class="col-md-3">
        <a class="info-box bg-warning" href="#configuracoes">
            <span class="info-box-icon"><i class="fas fa-sliders-h"></i></span>
            <span class="info-box-content"><span class="info-box-text">Configurações</span><span class="info-box-number">Sistema e família</span></span>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card card-outline card-primary" id="configuracoes">
            <div class="card-header"><h3 class="card-title">Configurações Editáveis</h3></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('painel-administrativo/configuracoes') ?>">
                    <?= csrf_field() ?>
                    <?php foreach ($settings as $setting): ?>
                        <div class="form-group">
                            <label><?= esc($setting['label']) ?></label>
                            <?php if ($setting['type'] === 'textarea'): ?>
                                <textarea name="<?= esc($setting['key']) ?>" class="form-control" rows="3"><?= esc($setting['value']) ?></textarea>
                            <?php elseif ($setting['type'] === 'boolean'): ?>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="<?= esc($setting['key']) ?>" name="<?= esc($setting['key']) ?>" value="1" <?= $setting['value'] ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="<?= esc($setting['key']) ?>">Ativo</label>
                                </div>
                            <?php else: ?>
                                <input type="<?= $setting['type'] === 'number' ? 'number' : 'text' ?>" name="<?= esc($setting['key']) ?>" class="form-control" value="<?= esc($setting['value']) ?>">
                            <?php endif; ?>
                            <small class="text-muted"><?= esc($setting['group']) ?></small>
                        </div>
                    <?php endforeach; ?>
                    <button class="btn btn-primary btn-mobile">Salvar Configurações</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-outline card-success">
            <div class="card-header"><h3 class="card-title">Permissões Por Cargo</h3></div>
            <div class="card-body">
                <?php
                $byRole = [];
                foreach ($rolePermissions as $item) {
                    $byRole[$item['role_id']][] = (int) $item['permission_id'];
                }
                ?>
                <?php foreach ($roles as $role): ?>
                    <form method="post" action="<?= base_url('painel-administrativo/permissoes') ?>" class="border-bottom pb-3 mb-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="role_id" value="<?= $role['id'] ?>">
                        <h5><?= esc($role['name']) ?></h5>
                        <div class="row">
                            <?php foreach ($permissions as $permission): ?>
                                <div class="col-md-6">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox"
                                               id="perm_<?= $role['id'] ?>_<?= $permission['id'] ?>"
                                               name="permissions[]"
                                               value="<?= $permission['id'] ?>"
                                               <?= in_array((int) $permission['id'], $byRole[$role['id']] ?? [], true) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="perm_<?= $role['id'] ?>_<?= $permission['id'] ?>"><?= esc($permission['name']) ?></label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="btn btn-success btn-sm mt-2">Atualizar Cargo</button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card card-outline card-warning">
            <div class="card-header"><h3 class="card-title">Tipos De Ocorrência</h3></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('painel-administrativo/tipos-ocorrencia') ?>">
                    <?= csrf_field() ?>
                    <input name="name" class="form-control mb-2" placeholder="Nome" required>
                    <select name="severity" class="form-control mb-2">
                        <option value="baixa">Baixa</option>
                        <option value="media" selected>Média</option>
                        <option value="alta">Alta</option>
                    </select>
                    <textarea name="description" class="form-control mb-2" rows="2" placeholder="Descrição"></textarea>
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="incident_active" name="active" value="1" checked>
                        <label for="incident_active" class="custom-control-label">Ativo</label>
                    </div>
                    <button class="btn btn-warning btn-sm">Criar tipo</button>
                </form>
                <hr>
                <?php foreach ($incidentTypes as $type): ?>
                    <form method="post" action="<?= base_url('painel-administrativo/tipos-ocorrencia/' . $type['id']) ?>" class="border rounded p-2 mb-2">
                        <?= csrf_field() ?>
                        <input name="name" class="form-control form-control-sm mb-1" value="<?= esc($type['name']) ?>" required>
                        <select name="severity" class="form-control form-control-sm mb-1">
                            <option value="baixa" <?= $type['severity'] === 'baixa' ? 'selected' : '' ?>>Baixa</option>
                            <option value="media" <?= $type['severity'] === 'media' ? 'selected' : '' ?>>Média</option>
                            <option value="alta" <?= $type['severity'] === 'alta' ? 'selected' : '' ?>>Alta</option>
                        </select>
                        <textarea name="description" class="form-control form-control-sm mb-1" rows="2"><?= esc($type['description'] ?? '') ?></textarea>
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="incident_active_<?= $type['id'] ?>" name="active" value="1" <?= $type['active'] ? 'checked' : '' ?>>
                            <label for="incident_active_<?= $type['id'] ?>" class="custom-control-label">Ativo</label>
                        </div>
                        <button class="btn btn-success btn-sm">Salvar</button>
                        <button type="submit"
                                form="delete_incident_type_<?= $type['id'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Excluir este tipo de ocorrência?')">Excluir</button>
                    </form>
                    <form id="delete_incident_type_<?= $type['id'] ?>" method="post" action="<?= base_url('painel-administrativo/tipos-ocorrencia/' . $type['id'] . '/excluir') ?>">
                        <?= csrf_field() ?>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
