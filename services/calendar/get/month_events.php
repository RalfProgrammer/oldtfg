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

$user_id = get_param('u', 0);
$month   = get_param('m', false);
$year    = get_param('y', false);

if($USER->id != $user_id && $USER->rol < ROL_AUXILIAR){
    echo json_encode(array(
        'success' => false,
        'info'    => 'Solo puedes ver tus citas'
    ));
    die();
}

$date = "$year-$month-01";

if (($date = strtotime($date)) === false) {
    echo json_encode(array(
        'success' => false,
        'info'    => 'La fecha solicitada es incorrecta'
    ));
    die();
}

$from   = strtotime('-7 day', $date);
$from   = date('Y-m-d', $from);
$to     = strtotime('+37 day', $date);
$to     = date('Y-m-d', $to);

echo json_encode(array(
    'success' => true,
    'info'    => $events = Event::getUserEvents($user_id, $from, $to)
));