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

$id     = get_param('id', 0);
$name   = stripslashes(rawurldecode(get_param('name', false)));
$rol    = get_param('rol', false);
$values = json_decode(get_param('values', false));

debugPHP($values, 'values');

if($id >= 1 && $id <= 4 && $id != $rol){
    echo json_encode(array(
        "success" => false,
        "info"    => 'No puedes cambiar el rol por defecto de rol'
    ));
    die();
}

$Permission = new Permission($id);
$Permission->setName($name);
$Permission->setRol($rol);
$Permission->setCreator($USER->id);
$Permission->setIndividual(0);
$Permission->setValues($values);

if($Permission->save()){
    echo json_encode(array(
        "success" => true,
        "info"    => $Permission
    ));
}else{
    echo json_encode(array(
        "success" => false,
        "info"    => 'Error al guardar'
    ));
}