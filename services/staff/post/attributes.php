<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_edit(PERMISSION_STAFF)){
    echo_json(false, 'No tienes permiso');
}

$id     = get_param('id', 0);
$branch = get_param('b', false);
$horary = get_param('t', false);
$room   = get_param('r', '');
$office = get_param('o', '');
$phone  = get_param('p', '');

if(!$branch || ! $horary)
    echo_json(false, 'Rama y horario obligatorias');

if($Staff = new Staff($id)){
    $Staff->setBranch   ($branch);
    $Staff->setHorary   ($horary);
    $Staff->setRoom     ($room);
    $Staff->setOffice   ($office);
    $Staff->setH_phone  ($phone);
    if($Staff->save()){
        echo_json(true, 'Guardado correctamente');
    }else{
        echo_json(false, 'Error al guardar');
    }
}else{
    echo_json(false, 'El empleado no existe');
}

