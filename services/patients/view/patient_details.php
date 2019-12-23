<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_PATIENT)){
    echo_json(false, 'No tienes permiso');
}

$readable = '';
if(!($can_edit = Permission::can_edit(PERMISSION_PATIENT)))
    $readable = 'class="readable" disabled ';

require_once($CONFIG->dir . 'services/users/Relation.php');

$id = get_param('id', false);

$Patient = new Patient($id);
$branchs = Staff::getBranchNames();
?>

<div class="hv-patient_details">
    <span class="section-header"><i class="fa fa-hospital-o"></i>Datos Clínicos: </span>
    <div class="basic_info row">
        <div class="col-xs-12 col-sm-6">
            Nombre y Apellidos:
            <input type="text" value="<?= $Patient->getFullName()?>" disabled class="readable">
        </div>
        <div class="col-xs-12 col-sm-6">
            Número de historial:
            <input type="text" name="p_historic" value="<?= $Patient->getHistoric() ?>" disabled class="readable">
        </div>
        <div class="col-xs-12 col-sm-6">
            Altura:
            <input type="text" name="p_height" value="<?= $Patient->getHeight()?>" <?= $readable ?>>
        </div>
        <div class="col-xs-12 col-sm-6">
            Peso:
            <input type="text" name="p_weight" value="<?= $Patient->getWeight()?>" <?= $readable ?>>
        </div>
        <?php
        if($can_edit){?>
            <div class="col-xs-12">
                <div class="col-xs-12 col-sm-6 col-sm-offset-6 col-md-4 col-md-offset-8 col-lg-3 col-lg-offset-9">
                    <span class="btn btn-primary bt-save">Guardar</span>
                </div>
            </div><?php
        }?>
    </div>
    <span class="section-header"><i class="fa fa-user-md"></i>Personal sanitario asignado</span>
    <?php
    if($can_edit){?>
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-3">
                <select class="filter_branch">
                    <option value="0">Cualquier Rama</option>
                    <?php
                    foreach($branchs as $i => $branch){?>
                        <option value="<?= $i ?>"><?= $branch ?></option><?php
                    }?>
                </select>
            </div>
            <div class="col-xs-12 col-sm-8 col-md-9">
                <input class="inp-search" type="text" placeholder="Añade personal (nombre o identificador)">
            </div>
        </div><?php
    }?>
    <div class="doctors_list">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-4"><h5>Apellidos, Nombre</h5></div>
            <div class="hidden-xs col-sm-3 col-md-3"><h5>Identificador</h5></div>
            <div class="hidden-xs col-sm-3 col-md-3"><h5>Horario</h5></div>
            <div class="hidden-xs hidden-sm col-md-2"><h5>Rama</h5></div>
        </div>
        <ul>
            <?php
            if($doctors = Relation::getDoctors($Patient->getId())){
                foreach($doctors as $doctor){ ?>
                    <li class="doctor_item" name="<?= $doctor->getId()?>">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <img class="avatar" src="<?= $doctor->getSrcAvatar()?>">
                                <?= $doctor->getFullname()?>
                            </div>
                            <div class="hidden-xs col-sm-3 col-md-3">
                                <?= $doctor->getStaff_id()?>
                            </div>
                            <div class="hidden-xs col-sm-3 col-md-3">
                                <?= $doctor->other->horary_text?>
                            </div>
                            <div class="hidden-xs hidden-sm col-md-2">
                                <?= $doctor->other->branch_name?>
                            </div>
                        </div>
                        <?php
                        if($can_edit){?>
                            <i class="fa fa-trash-o bt-delete-doctor"></i><?php
                        }?>
                    </li><?php
                }
            }else{?>
                <li class="empty_list" style="text-align: center">- No tiene asignado nadie -</li><?php
            }?>
        </ul>
    </div>
</div>

<style>
    .hv-patient_details .doctors_list{
        padding: 5px 10px;
        max-height: 250px;
        overflow-y: auto;
    }
    .hv-patient_details .doctors_list li {
        display: block;
        position: relative;
        font-size: 14px;
    }
    .hv-patient_details .doctors_list li:hover{
        background-color: #f7f7f7;
    }
    .hv-patient_details .doctors_list li i.bt-delete-doctor{
        position: absolute;
        right: 5px;
        top: 10px;
    }
    .hv-patient_details .doctor_item{
        line-height: 30px;
    }
</style>

