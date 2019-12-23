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
?>

<div class="row list_header">
    <div class="col-xs-7 col-sm-6"><h5>Nombre</h5></div>
    <div class="col-xs-5 hidden-sm hidden-md hidden-lg"><h5>Estado</h5></div>
    <div class="hidden-xs col-sm-3"><h5>Inicio</h5></div>
    <div class="hidden-xs col-sm-3"><h5>Fin</h5></div>
</div><?php
$protocols = Protocol::getByUser($user_id);?>
<ul class="protocol_list"><?php
    foreach($protocols as $protocol){?>
        <li>
            <div class="row">
                <div class="col-xs-7 col-sm-6 dots"><?= $protocol->name ?></div>
                <div class="col-xs-5 hidden-sm hidden-md hidden-lg dots"><?= $protocol->other->status ?></div>
                <div class="hidden-xs col-sm-3 dots"><?= $protocol->start ?></div>
                <div class="hidden-xs col-sm-3 dots"><?= $protocol->end ?></div>
            </div>
        </li><?php
    }
    if(count($protocols) == 0){?>
        <li class="empty_list">- No tiene ninguno -</li><?php
    }?>
</ul>