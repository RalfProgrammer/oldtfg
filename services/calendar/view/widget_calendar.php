<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

require_once($CONFIG->dir . 'services/calendar/Event.php');

$now      = strtotime('now');
$from     = date('Y-m-d', $now);

$title = '';
switch($USER->rol){
    case ROL_USER  :
        $title  = 'En los proximos 6 meses tienes';
        $to     = strtotime('+180 days', strtotime($from));
        break;
    case ROL_DOCTOR  :
        $title  = 'Hoy tienes ';
        $to     = strtotime('+24 hours', strtotime($from));
        break;
    default :
        $title = '';
        $event = array();
}

$to       = strtotime('-1 second', $to);
$to       = date('Y-m-d H:i:s', $to);
$from     .= ' 00:00:00';
$events   = Event::getUserEvents(false, $from, $to);

?>
<div class="box">
    <h5 class="header_box dots">
        <i class="fa fa-calendar"></i><?= $title ?> <?= count($events) ?> citas
    </h5>
    <ul class="list_events"><?php
        if(count($events) > 0){
            foreach($events as $event){?>
            <li class="item_event_min" name="<?= $event->id ?>">
                <div class="row">
                    <div class="col-xs-3 col-sm-2 col-md-2">
                        <span class="ev_m_when"><?php
                            if($USER->rol == 1){
                                echo $event->other->day . ' ' . $event->other->month;
                            }else{
                                echo  $event->other->hour;
                            }?>
                        </span>
                    </div>
                    <div class="col-xs-9 col-sm-8 col-md-9 dots">
                        <span><?php
                            if($USER->rol == 1){
                                echo $event->request;
                            }else{
                                $User = new User($event->user);
                                echo $User->getFullName();
                            }?>
                        </span>
                    </div>
                    <div class="hidden-xs col-sm-2 col-md-1">
                        <?= ($event->online)? '<i class="fa fa-video-camera" title="online"></i>' : '<i class="fa fa-hospital-o" title="presencial"></i>'?>
                    </div>
                </div>
                </li><?php
            }
        }else{?>
            <li class="empty_list">- Sin citas -</li><?php
        }?>
    </ul>
</div>