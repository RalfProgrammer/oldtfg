<?php

if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_RECORD)){
    echo_error_view();
}

require_once($CONFIG->dir . 'services/room/Report.php');
require_once($CONFIG->dir . 'services/calendar/Event.php');

if(!isset($from_room))
    $from_room = false;

$can_edit = false;
if($USER->rol == ROL_USER){
    $user_id = $USER->id;
}else{
    if(!isset($user_id) || !$user_id)
        $user_id = get_param('u', false);
}

$can_edit = Permission::can_edit(PERMISSION_RECORD);

Log::create('records', 'view', $user_id);

$User = new Patient($user_id);

if(!$from_room){?>
    <div class="tabs"><?php
}
    if($User->getId() && $User->getRol() == 1){
        if(!$from_room){?>
            <div class="tab" name="Historial Médico"><?php
        }?>
            <div class="hv-record row">
                <?php
                require($CONFIG->dir . '/services/records/view/warnings.php');
                ?>
                <div class="col-xs-12" style="padding: 0">
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="box record-basic_info">
                                <h5 class="header_box"><i class="fa fa-user"></i>Paciente</h5>
                                <div>
                                    <table class="user_info">
                                        <tr>
                                            <td>Nombre:</td>
                                            <td><?= $User->getName()?></td>
                                        </tr>
                                        <tr>
                                            <td>Apellidos:</td>
                                            <td><?= $User->getLastname()?></td>
                                        </tr>
                                        <tr>
                                            <td>NºHistorial:</td>
                                            <td><?= $User->getHistoric()?></td>
                                        </tr>
                                        <tr>
                                            <td>DNI:</td>
                                            <td><?= $User->getDni()?></td>
                                        </tr>
                                        <tr>
                                            <td>F.Nacimiento:</td>
                                            <td><?= $User->getBirthdate()?> ( <?= $User->other->years ?> años)</td>
                                        </tr>
                                        <tr>
                                            <td>Sexo:</td>
                                            <td><?= ($User->sex == 'male')? 'Masculino' : 'Femenino'?></td>
                                        </tr>
                                        <tr>
                                            <td>Altura:</td>
                                            <td><?= $User->getHeight()?> cm</td>
                                        </tr>
                                        <tr>
                                            <td>Peso:</td>
                                            <td><?= $User->getWeight()?> kg</td>
                                        </tr>
                                        <tr>
                                            <td>G.Sanguineo:</td>
                                            <td><?= $User->getBlood() ?></td>
                                        </tr>
                                    </table>
                                    <?php
                                    if($can_edit){?>
                                        <div class="row">
                                            <div class="col-xs-4"><?php
                                                if(Permission::can_edit(PERMISSION_CALENDAR)){?>
                                                    <a class="btn btn-default bt-create-event"><i class="fa fa-calendar"></i>Cita</a><?php
                                                }?>
                                            </div>
                                            <div class="col-xs-4"><?php
                                                if(Permission::can(PERMISSION_CHAT, 2)){?>
                                                    <a class="btn btn-default bt-send-message"><i class="fa fa-comments-o"></i>Chat</a><?php
                                                }?>
                                            </div>
                                            <div class="col-xs-4">
                                                <a class="btn btn-default bt-open-alerts"><i class="fa fa-warning"></i>Alertas</a>
                                            </div>
                                        </div><?php
                                    }?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-8">
                            <div class="box record-reports" name="alerts">
                                <h5 class="header_box"><i class="fa fa-file-text-o"></i>Informe evolutivo</h5>
                                <div class="flip_content">
                                    <div class="historic_report"><?php
                                        echo Report::getCompleteReport($User->getId());?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="box flip" name="analytics">
                        <div class="flip_f">
                            <h5 class="header_box"><i class="fa fa-flask"></i>Analiticas<?php if($can_edit){?><label class="header_action"><i class="fa fa-pencil"></i>Editar</label><?php }?></h5>
                            <div class="flip_content">
                                <?php
                                $user_id = $User->getId();
                                require($CONFIG->dir . 'services/records/view/analytics.php'); ?>
                            </div>
                        </div>
                        <div class="flip_b">
                            <h5 class="header_box"><i class="fa fa-flask"></i>Edición de Analiticas<label class="header_action"><i class="fa fa-arrow-left"></i>Volver</label></h5>
                            <div class="flip_content">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12" style="padding: 0">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="box flip" name="medicines">
                                <div class="flip_f">
                                    <h5 class="header_box"><i class="fa fa-medkit"></i>Tratamientos<?php if($can_edit){?><label class="header_action"><i class="fa fa-pencil"></i>Editar</label><?php }?></h5>
                                    <div class="flip_content"><?php
                                        $user_id = $User->getId();
                                        require $CONFIG->dir . 'services/medicine/view/user_medicine.php';
                                        ?>
                                    </div>
                                </div>
                                <div class="flip_b">
                                    <h5 class="header_box">
                                        <span class="header_title dots"><i class="fa fa-medkit"></i>Edición de Tratamientos</span>
                                        <label class="header_action"><i class="fa fa-arrow-left"></i>Volver</label>
                                    </h5>
                                    <div class="flip_content">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="box flip" name="protocols">
                                <div class="flip_f">
                                    <h5 class="header_box"><i class="fa fa-legal"></i>Protocolos <?php if($can_edit){?><label class="header_action"><i class="fa fa-pencil"></i>Editar</label><?php }?></h5>
                                    <div class="flip_content">
                                        <?php
                                        $user_id = $User->getId();
                                        require($CONFIG->dir . 'services/records/view/protocols.php'); ?>
                                    </div>
                                </div>
                                <div class="flip_b">
                                    <h5 class="header_box">
                                        <span class="header_title dots"><i class="fa fa-legal"></i>Edición de Protocolos</span>
                                        <label class="header_action"><i class="fa fa-arrow-left"></i>Volver</label>
                                    </h5>
                                    <div class="flip_content">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><?php
        if(!$from_room){?>
            </div><?php
        }
    }else{

    }

if(!$from_room){?>
    </div><?php
}?>