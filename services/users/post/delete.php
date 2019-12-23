<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_USERS)){
    echo_json(false, 'No tienes permisos');
}

require_once "$CONFIG->dir/services/users/Relation.php";

$id = get_param('id', false);

$User = new User($id);

if($User->getId()){
    if($id == $USER->id){
        echo json_encode(array(
            'success' => false,
            'info'    => 'No te puedes borrar a ti mismo'
        ));
        die();
    }

    $User->setDeleted(1);
    if($User->save()){
        Relation::deleteUserRelations($User->getId());
        echo json_encode(array(
            'success' => true,
            'info'    => $id
        ));
    }else{
        echo json_encode(array(
            'success' => false,
            'info'    => 'Se ha producido un error al guardar'
        ));
    }
}else{
    echo json_encode(array(
        'success' => false,
        'info'    => 'El usuario no existe'
    ));
}