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

$user_id     = get_param('u', false);

$all_perms   = Permission::get_all();
$user_own    = false;

if($user_id){
    if($User = new User($user_id)){
        $user_rol  = $User->getRol();
        $user_perm = $User->getRol_perms();
        $user_own = array_filter($all_perms, create_function('$p', 'return $p->individual == ' . $User->getId(). ';'));
        $user_own  = (count($user_own) > 0) ? array_shift($user_own)->id : false;
    }
}

echo json_encode(array(
    "success" => true,
    "info"    => array(
        "list"  => $all_perms,
        'user'  => array(
            'rol'       => (isset($User)) ? $User->getRol() : 0,
            'rol_perms' => (isset($User)) ? $User->getRol_perms() : 0,
            'perm_own'  => (isset($User)) ? $user_own : 0
        ),
        'can_edit'  => Permission::can_edit(PERMISSION_ROLES)
    )
));
