<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_edit(PERMISSION_RECORD)){
    echo_view('No tienes permiso');
}

require_once($CONFIG->dir . 'services/medicine/Medicine.php');

$id = get_param('m', 0);
if(!($Medicine = new Medicine($id))){
    echo_view('No existe el tratamiento solicitado');
}

if($Medicine->other->finished){
    echo_view('El tratamiento ya esta cancelado');
}

if($USER->rol == ROL_USER){
    if($Medicine->getPatient() != $USER->id){
        echo_view('No tienes permiso');
    }
}
?>

<div class="md_cancel">
    <h5>¿Estas seguro de cancelar el tratamiento?</h5>
    <span>Tratamiento: <?= $Medicine->other->name ?></span>
    <span>Fecha creación: <?= $Medicine->timestamp ?></span>
    <textarea placeholder="Motivo"></textarea>
    <div class="row">
        <div class="col-xs-12 col-sm-3 col-sm-offset-6 col-md-2 col-md-offset-8">
            <a class="btn btn-danger bt-yes">Si</a>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2">
            <a class="btn btn-default bt-no">No</a>
        </div>
    </div>
</div>

<style>
    .md_cancel > span{
        font-size: 14px;
        display: block;
    }
    .md_cancel textarea{
        margin-top: 5px;
        height: 100px;
    }
</style>