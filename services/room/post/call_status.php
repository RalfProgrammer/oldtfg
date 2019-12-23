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

require_once($CONFIG->dir . 'services/room/Report.php');
require_once($CONFIG->dir . 'services/room/Call.php');
require_once($CONFIG->dir . 'services/calendar/Event.php');

$call_id  = get_param('c', false);
$action   = get_param('a', 'start');

if($Call = new Call($call_id)){
    if($Call->getCaller() != $USER->id && $Call->getReceptor() != $USER->id){
        echo_json(false, 'No estas en la conversacion');
    }

    $start   = explode(' ', $Call->getStart());
    $started = $start[0] != '0000-00-00';
    switch($action){
        case 'start':
            if($started){
                echo_json(false, 'Ya esta iniciada');
            }
            $Call->setStart(date('Y-m-d H:i:s'));
            break;
        case 'end':
            $end = explode(' ', $Call->getEnd());
            if(!$started){
                echo_json(false, 'No esta iniciada');
            }
            if($end[0] != '0000-00-00'){
                echo_json(false, 'Ya esta finalizada');
            }
            $Call->setEnd(date('Y-m-d H:i:s'));
            break;
    }

    if($Call->save()){
        echo_json(true, 'Guardado');
    }else{
        echo_json(false, 'Error al guardar');
    }
}
echo_json(false, 'No existe');