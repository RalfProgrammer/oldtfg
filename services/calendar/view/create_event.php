<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_edit(PERMISSION_CALENDAR)){
    echo_view('No tienes permisos');
}

require_once ($CONFIG->dir . '/services/users/Relation.php');

$user_id = $USER->id;

$Patient    = false;
$patients   = false;
$Doctor     = false;
$doctors    = false;

switch($USER->rol){
    case ROL_USER :
        $Patient = new Patient($USER->id);
        $doctors = Relation::getDoctors($Patient->getId());
        break;
    case ROL_DOCTOR :
        $p        = get_param('p', false);
        $Patient  = ($p)? new Patient($p) : false;
        $Doctor   = new Staff($USER->id);
        $patients = Relation::getPatients($Doctor->getId());
        break;
    case ROL_AUXILIAR:
    case ROL_ADMIN:
        $p = get_param('p', false);
        $d = get_param('d', false);
        $Patient = ($p) ? new Patient($p) : false;
        if($Patient){
            $doctors = Relation::getDoctors($Patient->getId());
        }
        $Doctor  = ($d) ? new Staff($d) : false;
        if($Doctor){
            $patients = Relation::getPatients($Doctor->getId());
        }
        break;
}

if(!$Patient && !$Doctor){
    echo '<h5 style="text-align: center;padding: 25px 0;display: block">Se ha producido un error en la identificacion de los usuarios</h5>';
    die();
}

?>
<div class="hv-create_event row">
    <div class="col-xs-12 col-sm-6">
        <label>Paciente:</label><?php
        if($Patient){?>
            <input type="hidden" name="ev_patient" value="<?= $Patient->getId()?>">
            <input type="text" value="<?= $Patient->getFullName(). ' ( NH:' . $Patient->getHistoric() . ' )'?>" class="readable"><?php
        }else{?>
            <select name="ev_patient"><?php
                foreach($patients as $patient){?>
                    <option value="<?= $patient->getId()?>"><?= $patient->getLastname() . ', ' . $patient->getName()?> ( NH: <?= $patient->getHistoric()?> )</option><?php
                }?>
            </select><?php
        }?>
    </div>
    <div class="col-xs-12 col-sm-6">
        <label>Profesional Sanitario:</label><?php
        if($Doctor){?>
            <input type="hidden" name="ev_doctor" value="<?= $Doctor->getId()?>">
            <input type="text" value="<?= $Doctor->getFullName(). ' ( ID:' . $Doctor->getStaff_id() . ' )'?>" class="readable"><?php
        }else{?>
            <select name="ev_doctor"><?php
                foreach($doctors as $doctor){?>
                    <option value="<?= $doctor->getId()?>"><?= $doctor->getLastname() . ', ' . $doctor->getName()?> ( ID: <?= $doctor->getStaff_id()?> )</option><?php
                }?>
            </select><?php
        }?>
    </div>
    <div class="col-xs-12 col-sm-4">
        <label>Busqueda:</label>
        <select class="ev_search">
            <option value="1">Manual</option>
            <option value="2">Proxima libre</option>
            <option value="3">Proxima a partir del dia</option>
        </select>
    </div>
    <div class="search_day col-xs-12 col-sm-8">
        <div class="search on col-xs-12" name="1">
            <div class="col-xs-12 col-sm-6">
                <label>Dia:</label>
                <input type="text" class="inp_date" placeholder="busca un dia">
            </div>
            <div class="col-xs-12 col-sm-6">
                <label>Hora:</label>
                <select class="sel_results">
                    <option value="0">- Seleccione un dia-</option>
                </select>
            </div>
        </div>
        <div class="search col-xs-12" name="2">
            <div class="col-xs-12 col-sm-12">
                <label>Libres:</label>
                <select class="sel_results">
                    <option value="0">Buscando..</option>
                </select>
            </div>
        </div>
        <div class="search col-xs-12" name="3">
            <div class="col-xs-12 col-sm-6">
                <label>A partir del dia:</label>
                <input type="text" class="inp_date" placeholder="Busca un dia">
            </div>
            <div class="col-xs-12 col-sm-6">
                <label>Libres:</label>
                <select class="sel_results">
                    <option value="0">- Seleccione un dia -</option>
                </select>
            </div>
        </div>
        <input type="hidden" name="ev_date">
    </div>
    <div class="col-xs-12">
        <label>Tipo:</label>
        <select class="ev_type">
            <option value="1">Online</option>
            <option value="0">Presencial</option>
        </select>
    </div>
    <div class="col-xs-12">
        <label>Motivo:</label>
        <textarea class="ev_request"></textarea>
    </div>
    <div class="col-xs-12 col-sm-3 col-sm-offset-6 col-md-2 col-md-offset-8" style="margin-bottom: 10px">
        <a class="btn btn-primary bt-send">Pedir</a>
    </div>
    <div class="col-xs-12 col-sm-3 col-md-2">
        <a class="btn btn-default bt-cancel">Cancelar</a>
    </div>
</div>

<style>
    input[type=text].readable {
        border: none!important;
    }
    .search_day > div{
        display: none;
    }
    .search_day > div.on{
        display: block;
    }
    .hv-create_event .ev_request{
        min-height: 90px;
    }

</style>