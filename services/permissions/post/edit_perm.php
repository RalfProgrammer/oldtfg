<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_ROLES)){
    echo json_encode(array(
        "success" => false,
        "info"    => 'No tienes permisos'
    ));
}

$perm_id = get_param('id', false);
$name    = stripslashes(rawurldecode(get_param('name', false)));
$allow   = get_param('allow', false);

if($Permission = new Permission($perm_id)){
    $Permission->setName($name);
    $Permission->setAllow(json_encode($allow));
    if($Permission->save()){
        echo json_encode(array(
            "success" => true,
            "info"    => $Permission
        ));
    }else{
        echo json_encode(array(
            "success" => false,
            "info"    => 'Error guardando'
        ));
    }
}else{
    echo json_encode(array(
        "success" => false,
        "info"    => 'Permiso inexistente'
    ));
}