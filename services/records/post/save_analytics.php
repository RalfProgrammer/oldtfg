<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_RECORD) || $USER->rol == 1){
    echo json_encode(array(
        'success' => false,
        'info'    => 'No puedes editarlo'
    ));
    die();
}
require_once($CONFIG->dir . 'services/records/Analytic.php');

$data = json_decode(get_param('data', false));

$error   = "";
$new_ids = array();

foreach($data as $values){
    $Analytic = new Analytic($values->id);
    $Analytic->setUser      ($values->u);
    $Analytic->setCreator   ($USER->id);
    $Analytic->setType      ($values->type);
    $results = json_decode($values->result);
    $Analytic->setDate      ($results->date);
    unset($results->date);
    $Analytic->setResult    ($results);

    $is_new = !$Analytic->getId();
    if(!$Analytic->save()){
        $error .= '"' . $Analytic->getDate() . '", ';
    }else{
        if($is_new)
            array_push($new_ids, $Analytic->getId());
    }
}

if($error == ""){
    echo json_encode(array(
        'success' => true,
        'info'    => $new_ids
    ));
}else{
    echo json_encode(array(
        'success' => true,
        'info'    => 'Error al guardar: ' . $error
    ));
}