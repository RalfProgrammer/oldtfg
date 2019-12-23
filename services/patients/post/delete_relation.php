<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_edit(PERMISSION_PATIENT) && !Permission::can_edit(PERMISSION_STAFF)){
    echo_json(false, 'No tienes permisos');
}

require_once($CONFIG->dir . '/services/users/Relation.php');

$patient = get_param('p', false);
$doctor  = get_param('d', false);

if($Relation = Relation::getRelation($doctor, $patient)){
    if($Relation->delete()){
        echo_json(true, 'Borrado');
    }else{
        echo_json(false, 'Error al borrar');
    }
}else{
    echo_json(false, 'La relacion no existe');
}
