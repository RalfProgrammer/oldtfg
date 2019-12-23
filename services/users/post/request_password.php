<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

$value = get_param('t', false);

if($User = User::get_by_dni($value)){
    $User->sendEmail();
}

echo json_encode(array(
    'success' => true,
    'info'    => 'Si el email o DNI es correcto recibiras un email con los pasos a seguir para crear tu nueva contraseña'
));