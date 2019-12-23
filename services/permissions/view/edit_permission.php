<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_ROLES)){
    echo 'No tienes permisos';
    die();
}?>

<div class="hv-perms_create popup_perms_wrapper" name="${data.id}">
    <div class="row">
        <div class="col-xs-12 col-xs-8">
            <label>Nombre:</label>
            <input type="text" name="perm_name" value="{{if data.id > 0}}${data.name}{{/if}}" placeholder="Nombre" {{if data.id > 0 && data.id < 5}}readonly{{/if}}>
        </div>
        <div class="col-xs-12 col-xs-4">
            <label>Rol:</label>
            <select name="perm_rol" {{if data.id > 0}}disabled="disabled"{{/if}}>
                <option value="<?= ROL_USER ?>" {{if data.rol == <?= ROL_USER?>}}selected{{/if}}>Pacientes</option>
                <option value="<?= ROL_DOCTOR ?>" {{if data.rol == <?= ROL_DOCTOR?>}}selected{{/if}}>Personal Sanitario</option>
                <option value="<?= ROL_AUXILIAR ?>" {{if data.rol == <?= ROL_AUXILIAR?>}}selected{{/if}}>Personal Administrativo</option>
                <option value="<?= ROL_ADMIN ?>" {{if data.rol == <?= ROL_ADMIN?>}}selected{{/if}}>Administrador</option>
            </select>
        </div>

    </div>
    <div class="perm_wrapper">

    </div>
    <div class="row"><?php
        if(Permission::can_edit(PERMISSION_ROLES)){?>
            {{if data.id <= 4}}
            <div class="col-xs-6 col-md-4 col-lg-2"></div>
            {{/if}}
            <div class="col-xs-6 col-md-4 col-lg-2 col-md-offset-4 col-lg-offset-8">
                <a class="btn btn-primary bt-save">Guardar</a>
            </div>
            {{if data.id > 4}}
            <div class="col-xs-6 col-md-4 col-lg-2">
                <a class="btn btn-danger bt-delete">Borrar</a>
            </div>
            {{/if}}<?php
        }?>
    </div>
</div>

<style>
    .hv-perms_create .perm_wrapper{
        margin: 10px 0;
    }
</style>