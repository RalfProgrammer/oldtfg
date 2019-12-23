<?php
if(!Permission::can_view(PERMISSION_ROOM)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}

require_once($CONFIG->dir . 'services/calendar/Event.php');

if($next_event = Event::getUserEvents($USER->id, $from, $to, false, true)){
    $next_event = array_shift($next_event);
}

if($next_event && count($next_event) > 0){

    require_once($CONFIG->dir . 'services/room/Report.php');

    $Note   = Note::getByEventAndUser($next_event->id);
    $Doctor = new Staff($next_event->getDoctor());?>

    <input type="hidden" name="event_id" value="<?= $next_event->id ?>">
    <div class="col-xs-12">
        <div class="col-xs-12 col-sm-4">
            <div class="box event_details">
                <h5 class="header_box dots">
                    <i class="fa fa-h-square"></i> Sala de espera <?= $next_event->getLocation() ?>
                </h5>
                <h3 style="text-align: center;margin-top: 25px">Cita <?= $next_event->other->hour?></h3>
                <h6 style="text-align: center;margin-bottom: 25px">
                    <?= ($next_event->online)? 'Espere a ser llamado' : 'Esta cita es presencial' ?>
                </h6>
                <div >
                    <h6>Estado sala de espera:</h6>
                    <ul style="padding-left: 10px">
                        <li><i class="fa fa-group"></i>Pacientes delante: </li>
                    </ul>
                </div>
                <div>
                    <h6>Detalles de la cita:</h6>
                    <ul style="padding-left: 10px">
                        <li><i class="fa fa-calendar"></i>Fecha: <?= $next_event->getStart()?></li>
                        <li><i class="fa fa-plus-square"></i>Motivo: <?= $next_event->getRequest()?></li>
                        <li><i class="fa fa-user-md"></i>Doctor: <?= $Doctor->getFullname() ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-8">
            <div class="box event_note">
                <h5 class="header_box dots">
                    <i class="fa fa-book"></i> Notas de la cita
                </h5>
                <textarea style="height: 200px;" class="room_note"><?= ($Note)? $Note->getText() : '' ?></textarea>
                <input type="hidden" name="room_note_id" value="<?= ($Note)? $Note->getId() : 0 ?>">
                <input type="checkbox" <?= ($Note && $Note->getVisible()? 'checked':'')?> name="room_note_visible">Quiero que el medico pueda verla
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-12 col-sm-6">
            <div class="box hv-record">
                <h5 class="header_box dots">
                    <i class="fa fa-file-text-o"></i> Informe Evolutivo
                </h5>
                <div style="max-height: 200px;overflow: auto"><?php
                    echo Report::getCompleteReport($USER->id);?>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="box">
                <h5 class="header_box dots">
                    <i class="fa fa-medkit"></i> Mi Medicación
                </h5>
                <?php
                $filter = 'actual';
                require_once($CONFIG->dir . 'services/medicine/view/user_medicine.php');
                ?>
            </div>
        </div>
    </div><?php
}else{
    if($next_event = Event::getUserEvents($USER->id, $from, false, true)){
        $next_event = array_shift($next_event);
    }?>
    <div class="box">
        <h5 class="header_box">
            <i class="fa fa-calendar"></i> Hoy no tienes cita
        </h5><?php
    if($next_event){?>
        <span>Tu proxima cita es <?= $next_event->getStart() . ' ' . (($next_event->online)? 'Presencial' : 'Online') . ')'?></span><?php
    }else{?>
        <span>No tienes ninguna cita programada</span><?php
    }
    ?>
    </div><?php
}?>

<style>
    .event_details i {
        margin-right: 5px;
        color: #666;
    }
    .event_note input[type=checkbox]{
        display: inline-block;
        margin-right: 5px;
    }
</style>