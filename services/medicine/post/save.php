<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_edit(PERMISSION_RECORD)){
    echo_json(false, 'No tienes permiso');
}

require_once($CONFIG->dir . 'services/medicine/Medicine.php');

$user    = get_param('u', 0);

if($USER->rol == ROL_USER){
    if($user != $USER->id){
        echo_json(false, 'No tienes permiso');
    }
}
$id      = get_param('id', 0);
$start   = get_param('start', false);
$intvl1  = get_param('intvl1', false);
$intvl2  = get_param('intvl2', false);
$intvl3  = get_param('intvl3', false);

if(!$intvl1 || !is_numeric($intvl1) || $intvl1 < 1)
    echo_json(false, 'EL formato de la dosis debe ser numerico y mayor que 0');

if(!$intvl2 || !is_numeric($intvl2) || $intvl2 < 1)
    echo_json(false, 'El formato del intervalo de la dosis debe ser numerico y mayor que 0');

if(!in_array($intvl3, array('h','d','w','m','y')))
    echo_json(false, 'Formato del intervalo incorrecto (hora,dia,mes..)');

if(!$start || !strtotime($start))
    echo_json(false, 'Fecha de inicio incorrecta');

$valid = false;
$all_medicines = Medicine::getAll();
foreach($all_medicines as $med){
    if($med['id'] == $id){
        $valid = true;
        break;
    }
}

if(!$valid)
    echo_json(false, 'El tratamiento no existe');

$Medicine = new Medicine();
$Medicine->setPatient   ($user);
$Medicine->setDoctor    ($USER->id);
$Medicine->setMedicine  ($id);
$Medicine->setStart     ($start);
$Medicine->setInterval  ("$intvl1#$intvl2#$intvl3");

if($Medicine->save()){
    echo_json(true, 'Tratamiento guardado');
}else{
    echo_json(false, 'Error al guardar');
}