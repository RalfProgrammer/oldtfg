<?php
if(!isset($CONFIG))
    require_once('../../config.php');

if(!Permission::can_view(PERMISSION_USERS)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}?>

<div class="tabs hv-users">
    <div class="tab" name="Todos los usuarios">
        <div class="box">
            <h5 class="header_box">
                <i class="fa fa-users"></i>Todos los usuarios
                <label class="header_action bt-create"><i class="fa fa-plus"></i>Crear usuario</label>
            </h5>
            <div class="row">
                <div class="col-xs-12 col-sm-3 col-md-2">
                    <select class="u_rol">
                        <option value="<?= ROL_USER?>">Pacientes</option>
                        <option value="<?= ROL_DOCTOR?>">Personal Sanitario</option>
                        <option value="<?= ROL_AUXILIAR?>">Personal Administrativo</option>
                        <option value="<?= ROL_ADMIN?>">Administradores</option>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-5 col-md-8">
                    <input type="text" name="" placeholder="Nombre usuario o historial">
                </div>
                <div class="col-xs-12 col-sm-4 col-md-2">
                    <a class="btn btn-primary bt-search inp_height"><i class="fa fa-search"></i>Buscar</a>
                </div>
            </div>
            <div class="row user_list_header">
                <div class="col-xs-12 col-sm-8 col-md-6 col-lg-3"><h5>Apellidos, Nombre</h5></div>
                <div class="hidden-xs col-sm-4 col-md-2 col-lg-2"><h5>Rol</h5></div>
                <div class="hidden-xs hidden-sm col-md-2 col-lg-2"><h5>Identificador</h5></div>
                <div class="hidden-xs hidden-sm col-md-2 col-lg-2"><h5>DNI</h5></div>
                <div class="hidden-xs hidden-sm hidden-md col-lg-2"><h5>Nacimiento</h5></div>
                <div class="hidden-xs hidden-sm hidden-md col-lg-1"><h5>Sexo</h5></div>
            </div>
            <ul class="user_list list_limit_height-lg">

            </ul>
        </div>
    </div>
</div>

<style>
    .hv-users .user_list_header{
        padding: 5px 0;
        display: block;
    }
    .hv-users .user_list_header h5{
        margin-bottom: 0;
    }
    .hv-users .user_list li{
        line-height: 30px;
    }
</style>