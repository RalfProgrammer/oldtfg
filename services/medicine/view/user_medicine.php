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

if(!isset($filter))
    $filter = false;

require_once($CONFIG->dir . 'services/medicine/Medicine.php');

$medicines = Medicine::getByUser($user_id);

if($filter){
    $now = strtotime('now');
    switch($filter){
        case 'actual':
            $medicines  = array_filter($medicines, create_function('$u','return $u->stop_user <= 0;'));
            break;
        case 'old':
            $medicines  = array_filter($medicines, create_function('$u','return $u->stop_user > 0;'));
            break;
    }
}
?>

<div class="row patients_list_header">
    <div class="col-xs-7 col-sm-4 col-lg-3"><h5>Nombre</h5></div>
    <div class="col-xs-5 col-sm-2 col-lg-2"><h5>Inicio</h5></div>
    <div class="hidden-xs col-sm-4 col-lg-4"><h5>Estado</h5></div>
    <div class="hidden-xs col-sm-2 col-lg-3"><h5>Dosis</h5></div>
</div>
<ul class="medicines_list"><?php
    foreach($medicines as $medicine){?>
        <li name="<?= $medicine->id ?>">
            <div class="row">
                <div class="col-xs-7 col-sm-4 col-lg-3 dots"><?= $medicine->other->name ?></div>
                <div class="col-xs-5 col-sm-2 col-lg-2 dots"><?= $medicine->start ?></div>
                <div class="hidden-xs col-sm-4 col-lg-4 dots"><?= $medicine->other->status ?></div>
                <div class="hidden-xs col-sm-2 col-lg-3 dots"><?= $medicine->other->interval_text ?></div>
            </div>
        </li><?php
    }?>
</ul>