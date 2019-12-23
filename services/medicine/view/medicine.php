<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_RECORD)){
    echo_view('No tienes permiso');
}

require_once($CONFIG->dir . 'services/medicine/Medicine.php');

$id = get_param('m', 0);

if(!($Medicine = new Medicine($id))){
    echo_view('No existe el tratamiento solicitado');
}

if($USER->rol == ROL_USER){
    if($Medicine->getPatient() != $USER->id){
        echo_view('No tienes permiso');
    }
}

$Patient = new Patient($Medicine->getPatient());
$Doctor  = new Staff($Medicine->getDoctor());
?>

<div class="md_view">
    <ul>
        <li>
            <span><i class="fa fa-medkit"></i>Tratamiento:</span>
            <span><?= $Medicine->other->name ?></span>
        </li>
        <li>
            <span><i class="fa fa-user"></i>Paciente:</span>
            <span><?= $Patient->getFname() ?></span>
        </li>
        <li>
            <span><i class="fa fa-user-md"></i>P.Sanitario:</span>
            <span><?= $Doctor->getFname() ?></span>
        </li>
        <li>
            <span><i class="fa fa-calendar-o"></i>Recetado:</span>
            <span><?= $Medicine->getTimestamp() ?></span>
        </li>
        <li>
            <span><i class="fa fa-calendar-o"></i>Inicio:</span>
            <span><?= $Medicine->getStart() ?></span>
        </li>
        <li>
            <span><i class="fa fa-history"></i>Dosis:</span>
            <span><?= $Medicine->other->interval_text ?></span>
        </li>
        <li>
            <span><i class="fa fa-square-o"></i>Estado:</span>
            <span><?= $Medicine->other->status ?></span>
        </li><?php
        if($Medicine->other->finished){?>
            <li>
                <span><i class="fa fa-user-md"></i>Finalizado por:</span>
                <span><?php
                    if($Medicine->getStop_user() != $Medicine->getDoctor())
                        $Doctor = new Staff($Medicine->getStop_user());
                    echo $Doctor->getLastname() . ', ' . $Doctor->getName();
                ?></span>
            </li>
            <li>
                <span><i class="fa fa-info-circle"></i>Motivo:</span>
                <span><?= $Medicine->getStop() ?></span>
            </li><?php
        }?>
    </ul>
</div>

<style>
    .md_view span{
        font-size: 14px;
    }
    .md_view span i{
        color: #666;
        font-size: 18px;
        margin-right: 5px;
    }
    .md_view li{
        padding: 10px;
    }
    .md_view li:not(:last-child){
        border-bottom: 1px solid #d8d8d8;
    }
</style>