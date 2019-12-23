<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_edit(PERMISSION_PATIENT) && !Permission::can_edit(PERMISSION_STAFF)){
    echo_json(false, 'No tienes permisos');
}

require_once($CONFIG->dir . '/services/users/Relation.php');

$patient_id = get_param('p', false);
$doctor_id  = get_param('d', false);

$Patient = new User($patient_id);
$Doctor  = new User($doctor_id);

if($Patient->getRol() != ROL_USER)
    echo_json(false, 'Paciente incorrecto');

if($Doctor->getRol() != ROL_DOCTOR)
    echo_json(false, 'Profesional Sanitario incorrecto');

$Relation = new Relation();
$Relation->setPatient($patient);
$Relation->setDoctor($doctor);

if($Relation->save()){
    echo_json(true, 'Guardado');
}else{
    echo_json(false, 'Error al guardar');
}