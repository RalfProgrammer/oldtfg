<?php
/**
 * User: Denis
 * Date: 6/04/14
 */

if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_edit(PERMISSION_CALENDAR)){
    echo json_encode(array(
        'success' => true,
        'info'    => 'No tienes permisos'
    ));
    die();
}

require_once($CONFIG->dir . 'services/calendar/Event.php');
require_once($CONFIG->dir . 'services/users/Relation.php');

$patient  = get_param('patient' , false);
$doctor   = get_param('doctor'  , false);
$date     = get_param('date'    , false);
$type     = get_param('type'    , false);
$request  = get_param('request' , false);
$location = get_param('location', false);

$start    = date("Y-m-d H:i:s", $date);
$end      = date("Y-m-d H:i:s", strtotime('+30 minute', strtotime($start)));

switch($USER->rol){
    case ROL_USER:
        if($patient != $USER->id){
            echo_json(false, 'Solo puedes pedir citas para ti');
        }
        if(!$doctor){
            echo_json(false, 'El profesional sanitario no existe');
        }

        $doctors = Relation::getDoctors($patient, true);
        if(!in_array($doctor, $doctors)){
            echo_json(false, 'El profesional medico no lo tiene asignado');
        }

        break;
    case ROL_DOCTOR:
        if($doctor != $USER->id){
            echo_json(false, 'Solo puedes pedir citas para ti');
        }
        if(!$patient){
            echo_json(false, 'Paciente no existe');
        }

        $patients = Relation::getPatients($doctor, true);
        if(!in_array($patient, $patients)){
            echo_json(false, 'El paciente no lo tiene asignado');
        }
        break;
    case ROL_AUXILIAR :
    case ROL_ADMIN :
        if(!($Patient = new Patient($patient))){
            echo_json(false, 'El paciente no existe');
        }
        if(!($Doctor = new Staff($doctor))){
            echo_json(false, 'El profesional sanitario no existe');
        }
        break;
}

if(!Event::testDate($start, $patient, $doctor)){
    echo_json(false, 'Esa fecha no esta disponible');
}

$Event = new Event();
$Event->setUser     ($patient);
$Event->setDoctor   ($doctor);
$Event->setStart    ($start);
$Event->setEnd      ($end);
$Event->setRequest  ($request);
$Event->setOnline   ($type);

if(!$location){
    $Doctor = new Staff($doctor);
    $location = $Doctor->getRoom();
}
$Event->setLocation ($location);

if($Event->save()){
    $time = new stdClass();
    $time->date = $Event->getDateCalendar();
    $time->time = $Event->getTimeCalendar();
    $Event->dateCalendar = $time;
    $Event->loadExtraInfo();

    echo json_encode(array(
        "success" => true,
        "info"    => $Event
    ));
}else {
    echo json_encode(array(
        "success" => false,
        "info"    => 'error al guardar'
    ));
}
