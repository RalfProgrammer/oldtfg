<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_CALENDAR)){
    echo_view('No tienes permisos');
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
    }
    if($error){
        echo_view('No tienes permisos para ver el evento');
    }
    $Patient = new Patient($Event->getUser());
    $Doctor  = new Staff($Event->getDoctor());?>
    <div class="ev-info">
        <div>
            <span><i class="fa fa-user"></i>Paciente:</span>
            <span><?= $Patient->getLastname() . ', ' .$Patient->getName() . ' ( NH:' . $Patient->getHistoric() . ' )' ?></span>
        </div>
        <div>
            <span><i class="fa fa-user-md"></i>Profesional:</span>
            <span><?= $Doctor->getLastname() . ', ' .$Doctor->getName() . ' ( ID:' . $Doctor->getStaff_id() . ' )' ?></span>
        </div>
        <div>
            <span><i class="fa fa-calendar"></i>Solicitada:</span>
            <span><?= $Event->getTimestamp() ?></span>
        </div>
        <div>
            <span><i class="fa fa-calendar-o"></i>Inicio:</span>
            <span><?= $Event->other->day_start . ' ' . $Event->other->hour ?></span>
        </div>
        <div>
            <span><i class="fa fa-calendar-o"></i>Fin:</span>
            <span><?= $Event->other->day_end . ' ' . $Event->other->hour_end ?></span>
        </div>
        <div>
            <span><i class="fa fa-info-circle"></i>Solicitud:</span>
            <span><?= $Event->getRequest() ?></span>
        </div>
        <div>
            <span><i class="fa fa-hospital-o"></i>Tipo:</span>
            <span><?= $Event->getOnline() ? 'Online' : 'Presencial' ?></span>
        </div>
        <?php
        if(!$Event->getOnline()){?>
            <div>
                <span><i class="fa fa-map-marker"></i>Lugar:</span>
                <span><?= $Event->getLocation() ?></span>
            </div><?php
        }?>
    <?php
    if(Permission::can_edit(PERMISSION_CALENDAR)){
        if(strtotime('now') < $Event->other->start_time){?>
            <span class="row">
                <div class="col-xs-12 col-sm-6 col-sm-offset-6 col-md-4 col-md-offset-8 col-lg-3 col-lg-offset-9">
                    <a class="btn btn-danger bt-delete"><i class="fa fa-trash-o"></i>Anular</a>
                </div>
            </span><?php
        }
    }?>
    </div>
    <style>
        .ev-info div span{
            font-size: 14px;
        }
        .ev-info > div{
            padding: 10px;
        }
        .ev-info > div:not(:last-child){
            border-bottom: 1px solid #d8d8d8;
        }
        .ev-info > div i{
            color: #666;
            margin-right: 5px;
            font-size: 18px;
        }
        .ev-info > div > div span:first-child{
            display: block;
        }
        .ev-info > span{
            display: block;
            margin-top: 5px;
        }
    </style><?php
}else{
    echo_view('El evento no existe');
}
