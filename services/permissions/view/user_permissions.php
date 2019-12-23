<?php
if(!isset($CONFIG))
    require_once('../../../config.php');
?>
<div class="hv-edit_up popup_perms_wrapper">
    <h4>${user.fullname}</h4>
    <div class="row">
        <div class="col-xs-6">
            Rol
        </div>
        <div class="col-xs-6">
            Permisos
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <select name="perm_user_rol">
                <option value="<?= ROL_USER ?>" {{if perms.user.rol == <?= ROL_USER ?>}} selected {{/if}}>Pacientes</option>
                <option value="<?= ROL_DOCTOR ?>" {{if perms.user.rol == <?= ROL_DOCTOR ?>}} selected {{/if}}>Personal Sanitario</option>
                <option value="<?= ROL_AUXILIAR ?>" {{if perms.user.rol == <?= ROL_AUXILIAR ?>}} selected {{/if}}>Personal Administrativo</option>
                <option value="<?= ROL_ADMIN ?>" {{if perms.user.rol == <?= ROL_ADMIN ?>}} selected {{/if}}>Administradores</option>
            </select>
        </div>
        <div class="col-xs-6">
            <select name="perm_user_perm">
                {{each(i, perm) perms.list}}
                    {{if perm.rol == perms.user.rol && perm.id >0}}
                        {{if perm.individual == 0}}
                            <option value="${perm.id}" {{if perm.id == perms.user.rol_perms }} selected {{/if}}>${perm.name}</option>
                        {{else}}
                            {{if perm.individual == user.id}}
                                <option value="user" {{if perm.id == perms.user.rol_perms}}selected{{/if}}>*Editados para el usuario</option>
                            {{/if}}
                        {{/if}}
                    {{/if}}
                {{/each}}
            </select>
        </div>
    </div>
    <div class="perm_wrapper">

    </div>

    <?php
    if(Permission::can_edit(PERMISSION_ROLES)){?>
        <div class="row">
            <div class="col-xs-12 col-sm-3 col-sm-offset-9 col-md-2 col-md-offset-10">
                <a class="btn btn-primary bt-save">
                    Guardar
                </a>
            </div>
        </div><?php
    }?>
</div>

<style>
    .hv-edit_up .perm_wrapper{
        margin: 10px 0;
    }
</style>