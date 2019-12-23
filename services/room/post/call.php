<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_ROOM)){
    echo json_encode(array(
        "success" => false,
        "info"    => 'No tienes permisos'
    ));
    die();
}

require_once($CONFIG->dir . 'services/room/Report.php');
require_once($CONFIG->dir . 'services/room/Call.php');
require_once($CONFIG->dir . 'services/calendar/Event.php');

$event  = get_param('e', false);
$action = get_param('a', 'call');

switch($action){
    case 'call':
        $Event = new Event($event);
        if($USER->rol == ROL_DOCTOR){
            $Report = Report::byEvent($event);
            if($Report->getId()){
                if($Report->getEnd() || $Report->getAbsence()){
                    echo_json(false, 'El evento esta finalizado');
                }
                $Patient = new Patient($Event->getUser());

                $Call = new Call();
                $Call->setCaller    ($USER->id);
                $Call->setReceptor  ($Patient->getId());
                $Call->setEvent     ($event);

                if($Call->save()){
                    echo json_encode(array(
                        'success' => true,
                        'info'    => array(
                            'call_id'   => $Call->getId(),
                            'call_name' => $Patient->getFullName(),
                            'sip_id'    => $Patient->getSip_id()
                        )
                    ));
                    die();
                }else{
                    echo_json(false, 'Error al iniciar la llamada');
                }
            }else{
                echo_json(false, 'No existe informe');
            }
        }else{
            echo_json(false, 'No puedes iniciar la llamda');
        }

        break;
    case 'stop': break;
}

echo_json(false, 'Datos no validos');