<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_edit(PERMISSION_PROFILE)){
    echo json_encode(array(
        'success' => false,
        'info'    => 'No tienes permisos para editar'
    ));
    die();
}

$action = get_param('a', false);
$data   = json_decode(get_param('data', false));

$error  = false;

switch($action){
    case 'email':
        $data->em_1 = stripslashes(rawurldecode($data->em_1));
        $data->em_2 = stripslashes(rawurldecode($data->em_2));

        if(!$data->em_1 || !$data->em_2){
            $error = 'Los parametros no son correctos';
        }
        if($data->em_1 != $data->em_2){
            $error = 'Los emails no coinciden';
        }
        if($data->em_1 == array_shift($USER->contact->email)){
            $error = 'El email es el actual';
        }
        if(!filter_var($data->em_1, FILTER_VALIDATE_EMAIL)){
            $error = 'El formato del email no es valido';
        }

        if(!$error){
            $Object = new StdClass();
            $Object->id       = $USER->id;
            $Object->contact        = $USER->contact;
            $Object->contact->email = array($data->em_1);
            $Object->contact = json_encode($Object->contact);
            if(db_update(USER_TABLE, $Object)){
                $USER->contact       = json_decode($Object->contact);
                $USER->other->emails = $data->em_1;
                session_start();
                $_SESSION['user'] = serialize($USER);
                session_write_close();
                echo json_encode(array(
                    'success' => true,
                    'info'    => 'Email cambiado correctamente'
                ));
                die();
            }else{
                $error = 'Se ha producido un error al guardar';
            }
        }
        break;
    case 'password':
        $data->pass_1     = stripslashes(rawurldecode($data->pass_1));
        $data->pass_2     = stripslashes(rawurldecode($data->pass_2));
        $data->pass_old   = stripslashes(rawurldecode($data->pass_old));

        if(!$data->pass_1 || !$data->pass_2 || !$data->pass_old){
            $error = 'Los parametros no son correctos';
        }
        if($data->pass_1 != $data->pass_2){
            $error = 'Las contraseñas no coinciden';
        }
        if(strlen($data->pass_1) < 6){
            $error = 'La contraseña tiene menos de 6 caracteres';
        }

        if(!$error){
            if(System::authenticate($USER->dni, $data->pass_old)){
                $Object = new StdClass();
                $Object->id        = $USER->id;
                $Object->password  = System::encrypt($data->pass_1);
                if(db_update(USER_TABLE, $Object)){
                    echo json_encode(array(
                        'success' => true,
                        'info'    => 'Contraseña cambiada correctamente'
                    ));
                    die();
                }else{
                    $error = 'Se ha producido un error al guardar';
                }
            }else{
                $error = 'La contraseña actual no es correcta';
            }
        }
        break;
    default :
        $error = 'La accion no existe';
}

echo json_encode(array(
    'success' => false,
    'info'    => $error
))
?>