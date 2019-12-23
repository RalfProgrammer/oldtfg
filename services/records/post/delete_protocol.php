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
require_once($CONFIG->dir . 'services/records/Protocol.php');

$id       = get_param('id', false);
$Protocol = new Protocol($id);

if(!$Protocol->getId()){
    echo json_encode(array(
        'success' => false,
        'info'    => 'No existe el protocolo'
    ));
    die();
}

if($Protocol->delete()){
    echo json_encode(array(
        'success' => true,
        'info'    => 'Borrado correctamente'
    ));
}else{
    echo json_encode(array(
        'success' => true,
        'info'    => 'Error al borrar'
    ));
}