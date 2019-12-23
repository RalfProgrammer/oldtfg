<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_USERS)){
    echo json_encode(array(
        "success" => false,
        "info"    => 'No tienes permisos'
    ));
}

//User info
$id          = get_param('id', false);
$name        = stripslashes(rawurldecode(get_param('name', false)));
$lastname    = stripslashes(rawurldecode(get_param('lastname', false)));
$dni         = get_param('dni', false);
$sex         = get_param('sex', false);
$birthdate   = get_param('birthdate', false);
$address_dir = get_param('address_dir', '');
$address_ciu = get_param('address_ciu', '');
$address_cp  = get_param('address_cp', '');
$phone       = get_param('phone', false);
$email       = get_param('email', false);
$rol         = get_param('rol', false);
$perm        = get_param('perm', "0");
$blood       = get_param('blood', false);
$information = stripslashes(rawurldecode(get_param('information', false)));

$User = false;
$identifier = false;
switch($rol){
    case ROL_USER :
        $historic    = get_param('historic', false);
        $status      = get_param('status', '');
        $height      = get_param('height', false);
        $weight      = get_param('weight', false);

        $User       = new Patient($id);
        if(!$historic){
            $correct = false;
            $Patient_tst = new Patient();
            while(!$correct){
                $historic = rand(0, 9999999999);
                $correct = $Patient_tst->test_type_id($historic);
            }
        }
        $User->setHistoric  ($historic);
        $User->setStatus    ($status);
        $User->setHeight    ($height);
        $User->setWeight    ($weight);
        $User->setSip_id    ('denispfg2@sip2sip.info');
        $User->setSip_name  ('denispfg2');
        $identifier = $historic;
        break;
    case ROL_DOCTOR  :
    case ROL_AUXILIAR  :
        $staff_id    = get_param('staff_id', false);
        $branch      = get_param('branch', false);
        $horary      = get_param('horary', false);
        $room        = get_param('room', false);
        $office      = get_param('office', false);
        $h_phone     = get_param('h_phone', false);

        if(!$staff_id){
            $correct = false;
            $Staff_tst = new Staff();
            while(!$correct){
                $staff_id = rand(0, 9999999999);
                $correct = $Staff_tst->test_type_id($staff_id);
            }
        }

        $User       = new Staff($id);
        $User->setStaff_id  ($staff_id);
        $User->setBranch    ($branch);
        $User->setHorary    ($horary);
        $User->setRoom      ($room);
        $User->setOffice    ($office);
        $User->setH_phone   ($h_phone);
        $User->setSip_id    ('denispfg@sip2sip.info');
        $User->setSip_name  ('denispfg');
        $identifier = $staff_id;break;
    default :
        $User = new User($id);
        $identifier = false;
        $User->setSip_id    ('denispfg@sip2sip.info');
        $User->setSip_name  ('denispfg');
}

if(!$User->test_type_id($identifier, $id)){
    echo(json_encode(array(
        'success' => false,
        'info'    => 'El numero del identificador del usuario ya existe'
    )));
    die();
}

$User->setName          ($name);
$User->setLastname      ($lastname);
$User->setDni           ($dni);
$User->setSex           ($sex);
$User->setBirthdate     ($birthdate);

$contact_info = new StdClass();

$contact_info->address      = new StdClass();
$contact_info->address->dir  =  $address_dir;
$contact_info->address->ciu  =  $address_ciu;
$contact_info->address->cp   =  $address_cp;

$contact_info->email   = array();
if($email)
    array_push($contact_info->email, $email);

$contact_info->phone   = array();
if($phone)
    array_push($contact_info->phone, $phone);

$User->setContact       (json_encode($contact_info));

$User->setRol           ($rol);
$User->setRol_perms     ($perm);
$User->setBlood         ($blood);
$User->setInformation   ($information);

if($User->save()){
    $User->loadExtraInfo();
    switch($User->getRol()){
        case 1: $User->loadExtraPatientInfo();break;
        case 2:
        case 3: $User->loadExtraStaffInfo();break;
    }
    echo json_encode(array(
        'success' => true,
        'info'    => $User
    ));
}else{
    echo json_encode(array(
        'success' => false,
        'info'    => 'Se ha producido un error al guardar'
    ));
}