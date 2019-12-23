<?php

if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_RECORD)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}
require_once($CONFIG->dir . 'services/records/Protocol.php');

if($USER->rol == 1){
    $user_id = $USER->id;
}else{
    if(!isset($user_id)){
        $user_id = get_param('u', 0);
    }
}
?>

<div class="row list_header">
    <div class="col-xs-8 col-sm-6"><h5>Nombre</h5></div>
    <div class="col-xs-4 hidden-sm hidden-md hidden-lg"><h5>Estado</h5></div>
    <div class="hidden-xs col-sm-3"><h5>Inicio</h5></div>
    <div class="hidden-xs col-sm-3"><h5>Fin</h5></div>
</div><?php
$protocols = Protocol::getByUser($user_id);?>
<ul class="protocol_list"><?php
    foreach($protocols as $protocol){?>
        <li name="<?= $protocol->id ?>">
            <div class="row info_protocol">
                <div class="col-xs-8 col-sm-6 dots">
                    <input type="text" name="name" value="<?= $protocol->name ?>" data-saved="<?= $protocol->name ?>" placeholder="<?= $protocol->name ?>">
                </div>
                <div class="hidden-xs col-sm-3 dots">
                    <input type="text" name="start" class="date" value="<?= $protocol->start ?>" data-saved="<?= $protocol->start ?>" placeholder="<?= $protocol->start ?>">
                </div>
                <div class="hidden-xs col-sm-3 dots">
                    <input type="text" name="end" class="date" value="<?= $protocol->end ?>" data-saved="<?= $protocol->end ?>" placeholder="<?= $protocol->end ?>">
                </div>
            </div>
            <div class="item_actions">
                <i class="fa fa-trash-o delete"></i>
            </div>
        </li><?php
    }?>
</ul>
<div class="list_actions empty_list row">
    <div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-4 col-lg-2 col-lg-offset-8">
        <a class="btn btn-default btn-sm bt-add">Añadir Nuevo</a>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2">
        <a class="btn btn-primary btn-sm bt-save">Guardar</a>
    </div>
</div>