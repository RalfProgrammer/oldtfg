<?php

if(!isset($CONFIG))
    require_once('../../../config.php');


if(!Permission::can_view(PERMISSION_STAFF)){
    echo json_encode(array(
        "success" => false,
        "info"    => 'No tienes permisos'
    ));
    die();
}
require_once($CONFIG->dir . 'services/users/Relation.php');

$readable = '';
if(!($can_edit = Permission::can_edit(PERMISSION_STAFF)))
    $readable = 'class="readable" readonly ';

$id = get_param('id', false);

$Staff = new Staff($id);
?>

<div class="hv-staff_details">
    <span class="section-header"><i class="fa fa-hospital-o"></i>Datos Clínicos: </span>
    <div class="basic_info row">
        <div class="col-xs-12 col-sm-6">
            Nombre:
            <input type="text" value="<?= $Staff->getFullName()?>"  disabled class="readable">
        </div>
        <div class="col-xs-12 col-sm-6">
            Identificador:
            <input type="text" name="w_identifier" value="<?= $Staff->getOther()->identifier ?>" disabled class="readable">
        </div>
        <div class="col-xs-12 col-sm-6">
            Rama:
            <select name="s_branch" <?= $readable?>>
                <option value="">- Selecciona la especialidad -</option><?php
                $branchs = Staff::getBranchNames();
                foreach($branchs as $key => $branch){?>
                    <option value="<?= $key?>" <?= $Staff->getBranch() == $key ? 'selected' : ''?>><?= $branch?></option><?php
                }?>
            </select>
        </div>
        <div class="col-xs-12 col-sm-6">
            Turno:
            <select name="s_horary" <?= $readable?>>
                <option value="">Horario</option>
                <option value="M"  <?= $Staff->getHorary() == "M" ? 'selected' : ''?>>Mañana</option>
                <option value="ME" <?= $Staff->getHorary() == "ME" ? 'selected' : ''?>>Mañana y Tarde</option>
                <option value="MN" <?= $Staff->getHorary() == "MN" ? 'selected' : ''?>>Mañana y Noche</option>
                <option value="MEN" <?= $Staff->getHorary() == "MEN" ? 'selected' : ''?>>Mañana , Tarde y Noche</option>
                <option value="E"   <?= $Staff->getHorary() == "E" ? 'selected' : ''?>>Tarde</option>
                <option value="EN" <?= $Staff->getHorary() == "EN" ? 'selected' : ''?>>Tarde y Noche</option>
                <option value="N" <?= $Staff->getHorary() == "N" ? 'selected' : ''?>>Noche</option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-4">
            Consulta:
            <input type="text" name="s_room" placeholder="Consulta" value="<?= $Staff->getRoom()?>" <?= $readable?>>
        </div>
        <div class="col-xs-12 col-sm-4">
            Oficina:
            <input type="text" name="s_office" placeholder="Oficina" value="<?= $Staff->getOffice()?>" <?= $readable?>>
        </div>
        <div class="col-xs-12 col-sm-4">
            Telefono Hospital:
            <input type="text" name="s_hphone" placeholder="Telefono Hospital" value="<?= $Staff->getH_phone()?>" <?= $readable?>>
        </div>
        <?php
        if($can_edit){?>
            <div class="col-xs-12">
                <div class="col-xs-12 col-sm-6 col-sm-offset-6 col-md-4 col-md-offset-8 col-lg-3 col-lg-offset-9">
                    <span class="btn btn-primary bt-save">Guardar</span>
                </div>
            </div><?php
        }?>
    </div><?php
    if($Staff->getRol() == ROL_DOCTOR){?>
        <span class="section-header"><i class="fa fa-group"></i>Sus pacientes</span>
        <div class="row">
            <div class="col-xs-12">
                <input class="inp-search" type="text" placeholder="Añade pacientes (nombre o historial)">
            </div>
        </div>
        <div class="patients_list">
            <div class="row">
                <div class="col-xs-12 col-sm-8"><h5>Nombre</h5></div>
                <div class="hidden-xs col-sm-4"><h5>NºHistorial</h5></div>
            </div>
            <ul>
                <?php
                if($patients = Relation::getPatients($Staff->getId())){
                    foreach($patients as $patient){ ?>
                        <li class="patient_item" name="<?= $patient->getId()?>">
                            <div class="row">
                                <div class="col-xs-12 col-sm-8">
                                    <img class="avatar" src="<?= $patient->getSrcAvatar()?>">
                                    <?= $patient->getFullname()?>
                                </div>
                                <div class="hidden-xs col-sm-4">
                                    <?= $patient->getHistoric()?>
                                </div>
                            </div>
                            <i class="fa fa-trash-o bt-delete-patient"></i>
                        </li><?php
                    }
                }else{?>
                    <li class="empty_list" style="text-align: center">- No tiene asignado ningun paciente -</li><?php
                }?>
            </ul>
        </div><?php
    }?>
</div>

<style>
    .hv-staff_details .basic_info {
        display: table;
    }
    .hv-staff_details .basic_info > div{
        display: table-row;
    }
    .hv-staff_details .basic_info > div > div{
        display: table-cell;
    }
    .hv-staff_details .basic_info > div > div:first-child{
        padding-right: 5px;
    }
    .hv-staff_details .basic_info > div > div:last-child{
        font-size: 14px;
    }
    .hv-staff_details .basic_info > div > div:last-child{

    }
    .hv-staff_details .patients_list{
        padding: 5px 10px;
        max-height: 250px;
        overflow-y: auto;
    }
    .hv-staff_details .patients_list li {
        display: block;
        position: relative;
        font-size: 14px;
    }
    .hv-staff_details .patients_list li:hover{
        background-color: #f7f7f7;
    }
    .hv-staff_details .patients_list li i.bt-delete-patient{
        position: absolute;
        right: 5px;
        top: 10px;
    }
    .hv-staff_details .patient_item{
        line-height: 30px;
    }
</style>

