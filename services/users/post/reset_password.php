<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

$user_id = get_param('u', false);

if(!Permission::can_view(PERMISSION_USERS) && $user_id != $USER->id){
    echo json_encode(array(
        "success" => false,
        "info"    => 'No tienes permisos'
    ));
}

$User = new User($user_id);
if($User->getId()){
    $new_pass = System::generateHash(8, true);
    $User->setPassword($new_pass);
    if($User->save()){
        echo json_encode(array(
            "success" => true,
            "info"    => 'Generada'
        ));
    }else{
        echo json_encode(array(
            "success" => false,
            "info"    => 'Se ha producido un error al generar la contrasena'
        ));
    }
}else{
    echo json_encode(array(
        "success" => false,
        "info"    => 'No existe el usuario'
    ));
}