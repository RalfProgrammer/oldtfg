<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_CHAT)){
    echo 'No tienes permisos';
    die();
}

require_once ($CONFIG->dir . 'services/chat/Chat.php');

$chats = Chat::getChats();

echo json_encode(array(
    'success' => true,
    'info'    => $chats
));