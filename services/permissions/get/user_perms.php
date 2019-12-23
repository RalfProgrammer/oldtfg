<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_ROLES)){
    echo json_encode(array(
        "success" => false,
        "info"    => 'No tienes permisos'
    ));
    die();
}

$id = get_param("id", false);

if($User = new User($id)){
    echo json_encode(array(
        "success" => true,
        "info"    => array(
            "role"       => $User->getRol(),
            "permission" => $User->getPermissions(),
            "values"     => $User->loadPermissions(),
            "names"      => Permission::permNames()
        )
    ));
}else{
    echo json_encode(array(
        "success" => false,
        "info"    => 'El usuario no existe'
    ));
}
?>