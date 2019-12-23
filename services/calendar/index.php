<?php
if(!isset($CONFIG))
    require_once('../../config.php');

if(!Permission::can_view(PERMISSION_CALENDAR)){
    echo_error_view();
}

require_once ($CONFIG->dir . '/services/calendar/Event.php');

$user_id = $USER->id;

if($USER->rol == ROL_AUXILIAR || $USER->rol == ROL_ADMIN){
    $user_id = get_param('u', false);
    $user_id = ($user_id === 'false') ? false : $user_id;
}

Log::create('calendar', 'view', $user_id);

$today = date('Y-m-d H:i:s');

$User_cal = new User($user_id);
?>
<div class="tabs">
    <div class="tab" name="Agenda"><?php
        if($USER->rol == ROL_AUXILIAR || $USER->rol == ROL_ADMIN){?>
            <div class="col-xs-12">
                <div class="box">
                    <h5 class="header_box">
                        <i class="fa fa-user"></i>Busca un paciente o prof.sanitario
                    </h5>
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 col-md-2">
                            <select class="ev_user_type">
                                <option value="1">Pacientes</option>
                                <option value="2">Profesionales Sanitarios</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-md-10">
                            <input type="text" name="search_user" placeholder="Nombre o identificador">
                        </div>
                    </div>
                    <span style="margin-right: 5px">Citas de:</span><?php
                    if($user_id){
                        $User = new User($user_id);
                        if($User->getRol() == ROL_USER){
                            $User = new Patient($user_id);
                            echo $User->getFullName() . ' (NH: ' . $User->getHistoric() . ')';
                        }else{
                            $User = new Staff($user_id);
                            echo $User->getFullName() . ' (ID: ' . $User->getStaff_id() . ')';
                        }
                    }else{
                        echo 'No has seleccionado ningun usuario';
                    }?>
                </div>
            </div><?php
        }
        if($user_id){?>
            <div class="col-xs-12">
                <div class="box">
                    <h5 class="header_box"><i class="fa fa-calendar-o"></i>Citas<?php
                        if(Permission::can_edit(PERMISSION_CALENDAR)){?>
                            <label class="header_action"><i class="fa fa-plus"></i>Pedir Cita</label><?php
                        }?>
                    </h5>
                    <input type="hidden" name="user_rol" value="<?= $User_cal->getRol()?>">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-8 no_pd"><?php
                            if($User_cal->getRol() == ROL_USER){
                                $events = Event::getUserEvents($user_id, false, $today);?>
                                <span class="old_events"><?= count($events) . ' citas pasadas'?></span>
                                <div class="list_limit_height-md">
                                    <ul class="event_list"></ul>
                                </div><?php
                            }else{
                                $today = explode(' ', $today);
                                $today = $today[0];?>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="row doctor_agenda_header">
                                            <input type="hidden" name="doctor_day" value="<?= strtotime($today)?>">
                                            <i class="col-xs-1 fa fa-angle-left bt-previous"></i>
                                            <span class="event_day col-xs-10 col-xs-offset-1"></span>
                                            <i class="col-xs-1 fa fa-angle-right bt-next"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="list_limit_height-md">
                                            <ul class="event_list"></ul>
                                        </div>
                                    </div>
                                </div><?php
                            }?>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <div class="calendar"></div>
                        </div>
                    </div>
                </div>
            </div><?php
        }
        ?>
    </div>
</div>
<style>
    .doctor_agenda_header{
        margin-bottom: 5px;
        padding: 5px;
        border: 1px solid #d8d8d8;
        background-color: #f2f1ef;
        position: relative;
    }
    .doctor_agenda_header i{
        font-size: 30px;
        color: #666;
        position: absolute;
        top: 0;
        width: 50px;
    }
    .doctor_agenda_header i.bt-previous{
        left: 0;
    }
    .doctor_agenda_header i.bt-next{
        right: 0;
    }
    .doctor_agenda_header i,
    .doctor_agenda_header span{
        text-align: center;
    }
    .event_list{
        min-height: 38px;
    }
    .event_list .event_item {
        border-top: 1px solid #d8d8d8;
    }
    .old_events {
        display: block;
        width: 100%;
        text-align: center;
        border-width: 1px 1px 2px 1px;
        border-style: solid;
        border-color: #d8d8d8;
        padding: 10px;
        margin-bottom: 10px;
        cursor: pointer;
    }
</style>