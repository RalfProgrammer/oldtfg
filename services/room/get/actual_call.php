<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_ROOM)){
    echo json_encode(array(
        "success" => false,
        "info"    => 'No tienes permisos'
    ));
    die();
}

require_once($CONFIG->dir . 'services/room/Call.php');
require_once($CONFIG->dir . 'services/calendar/Event.php');

if($call = Call::getActualCall()){
    $info = new StdClass();
    $info->id     = $call->id;
    $info->event  = $call->event;
    $info->caller = new User($call->caller);
    $info->caller = $info->caller->getFullName();
    echo_json(true, $info);
}else{
    echo_json(false, 'No te han llamado');
}