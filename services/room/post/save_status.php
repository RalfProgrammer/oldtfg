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
require_once($CONFIG->dir . 'services/calendar/Event.php');

$event_id  = get_param('id', false);
$type      = get_param('t', false);

$Event = new Event($event_id);

if($Event->getId()){
    if($USER->id != $Event->getDoctor()){
        echo json_encode(array(
            'success' => false,
            'info'    => 'No eres el medico'
        ));
        die();
    }
    $Report = Report::byEvent($Event->getId());
    if($Report->getEnd() || $Report->getAbsence()){
        echo json_encode(array(
            'success' => false,
            'info'    => 'La cita esta finalizada'
        ));
        die();
    }
    $now = date('Y-m-d H:i:s');
    $finished = false;
    switch($type){
        case 'absence'      : $Report->setAbsence(1);$finished = true;break;
        case 'no_absence'   : $Report->setAbsence(0);break;
        case 'call'         : $Report->setCalled($now);break;
        case 'end'          : $Report->setEnd($now);$finished = true;break;
        case 'start'        : $Report->setStart($now);break;
        case 'report'       :
            $text = stripslashes(rawurldecode(get_param('r','')));
            $Report->setReport($text);break;
        default:
            json_encode(array(
                'success' => false,
                'info'    => 'El tipo no existe'
            ));
            die();
    }
    $Report->setEvent($event_id);
    if($Report->save()){
        if($finished){
            $Event->setFinished(1);
            $Event->save();
        }
        echo json_encode(array(
            'success' => true,
            'info'    => $now
        ));
    }else{
        echo json_encode(array(
            'success' => false,
            'info'    => 'Error al guardar'
        ));
    }
}else{
    echo json_encode(array(
        'success' => false,
        'info'    => 'El evento no existe'
    ));
}