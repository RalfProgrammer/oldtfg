<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_CALENDAR)){
    echo_json(false, 'No tienes permisos');
}

require_once ($CONFIG->dir . '/services/calendar/Event.php');

$event_id = get_param('e', false);

if($Event = new Event($event_id)){
    $error = false;
    switch($USER->rol){
        case ROL_USER:
            $error = $Event->getUser() != $USER->id;
            break;
        case ROL_DOCTOR:
            $error = $Event->getDoctor() != $USER->id;
            break;
        case ROL_AUXILIAR:
        case ROL_ADMIN:
            $error = !Permission::can(PERMISSION_CALENDAR, 3);
            break;
    }
    if($error){
        echo_json(false, 'No tienes permisos para ver el evento');
    }
    if(!Permission::can_edit(PERMISSION_CALENDAR)){
        echo_json(false, 'No tienes permisos para editar el evento');
    }
    if($Event->delete()){
        echo_json(true, 'Evento anulado correctamente');
    }else{
        echo_json(false, 'Se ha producido un error al anularlo');
    }
}else{
    echo_json(false, 'El evento no existe');
}