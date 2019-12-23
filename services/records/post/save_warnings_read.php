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

$ids = get_param('ids', array());

foreach($ids as $id){
    $Warning = new Warning($id);
    if($Warning->getId()){
        $Warning->saveRead();
    }
}

echo json_encode(array('success' => true, 'info' => 'actualizado'));