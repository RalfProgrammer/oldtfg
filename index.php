<?php
if(!isset($CONFIG))
    require_once('config.php');

$go_to       = get_param("v", false);
$login_error = false;

if(!is_logged()){
    $dni  = get_param('dni', false);
    $pass = get_param('password', false);

    if($dni && $pass){
        if(!System::login($dni, $pass)){
            $login_error = 'Usuario o contraseña incorrectos';
        }
    }
}


require_once($CONFIG->dir . 'core/header.php');

if(!is_logged()){
    $go_to = false;
    require_once($CONFIG->dir . 'views/login.php');
}else{
    if(!$go_to || $go_to == 'main')
        Log::create('main');
    require_once($CONFIG->dir . 'views/main.php');
}

require_once($CONFIG->dir . 'core/footer.php');
?>
