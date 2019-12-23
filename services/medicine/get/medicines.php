<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_RECORD)){
    echo_json(false, 'No tienes permiso');
}

require_once($CONFIG->dir . 'services/medicine/Medicine.php');

echo json_encode(array(
    'success' => true,
    'info'    => Medicine::getAll()
));