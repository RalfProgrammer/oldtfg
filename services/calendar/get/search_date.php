<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_CALENDAR)){
    echo 'No tienes permisos';
    die();
}

require_once($CONFIG->dir . 'services/calendar/Event.php');

$user_id = get_param('user', false);

if($USER->rol == 1 && $USER->id != $user_id){
    echo json_encode(array(
        'success' => false,
        'info'    => 'Solo puedes pedir cita para ti'
    ));
    die();
}

$date   = get_param('date', false);
$doctor = get_param('doctor', false);
$type   = get_param('type', false);

$today = date('Y-m-d');
if(strtotime($date . '00:00') < strtotime($today)){
    echo json_encode(array(
        'success' => false,
        'info'    => 'Ese dia ha pasado'
    ));
    die();
}

$Doctor = new Staff($doctor);

$hour = (strpos($Doctor->getHorary(), 'N') !== false) ? '00:00' : '08:00';

switch($type){
    case 1:
        $limit     = date('Y-m-d H:i', strtotime('+24 hour', strtotime($date . ' 00:00')));
        $dates_aux = $Doctor->generateFreeDates("$date $hour", $limit);
        $dates = array();
        foreach($dates_aux as $key => $date){
            $date = explode(' ', $date);
            $dates[$key] = $date[1];
        }
        ;break;
    case 2:
    case 3:
        $dates = $Doctor->generateFreeDates("$date $hour");
        ;break;
}

echo json_encode(array(
    'success' => true,
    'info'    => $dates
));