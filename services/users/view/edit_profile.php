<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_PROFILE)){
    echo 'No tienes permisos para verlo';
    die();
}

$can_edit = Permission::can_edit(PERMISSION_PROFILE);
?>

<div class="hv-profile row">
    <div class="col-xs-12">
        <div class="box">
            <h5 class="header_box"><i class="fa fa-user"></i>Datos Usuario</h5>
            <table>
                <tr>
                    <td>Nombre:</td>
                    <td><?= $USER->name?></td>
                </tr>
                <tr>
                    <td>Apellidos:</td>
                    <td><?= $USER->lastname?></td>
                </tr>
                <tr>
                    <td>Dni:</td>
                    <td><?= $USER->dni?></td>
                </tr>
                <tr>
                    <td>Dirección:</td>
                    <td> <?= $USER->contact->address->dir . ' ('. $USER->contact->address->ciu . ', ' . $USER->contact->address->cp . ') '?></td>
                </tr>
                <tr>
                    <td>Teléfono:</td>
                    <td><?= array_shift($USER->contact->phone) ?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-xs-12 col-md-6">
        <div class="box">
            <h5 class="header_box"><i class="fa fa-envelope-o"></i>Tu email</h5>
            <div class="email_wrapper row">
                <div class="col-xs-12">
                    Tu Email:
                    <input type="text" name="email_act" value="<?= array_shift($USER->contact->email) ?>" readonly="true">
                </div><?php
                if($can_edit){?>
                    <div class="col-xs-12 col-sm-6">
                        Nuevo email:
                        <input type="text" name="email_1" value="" placeholder="ejemplo@dominio.es">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        Repite nuevo Email:
                        <input type="text" name="email_2" value="" placeholder="ejemplo@dominio.es">
                    </div>
                    <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                        <a class="btn btn-primary bt-save_email">Guardar</a>
                    </div><?php
                }?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-6">
        <div class="box">
            <h5 class="header_box"><i class="fa fa-key"></i>Tu contraseña</h5>
            <div class="password_wrapper row"><?php
                if($can_edit){?>
                    <div class="col-xs-12 col-sm-6">
                        Nueva contraseña:
                        <input type="password" name="password_1" value="" placeholder="minimo 6 caracteres">
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        Repite nueva contraseña:
                        <input type="password" name="password_2" value="" placeholder="repite contraseña">
                    </div>
                    <div class="col-xs-12">
                        Confirma tu contraseña actual:
                        <input type="password" name="password_old" value="" placeholder="********">
                    </div>
                    <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                        <a class="btn btn-primary bt-save_password">Guardar</a>
                    </div><?php
                }else{?>
                    <div class="col-xs-12" style="text-align: center">No tienes permiso para cambiar la contraseña</div><?php
                }?>
            </div>
        </div>
    </div>
</div>

<style>

    .hv-profile table{
        margin-bottom: 0;
    }
    .hv-profile table,
    .hv-profile table td{
        border: none;
        background-color: #fff;
        text-align: left;
        padding: 5px 0;
    }
    .hv-profile table td:first-child{
        width: 75px;
        font-style: italic;
    }
    .hv-profile input[name=email_act]{
        border: none;
        cursor: default;
    }
</style>