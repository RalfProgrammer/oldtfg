<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_edit(PERMISSION_ROLES)){
    echo json_encode(array(
        "success" => false,
        "info"    => 'No tienes permisos'
    ));
    die();
}

$perm_id = get_param('id', false);

if($perm_id < 5){
    echo json_encode(array(
        "success" => false,
        "info"    => 'Los permisos por defecto no se pueden borrar'
    ));
    die();
}

if($Permission = new Permission($perm_id)){
    if($Permission->delete()){
        echo json_encode(array(
            "success" => true,
            "info"    => $perm_id
        ));
    }else{
        echo json_encode(array(
            "success" => false,
            "info"    => 'Error borrando'
        ));
    }
}else{
    echo json_encode(array(
        "success" => false,
        "info"    => 'Permiso inexistente'
    ));
}