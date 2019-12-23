<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_RECORD) || $USER->rol == 1){
    echo json_encode(array(
        'success' => false,
        'info'    => 'No puedes editarlo'
    ));
    die();
}
require_once($CONFIG->dir . 'services/records/Analytic.php');

$id       = get_param('id', false);
$Analytic = new Analytic($id);

if(!$Analytic->getId()){
    echo json_encode(array(
        'success' => false,
        'info'    => 'No existe el protocolo'
    ));
    die();
}

if($Analytic->delete()){
    echo json_encode(array(
        'success' => true,
        'info'    => 'Borrada correctamente'
    ));
}else{
    echo json_encode(array(
        'success' => true,
        'info'    => 'Error al borrar'
    ));
}