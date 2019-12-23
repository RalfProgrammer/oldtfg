<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_edit(PERMISSION_RECORD)){
    echo_json(false, 'No tienes permiso');
}

require_once($CONFIG->dir . 'services/medicine/Medicine.php');

$id = get_param('m', 0);
$t  = stripslashes(rawurldecode(get_param('t', '')));

if(!($Medicine = new Medicine($id))){
    echo_json(false, 'No existe el tratamiento');
}

if($t == ''){
    echo_json(false, 'Debes rellenar el motivo');
}

if($Medicine->other->finished){
    echo_json(false, 'El tratamiento ya estaba cancelado');
}

if($USER->rol == ROL_USER){
    if($Medicine->getPatient() != $USER->id){
        echo_json(false, 'No tienes permiso');
    }
}

$Medicine->setEnd       (date('Y-m-d H:i:s'));
$Medicine->setStop      ($t);
$Medicine->setStop_user ($USER->id);

if($Medicine->save()){
    echo_json(true, 'Cancelada correctamente');
}else{
    echo_json(false, 'Error al cancelar');
}