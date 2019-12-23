<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

require_once($CONFIG->dir . 'services/note/Note.php');

$id         = get_param('id', 0);
$text       = stripslashes(rawurldecode(get_param('text', false)));
$event      = get_param('event', false);
$visible    = get_param('visible', false);

$Note = new Note($id);
if($Note->getId() && $Note->getUser() != $USER->id){
    echo json_encode(array(
        'success' => false,
        'info'    => 'No puedes editar esta nota'
    ));
    die();
}

$Note->setText      ($text);
$Note->setUser      ($USER->id);
$Note->setEvent     (($event ? $event : 0));
$Note->setVisible   (($visible ? 1 : 0));

if($Note->save()){
    echo json_encode(array(
        'success' => true,
        'info'    => $Note
    ));
}else{
    echo json_encode(array(
        'success' => false,
        'info'    => 'Error al guardar'
    ));
}