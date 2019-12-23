<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_ROLES)){
    echo 'No tienes permisos';
    die();
}?>

<div class="hv-perms_create">
    <div class="row">
        <div class="col-xs-12 col-sm-8">
            <label>Nombre:</label>
            <input type="text" name="perm_name" value="" placeholder="Permisos nuevos">
        </div>
        <div class="col-xs-12 col-sm-4">
            <label>Rol:</label>
            <select name="perm_rol">
                <option value="1">Pacientes</option>
                <option value="2">Personal Administrativo</option>
                <option value="3">Personal Sanitario</option>
                <option value="4">Administradores</option>
            </select>
        </div>
    </div>

    <div class="perm_wrapper">

    </div>

    <div class="row"><?php
        if(Permission::can_edit(PERMISSION_ROLES)){?>
            <div class="col-xs-6">
                <a class="btn btn-primary bt-save">Guardar</a>
            </div><?php
        }?>
        <div class="col-xs-6">
            <a class="btn btn-default bt-cancel">Cancelar</a>
        </div>
    </div>
</div>
<style>
    .hv-perms_create .perm_wrapper{
        margin: 10px 0;
    }
</style>