<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_edit(PERMISSION_PATIENT)){
    echo_json(false, 'No tienes permiso');
}

$id     = get_param('id', 0);
$height = get_param('h', 0);
$weight = get_param('w', 0);

if(!is_numeric($height) || !is_numeric($weight))
    echo_json(false, 'Los valores no son numericos');

if($Patient = new Patient($id)){
    $Patient->setHeight($height);
    $Patient->setWeight($weight);
    if($Patient->save()){
        echo_json(true, 'Guardado correctamente');
    }else{
        echo_json(false, 'Error al guardar');
    }
}else{
    echo_json(false, 'El paciente no existe');
}

