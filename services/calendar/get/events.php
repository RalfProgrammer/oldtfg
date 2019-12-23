<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_CALENDAR)){
    echo json_encode(array(
        'success' => true,
        'info'    => 'No tienes permisos'
    ));
    die();
}

require_once($CONFIG->dir . 'services/calendar/Event.php');

$user_id = get_param('u', false);

if($USER->rol == ROL_USER && $USER->id != $user_id){
    echo_json(false, 'Solo puedes ver tus citas');
}

$User_cal = new User($user_id);

if($User_cal->getRol() == ROL_USER){
    $from = get_param('d', false);
    if($from){
        $from = date('Y-m-d', ($from / 1000));
        $from .= ' 00:00:00';
    }
    echo json_encode(array(
        'success' => true,
        'info'    => Event::getUserEvents($user_id, $from)
    ));

}else if($User_cal->getRol() == ROL_DOCTOR){
    $from   = get_param('d', false);
    $action = get_param('a', false);
    switch($action){
        case 'next':
            $to   = date('Y-m-d', strtotime('+2 day', $from));
            $from = date('Y-m-d', strtotime('+1 day', $from));
            break;
        case 'previous':
            $to   = date('Y-m-d', $from);
            $from = date('Y-m-d', strtotime('-1 day', $from));
            break;
        default :
            $to   = date('Y-m-d', strtotime('+1 day', $from));
            $from = date('Y-m-d', $from);
    }
    $from_text = explode('-', $from);

    $from   .= ' 00:00:00';
    $to     .= ' 00:00:00';

    debugPHP($from, 'from');
    debugPHP($to, 'to');

    echo_json(true, array(
        'day'      => $from_text[2] . '/' . $from_text[1] . '/' . $from_text[0],
        'day_time' => strtotime($from),
        'events'   => Event::getUserEvents($user_id, $from, $to)
    ));
}



