<?php
if(!isset($CONFIG))
    require_once('../../config.php');

if(!Permission::can_view(PERMISSION_ROOM)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}

Log::create('room');

$now  = strtotime('now');
$from = date('Y-m-d', $now);
$to   = strtotime('+24 hours', strtotime($from));
$to   = strtotime('-1 second', $to);
$to   = date('Y-m-d H:i:s', $to);
$from .= ' 00:00:00';

require_once($CONFIG->dir . 'services/note/Note.php');
?>

<div class="tabs">
    <div class="tab" name="Sala de espera"><?php
        switch($USER->rol){
            case ROL_USER    : require_once($CONFIG->dir . 'services/room/view/patient_view.php');break;
            case ROL_DOCTOR  : require_once($CONFIG->dir . 'services/room/view/doctor_view.php');break;
            default : require_once($CONFIG->dir . 'views/error_view.php');
        }?>
    </div>
</div>

<style>
    .event_details i {
        margin-right: 5px;
    }
    .room_note{
        height: 100px;
    }
    input[type=checkbox][name=room_note_visible]{
        display: inline-block;
        margin-right: 5px;
    }
</style>