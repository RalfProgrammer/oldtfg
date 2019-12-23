<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_ROOM)){
    echo_error_view();
}

require_once($CONFIG->dir . 'services/note/Note.php');
require_once($CONFIG->dir . 'services/room/Report.php');
require_once($CONFIG->dir . 'services/calendar/Event.php');

$error = false;
$event_id = get_param('ev', false);?>

<div class="tabs"><?php
    if($Event = new Event($event_id)){
        Log::create('room', 'view', $event_id);
        $Report     = Report::byEvent($Event->getId());
        $Doctor     = new Staff($Event->getDoctor());
        $Patient    = new Patient($Event->getUser());
        $Note       = Note::getByEventAndUser($event_id, $Patient->getId());

        $Report = Report::byEvent($Event->getId());

        if($USER->rol == ROL_USER && $Event->getUser() == $USER->id){
            if($Report && !$Report->isFinished()){?>
                <div class="tab" name="Consulta">
                    <input type="hidden" name="r_event" value="<?= $Event->getId()?>">
                    <?php
                        $from_room = true;
                        $user_id   = $Patient->getId();
                        include ($CONFIG->dir . 'services/records/view/record.php');
                    ?>
                </div><?php
            }else{
                $error = true;
            }
        }else if($USER->rol == ROL_DOCTOR && $Event->getDoctor() == $USER->id){
            $Report = Report::byEvent($Event->getId());
            $is_finished = $Report->getAbsence() || $Report->getEnd();?>
            <div class="tab hv-room" name="Tu consulta">
                <input type="hidden" name="r_event" value="<?= $Event->getId()?>">
                <input type="hidden" name="r_patient" value="<?= $Patient->getId()?>">
                <input type="hidden" name="r_finished" value="<?= ($is_finished) ? 1 : 0?>">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <h5 class="header_box dots">
                                <i class="fa fa-calendar-o"></i>Detalles de la cita
                            </h5>
                            <div class="row">
                                <div class="col-xs-12 room_time <?= ($Report->getId()) ? 'on': ''?>"><?php
                                    if($Report && $Report->getId()){
                                        if($Report->getAbsence()){
                                            echo 'Consulta finalizada por incomparecencia';
                                        }else{
                                            if($Report->getStart()){
                                                echo 'Consulta iniciada ' . $Report->getStart();
                                                if($Report->getEnd()){
                                                    echo ' y finalizada ' . $Report->getEnd();
                                                }else{
                                                    echo ' (no se ha dado por finalizada)';
                                                }
                                            }
                                        }
                                    }?>
                                </div>
                                <div class="col-xs-12 col-sm-8 col-md-10">
                                    <table class="rom_event_info">
                                        <tr>
                                            <td>Dia:</td>
                                            <td><?= $Event->other->day_start?></td>
                                        </tr>
                                        <tr>
                                            <td>Inicio previsto:</td>
                                            <td><?= $Event->other->hour?></td>
                                        </tr>
                                        <tr>
                                            <td>Fin previsto:</td>
                                            <td><?= $Event->other->hour_end ?></td>
                                        </tr>
                                        <tr>
                                            <td>Motivo:</td>
                                            <td class="dots"><?= $Event->getRequest()?></td>
                                        </tr>
                                        <tr>
                                            <td>Tipo:</td>
                                            <td class="dots"><?= $Event->getOnline()? 'Online' : 'Presencial'?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-xs-12 col-sm-4 col-md-2 room_actions"><?php
                                    if(!$is_finished){
                                        if($Event->getOnline()){
                                            if(!$Report->getStart()){?>
                                                <a class="btn btn-primary bt-call">Llamar al paciente</a><?php
                                            }else{?>
                                                <a class="btn btn-primary bt-call">Rellamar al paciente</a><?php
                                            }
                                        }elseif(!$Report->getStart()){?>
                                            <a class="btn btn-primary bt-start">Iniciar consulta</a><?php
                                        }?>
                                        <a class="btn btn-warning bt-absence" style="margin-top: 5px">Incomparecencia</a>
                                        <a class="btn btn-default bt-finish <?= $Report->getStart() ? 'show':'hide'?>" style="margin-top: 5px">Finalizar consulta</a><?php
                                    }?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $from_room = true;
                $user_id   = $Patient->getId();
                include ($CONFIG->dir . 'services/records/view/record.php');
                ?>
                <div class="row"><?php
                    if($Note && $Note->getVisible()){?>
                        <div class="col-xs-12">
                            <div class="box">
                                <h5 class="header_box dots">
                                    <i class="fa fa-book"></i>Nota del paciente
                                </h5>
                                <textarea style="height: 250;border: none" readonly><?= $Note->getText()?></textarea>
                            </div>
                        </div><?php
                    }?>

                    <div class="col-xs-12">
                        <div class="box">
                            <h5 class="header_box dots">
                                <i class="fa fa-file-o"></i>Informe de la consulta
                            </h5>
                            <textarea placeholder="Escriba aqui el nuevo informe" style="height: 250px" class="new_report" <?= ($is_finished || !$Report->getStart()) ? 'disabled':''?>><?= $Report->getReport()?></textarea>
                        </div>
                    </div>
                </div>
            </div><?php
        }else{
            $error = true;
        }
    }else{
        $error = true;
    }?>
</div>

<style>
    .hv-room table.rom_event_info{
        margin-bottom: 10px;
    }
    .hv-room table.rom_event_info,
    .hv-room table.rom_event_info td{
        border: none;
        background-color: #fff;
        text-align: left;
        padding: 5px 0;
    }
    .hv-room table.rom_event_info td:first-child{
        width: 100px;
        font-style: italic;
    }
    .hv-room .room_time{
        text-align: center;
    }
    .hv-room .room_time:not(.on){
        display: none;
    }
</style>

<?php
if($error){?>
    <script>
        $(function(){
            _Navigator.go('room');
        })
    </script><?php
}
?>

