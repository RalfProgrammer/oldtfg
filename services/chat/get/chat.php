<?php
if(!isset($CONFIG))
    require_once('../../../config.php');
//
//if(!Permission::can_view(PERMISSION_CHAT)){
//    echo json_encode(array(
//        'success' => false,
//        'info'    => 'No tienes permisos'
//    ));
//    die();
//}

require_once($CONFIG->dir . 'services/chat/Chat.php');

$user_id = get_param('u', 0 );

if(Chat::canChatWith($user_id)){
    $User = new User($user_id);
    $data = new StdClass();
    $data->your     = new StdClass();
    $data->your->name   = $USER->getFullName();
    $data->your->avatar = $USER->getSrcAvatar();
    $data->other    = new StdClass();
    $data->other->name   = $User->getFullName();
    $data->other->avatar = $User->getSrcAvatar();
    $data->messages = Message::getConversation($user_id);

    Chat::markAsRead($user_id);

    echo json_encode(array(
        'success' => true,
        'info'    => $data
    ));
}else{
    echo json_encode(array(
        'success' => false,
        'info'    => 'No tienes acceso a este usuario'
    ));
}