<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_MEDICINE)){
    echo json_encode(array(
        'success' => false,
        'info'    => 'No puedes acceder'
    ));
    die();
}

echo json_encode(array(
    'success' => true,
    'info'    => array()
));