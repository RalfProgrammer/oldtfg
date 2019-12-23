<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_RECORD)){
    echo_error_view();
}

require_once($CONFIG->dir . 'services/records/Protocol.php');

if($USER->rol == ROL_USER){
    $user_id = $USER->id;
}else{
    if(!isset($user_id)){
        $user_id = get_param('u', 0);
    }
}

require_once($CONFIG->dir . 'services/medicine/Medicine.php');

$medicines = Medicine::getByUser($user_id);
?>

<div class="row patients_list_header">
    <div class="col-xs-7 col-sm-4 col-lg-3"><h5>Nombre</h5></div>
    <div class="col-xs-5 col-sm-2 col-lg-3"><h5>Inicio</h5></div>
    <div class="hidden-xs col-sm-4 col-lg-3"><h5>Estado</h5></div>
    <div class="hidden-xs col-sm-2 col-lg-3"><h5>Dosis</h5></div>
</div>
<ul class="medicines_list"><?php
    foreach($medicines as $medicine){?>
        <li name="<?= $medicine->id ?>">
            <div class="row info_medicine">
                <div class="col-xs-7 col-sm-4 col-lg-3 dots"><?= $medicine->other->name ?></div>
                <div class="col-xs-5 col-sm-2 col-lg-2 dots"><?= $medicine->start ?></div>
                <div class="hidden-xs col-sm-4 col-lg-4 dots"><?= $medicine->other->status ?></div>
                <div class="hidden-xs col-sm-2 col-lg-3 dots"><?= $medicine->other->interval_text ?></div>
            </div>
            <div class="item_actions">
                <?php
                if(!$medicine->other->finished){?>
                    <i title="Parar" alt="Parar" class="fa fa-stop"></i><?php
                }?>
            </div>
        </li><?php
    }?>
</ul>
<div class="list_actions empty_list row add_medication">
    <h5>Añadir nuevo tratamiento:</h5>
    <div class="col-xs-12 col-sm-8">
        <input type="text" placeholder="Busca tratamiento" class="med_input">
        <span class="med_id" name=""><i class="fa fa-times"></i><label class="med_name"></label></span>
    </div>
    <div class="col-xs-12 col-sm-4">
        <input type="text" placeholder="Inicio" class="med_date">
    </div>
    <div class="col-xs-4">
        <input type="number" placeholder="Dosis" class="med_dosis">
    </div>
    <div class="col-xs-4">
        <input type="number" placeholder="Cada" class="med_cycle_num">
    </div>
    <div class="col-xs-4">
        <select class="med_cycle_type">
            <option value="h">Hora/s</option>
            <option value="d">Dia/s</option>
            <option value="w">Semana/s</option>
            <option value="m">Mes/es</option>
            <option value="y">Año/s</option>
        </select>
    </div>
    <div class="col-xs-12 col-sm-6 col-sm-offset-6 col-md-3 col-md-offset-9">
        <a class="btn btn-primary bt-add">Añadir</a>
    </div>
</div>

<style>
    .add_medication{
        margin-top: 10px !important;
    }
    .add_medication > h5{
        text-align: left;
        padding-left: 5px;
    }
    .add_medication .med_id {
        display: none;
    }
    .add_medication .med_id i{
        font-size: 18px;
        margin-right: 5px;
        color: #666;
    }
    .add_medication.active .med_id {
        display: block;
        text-align: left;
        line-height: 48px;
        padding: 0 5px;
        border: 1px solid #d8d8d8;
    }
    .add_medication .med_name{
        display: inline-block;
    }
    .add_medication.active .med_input {
        display: none;
    }
</style>