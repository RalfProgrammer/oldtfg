<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_CHAT)){
    echo json_encode(array(
        'success' => false,
        'info'    => 'No tienes permisos'
    ));
    die();
}

require_once($CONFIG->dir . 'services/chat/Chat.php');
require_once($CONFIG->dir . 'services/chat/Message.php');
$to = get_param('u', 0 );

if(Chat::canChatWith($to)){
    $text    = stripslashes(rawurldecode(get_param('t', '')));

    $Message = new Message();
    $Message->setFrom   ($USER->id);
    $Message->setTo     ($to);
    $Message->setMessage($text);

    if($Message->save()){
        Chat::openChats($USER->id, $to);
        $Message = new Message($Message->getId());
        $Message->setMessage(nl2br($Message->getMessage()));
        echo json_encode(array(
            'success' => true,
            'info'    => $Message
        ));
    }else{
        echo json_encode(array(
            'success' => false,
            'info'    => 'error al guardar'
        ));
    }
}else{
    echo json_encode(array(
        'success' => false,
        'info'    => 'No puedes enviar mensajes a ese usuario'
    ));
}