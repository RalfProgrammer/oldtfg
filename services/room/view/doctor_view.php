<?php
if(!Permission::can_view(PERMISSION_ROOM)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}

require_once($CONFIG->dir . 'services/room/Report.php');
require_once($CONFIG->dir . 'services/calendar/Event.php');

$patients = Event::getUserEvents($USER->id, $from, $to);
?>

<div class="hv-doctor-room box row">
    <h5 class="header_box"><i class="fa fa-group"></i>Tus pacientes de hoy:</h5>
    <div class="col-xs-12">
        <div class="row user_list_header">
            <div class="col-xs-9 col-sm-7"><h5>Paciente</h5></div>
            <div class="col-xs-2 col-sm-1"><h5>Hora</h5></div>
            <div class="hidden-xs col-sm-3"><h5>Estado Cita</h5></div>
        </div>
        <ul class="patients_list">
            <?php
            if(count($patients) > 0){
                foreach($patients as $event){
                    $Patient = new Patient($event->user);?>
                    <li name="<?= $event->getId()?>">
                        <div class="row">
                            <div class="col-xs-9 col-sm-7 col-md-7">
                                <img class="avatar patient_img" src="<?= $Patient->getSrcAvatar()?>">
                                <span class="patient_name <?= ($event->finished)? 'e_finished' : ''?>"><?= $Patient->getLastname() . ', ' . $Patient->getName()?></span>
                            </div>
                            <div class="col-xs-2 col-sm-1 col-md-1">
                                <?= $event->other->hour?><?php
                                if($event->online){?>
                                    <i class="fa fa-video-camera" title="Online"></i><?php
                                }else{?>
                                    <i class="fa fa-hospital-o" title="Presencial"></i><?php
                                }?>
                            </div>
                            <div class="hidden-xs col-sm-3 col-md-3">
                                <?php
                                    $EvReport = Report::byEvent($event->id);
                                    if($EvReport->getAbsence()){
                                        echo 'Incomparecencia';
                                    }else{
                                        if(!$EvReport->getStart()){
                                            echo 'Sin Iniciar';
                                        }elseif($EvReport->getEnd()){
                                            echo ' Finalizada';
                                        }else{
                                            echo 'En curso';
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </li><?php
                }
            }else{?>
                <li class="empty_list">- No tiene ningun paciente -</li><?php
            }?>
        </ul>
    </div>
</div>

<style>
    .hv-doctor-room li{
        line-height: 30px;
    }
    .hv-doctor-room .patient_img{
        float: left;
    }
    .hv-doctor-room .patient_name{
        display: inline-block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 65%;
        float: left;
    }
    .hv-doctor-room li .patient_name.e_finished{
        text-decoration: line-through;
    }
</style>