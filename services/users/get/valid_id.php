<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_USERS)){
    echo_json(false, 'No tienes permisos');
}

$id   =  get_param('id', '');
$rol  = get_param('r', 0);
$user = get_param('u', false);

if($id != ''){
    switch($rol){
        case ROL_USER:
            $Patient = new Patient();
            if(!$Patient->test_type_id($id, $user))
                echo_json(false, 'existe');
            break;
        case ROL_DOCTOR:
        case ROL_AUXILIAR:
            $Staff = new Staff();
            if(!$Staff->test_type_id($id, $user))
                echo_json(false, 'existe');
            break;
    }
}
echo_json(true, $id);
