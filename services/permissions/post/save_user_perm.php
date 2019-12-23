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

$u       = get_param('u', false);
$perm_id = get_param('id', false);
$rol     = get_param('rol', false);


if($User = new User($u)){
    if($perm_id == 'user'){
        $values   = json_decode(get_param('values', false));

        if(!($Permission = Permission::get_individual($User->getId()))){
            $Permission = new Permission();
        }

        $Permission->setName        ('user_' . $User->getId());
        $Permission->setCreator     ($USER->id);
        $Permission->setValues      ($values);
        $Permission->setRol         ($rol);
        $Permission->setIndividual  ($User->getId());

        if(!$Permission->save()){
            echo json_encode(array(
                'success' => false,
                'info'    => 'Error al guardar permiso'
            ));
            die();
        }
        $perm_id = $Permission->getId();
    }

    $User->setRol       ($rol);
    $User->setRol_perms ($perm_id);

    if($User->save()){
        $User->loadExtraInfo();
        echo json_encode(array(
            'success' => true,
            'info'    => $User
        ));
    }else{
        echo json_encode(array(
            'success' => false,
            'info'    => 'Error al actualizar el usuario'
        ));
    }
}else{
    echo json_encode(array(
        'success' => false,
        'info'    => 'El usuario no existe'
    ));
}