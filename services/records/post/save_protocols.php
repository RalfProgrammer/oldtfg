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

$data  = json_decode(get_param('data', false));

$error = "";

foreach($data as $values){
    $Protocol = new Protocol($values->id);
    $Protocol->setUser      ($values->u);
    $Protocol->setName      (stripslashes(rawurldecode($values->name)));
    $Protocol->setCreator   ($USER->id);
    $Protocol->setStart     ($values->start);
    $Protocol->setEnd       ($values->end);

    if(!$Protocol->save()){
        $error .= '"' . $Protocol->getName() . '", ';
    }
}

if($error == ""){
    echo json_encode(array(
        'success' => true,
        'info'    => 'Cambios guardados'
    ));
}else{
    echo json_encode(array(
        'success' => true,
        'info'    => 'Error al guardar: ' . $error
    ));
}