<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_CHAT)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}

require_once($CONFIG->dir . 'services/chat/Chat.php');

$user_id  = get_param('u', false);

if(Chat::canChatWith($user_id)){
    Log::create('chat', 'view', $user_id);

    $User = new User($user_id);?>
    <div class="tabs hv-chat_individual">
        <div class="tab" name="<?= $User->getFullName()?>">
            <ul class="messages"></ul>
        </div>
    </div>
    <div class="chat_writter">
        <div class="col-xs-8 col-sm-10 col-lg-11">
            <textarea class="chat_text"></textarea>
        </div>
        <div class="col-xs-4 col-sm-2 col-lg-1">
            <a class="btn btn-primary bt-send">Enviar</a>
        </div>

    </div><?php
}else{?>
    No tienes permisos <?php
}?>