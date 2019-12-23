<?php
if(!isset($CONFIG))
    require_once('../../config.php');

if(!Permission::can_view(PERMISSION_ROLES)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}

Log::create('permissions');

$all_perms = Permission::get_all();

$perms_patients     = array_filter($all_perms, create_function('$item', 'return $item->rol == 1;'));
$perms_doctors      = array_filter($all_perms, create_function('$item', 'return $item->rol == 2;'));
$perms_auxiliars    = array_filter($all_perms, create_function('$item', 'return $item->rol == 3;'));
$perms_admins       = array_filter($all_perms, create_function('$item', 'return $item->rol == 4;'));
?>

<div class="tabs hv-perms_index">
    <div class="tab" name="Perfiles">
        <div class="col-xs-12 col-sm-6">
            <div class="box">
                <h5 class="header_box">
                    <i class="fa fa-users"></i>Pacientes<?php
                    if(Permission::can_edit(PERMISSION_ROLES)){?>
                        <label data-id="1" class="header_action"><i class="fa fa-plus"></i>Añadir</label><?php
                    }?>
                </h5>
                <ul class="perm_list_rol" name="1"><?php
                    foreach($perms_patients as $perm){
                        if($perm->individual == 0){?>
                        <li name="<?= $perm->id ?>">
                            <i class="fa fa-angle-right"></i>
                            <span><?= $perm->name ?></span>
                            </li><?php
                        }
                    }?>
                </ul>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="box">
                <h5 class="header_box">
                    <i class="fa fa-user-md"></i>Personal Sanitario<?php
                    if(Permission::can_edit(PERMISSION_ROLES)){?>
                        <label data-id="2" class="header_action"><i class="fa fa-plus"></i>Añadir</label><?php
                    }?>
                </h5>
                <ul class="perm_list_rol" name="2"><?php
                    foreach($perms_doctors as $perm){
                        if($perm->individual == 0){?>
                            <li name="<?= $perm->id ?>">
                                <i class="fa fa-angle-right"></i>
                                <span><?= $perm->name ?></span>
                            </li><?php
                        }
                    }?>
                </ul>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="box">
                <h5 class="header_box">
                    <i class="fa fa-hospital-o"></i>Personal Administrativo<?php
                    if(Permission::can_edit(PERMISSION_ROLES)){?>
                        <label data-id="3" class="header_action"><i class="fa fa-plus"></i>Añadir</label><?php
                    }?>
                </h5>
                <ul class="perm_list_rol" name="3"><?php
                    foreach($perms_auxiliars as $perm){
                        if($perm->individual == 0){?>
                            <li name="<?= $perm->id ?>">
                                <i class="fa fa-angle-right"></i>
                                <span><?= $perm->name ?></span>
                            </li><?php
                        }
                    }?>
                </ul>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="box">
                <h5 class="header_box">
                    <i class="fa fa-laptop"></i>Administradores<?php
                    if(Permission::can_edit(PERMISSION_ROLES)){?>
                        <label data-id="4" class="header_action"><i class="fa fa-plus"></i>Añadir</label><?php
                    }?>
                </h5>
                <ul class="perm_list_rol" name="4"><?php
                    foreach($perms_admins as $perm){
                        if($perm->individual == 0){?>
                            <li name="<?= $perm->id ?>">
                                <i class="fa fa-angle-right"></i>
                                <span><?= $perm->name ?></span>
                            </li><?php
                        }
                    }?>
                </ul>
            </div>
        </div>
    </div>

    <div class="tab" name="Usuarios">
        <div class="box filters">
            <h5 class="header_box">
                <i class="fa fa-list-ul"></i>Listado de Usuarios
            </h5>
            <div class="row filters_opt">
                <div class="col-xs-12 col-sm-4 col-md-3">
                    <div class="row">
                        <div class="col-xs-12">
                            <select class="sel-rol">
                                <option value="0">Cualquier Rol</option>
                                <option value="<?= ROL_USER ?>">Pacientes</option>
                                <option value="<?= ROL_DOCTOR ?>">Personal Sanitario</option>
                                <option value="<?= ROL_AUXILIAR ?>">Personal Administrativo</option>
                                <option value="<?= ROL_ADMIN ?>">Administradores del sistema</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-8 col-md-9">
                    <input type="text" class="inp-name" placeholder="Nombre o identificador" >
                </div>
            </div>
            <div class="row user_list_header">
                <div class="col-xs-10 col-sm-8"><h5>Apellidos, Nombre</h5></div>
                <div class="col-xs-2 col-sm-2"><h5>Rol</h5></div>
                <div class="hidden-xs col-sm-2"><h5>Perfil</h5></div>
            </div>
            <ul class="user_rol_list list_limit_height-lg"></ul>
        </div>
    </div>
</div>

<style>
    .hv-perms_index i.fa-angle-right{
        margin-left: 10px;
        margin-right: 5px;
    }
    .hv-perms_index ul.user_rol_list li{
        line-height: 30px;
        cursor: pointer;
    }
    .hv-perms_index .perm_list_rol li{
        cursor: pointer;
    }
</style>