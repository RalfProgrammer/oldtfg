<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_RECORD)){
    echo json_encode(array(
        'success' => false,
        'info'    => 'no tienes permiso'
    ));
    die();
}

require_once($CONFIG->dir . 'services/records/Warning.php');

$text    = stripslashes(rawurldecode(get_param('text', '')));
$scope   = get_param('scope', '');
$patient = get_param('patient', 0);

$Warning = new Warning();
$Warning->setCreator ($USER->id);
$Warning->setText    ($text);
$Warning->setScope   ($scope);
$Warning->setPatient ($patient);

if($Warning->save()){
    $Warning_read = new Warning_read();
    $Warning_read->setUser    ($USER->id);
    $Warning_read->setWarning ($Warning->getId());
    $Warning_read->save();

    $Warning->other = new StdClass();
    $Warning->other->read = date('Y-m-d H;i:s');
    echo json_encode(array(
        'success' => true,
        'info'    => $Warning
    ));
}else{
    echo json_encode(
        array(
            'success' => false,
            'info'    => 'error al guardar'
        ));
}