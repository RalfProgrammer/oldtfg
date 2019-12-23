<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_RECORD)){
    echo json_encode(array(
        'success' => false,
        'info'    => 'no tienes permiso'
    ));
    die();
}

require_once($CONFIG->dir . 'services/records/Warning.php');

$id = get_param('id', 0);

$Warning = new Warning($id);

if($Warning->getId()){
    if($Warning->delete()){
        echo json_encode(array(
            'success' => true,
            'info'    => 'borrado'
        ));
    }else{
        echo json_encode(array(
            'success' => false,
            'info'    => 'Error al borrar'
        ));
    }
}else{
    echo json_encode(
        array(
            'success' => false,
            'info'    => 'No existe'
        ));
}