<?php
require_once($CONFIG->dir . 'services/users/Relation.php');

$patients = Relation::getPatients();
usort($patients, create_function('$a, $b', 'return ($a->lastname < $b->lastname) ? -1 : 1;'));
?>
<div class="hv-summary_doctor row">
    <input type="hidden" name="summary_type" value="doctor">
    <div class="col-xs-12 col-sm-12">
        <div class="box">
            <h5 class="header_box">
                <i class="fa fa-users"></i>Tus pacientes ( <?= count($patients)?> en total )
            </h5>
            <input type="text" class="search-patients" placeholder="Busca pacientes por nombre, historial o dni">
            <div class="row patients_list_header">
                <div class="col-xs-12 col-sm-6"><h5>Apellidos, Nombre</h5></div>
                <div class="hidden-xs col-sm-3"><h5>Nº Historial</h5></div>
                <div class="hidden-xs col-sm-3"><h5>Dni</h5></div>
            </div>
            <ul class="search-results"><?php
                if(count($patients)){
                    foreach($patients as $patient){?>
                        <li class="patient_item" name="<?= $patient->id ?>">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <img src="<?= $patient->getSrcAvatar()?>" class="avatar">
                                    <?= $patient->lastname . ', ' . $patient->name ?>
                                </div>
                                <div class="hidden-xs col-sm-3">
                                    <?= $patient->historic ?>
                                </div>
                                <div class="hidden-xs col-sm-3">
                                    <?= $patient->dni ?>
                                </div>
                            </div>
                        </li><?php
                    }
                }else{?>
                    <li class="empty_list">- No tienes pacientes -</li><?php
                }?>
            </ul>
        </div>
    </div>
    <div class="col-xs-12 col-md-6"><?php
        include $CONFIG->dir . 'services/chat/view/widget_messages.php'; ?>
    </div>
    <div class="col-xs-12 col-md-6"><?php
        include $CONFIG->dir . 'services/calendar/view/widget_calendar.php'; ?>
    </div>
</div>

<style>
    .hv-summary_doctor ul {
        max-height: 180px;
        overflow: auto;
        margin-bottom: 0;
    }
    .hv-summary_doctor ul.search-results{
        min-height: 180px;
    }
    .hv-summary_doctor ul.search-results li{
        line-height: 30px;
    }
    .hv-summary_doctor .item_event_min span.ev_m_when {
        font-weight: bold;
    }
    .hv-summary_doctor .item_event_min i {
        cursor: default;
    }
</style>