<?php
/**
 * User: Denis
 * Date: 6/04/14
 */

if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_CALENDAR)){
    echo_json(false, 'No tienes permisos');
}

require_once($CONFIG->dir . 'services/calendar/Event.php');

$user   = get_param('u', false);
switch($USER->rol){
    case ROL_USER:
    case ROL_DOCTOR:
        $user = $USER->id;
        break;
}

$from   = get_param('f', false);
$from   = ($from == 'false') ? false : $from;
if($from){
    $from   = date('Y-m-d', ($from / 1000));
}

$to     = get_param('t', false);
$to     = ($to == 'false') ? false : $to;
if($to){
    $to     = date('Y-m-d', ($to / 1000));
}

$same_day = ($from === $to);

if($same_day){
    $to = strtotime('+1 day', strtotime($from));
    $to = date('Y-m-d', $to);
}

$events = Event::getUserEvents($user, $from . ' 00:00:00', $to . ' 00:00:00');

$User_event = new User($user);
?>
<div class="cal-event_list">
    <h4>Eventos de <?= $User_event->getFullName()?></h4>
    <?php
    if($same_day){?>
        <span>Dia: <?= $from?></span><?php
    }else{?>
        <span><?php
        if($from){?>
            Desde: <?= $from?><?php
        }
        if($to){?>
             Hasta: <?= $to?><?php
        }?>
        </span><?php
    }?>
    <div class="list_limit_height-md">
        <ul><?php
            if(count($events) > 0){
                foreach($events as $event){?>
                <li name="<?= $event->id?>">
                    <div class="row">
                        <table>
                            <tr class="col-xs-12 col-sm-6">
                                <td><i class="fa fa-calendar-o"></i>Fecha:</td>
                                <td><?= $event->other->day_start . ' ' . $event->other->hour ?></td>
                            </tr>
                            <tr class="col-xs-12 col-sm-6">
                                <td><i class="fa fa-user-md"></i>P.Sanitario:</td>
                                <td><?= $event->other->doctor ?></td>
                            </tr>
                            <tr class="col-xs-12 col-sm-6">
                                <td><i class="fa fa-info-circle"></i>Solicitud:</td>
                                <td><?= $event->getRequest() ?></td>
                            </tr><?php
                            if(!$event->getOnline()){?>
                                <tr class="col-xs-12 col-sm-6">
                                    <td><i class="fa fa-map-marker"></i>Lugar:</td>
                                    <td><?= $event->getLocation() ?></td>
                                </tr><?php
                            }?>
                            <tr class="col-xs-12 col-sm-6">
                                <td><i class="fa fa-hospital-o"></i>Tipo:</td>
                                <td><?= ($event->getOnline())? 'Online' : 'Presencial'?></td>
                            </tr>
                        </table>
                    </div>
                    </li><?php
                }
            }else{?>
                <li class="empty_list">- No tiene eventos -</li><?php
            }?>
        </ul>
    </div>
</div>
<style>
    .cal-event_list span,
    .cal-event_list td {
        font-size: 14px;
    }
    .cal-event_list tr {
        padding: 5px 0;
    }
    .cal-event_list td {
        background-color: #fff;
        border: none;
        text-align: left;
        min-width: 80px;
        vertical-align: middle;
        padding: 0 5px;
    }
    .cal-event_list > span {
        display: block;
        margin: 10px 0;
        width: 100%;
        text-align: center;
    }
    .cal-event_list ul li{
        padding: 5px 10px;
        border: 1px solid #d8d8d8;
    }
    .cal-event_list ul li:not(:last-child){
        margin-bottom: 10px;
    }
    .cal-event_list table{
        margin-bottom: 0;
        border: none;
    }
    .cal-event_list td i{
        margin-right: 5px;
        font-size: 18px;
        color: #666;
    }
</style>