<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

require_once($CONFIG->dir . 'services/chat/Message.php');

$messages = Message::getNoRead();
?>
<div class="box">
    <h5 class="header_box dots">
        <i class="fa fa-comments-o"></i><?= $messages->total?> Mensajes sin leer
    </h5>
    <ul class="list_messages"><?php
        $no_messages = true;
        foreach($messages->list as $message){
            $num_messages = count($message);
            $f_message    = array_shift($message);
            $User = new User($f_message->from);
            ?>
            <li name="<?= $User->getId()?>">
                <img src="<?= $User->getSrcAvatar()?>" class="avatar">
                <?= $User->getFullName() ?>
                <span class="n_new_messages on"><?= $num_messages ?></span>
                <span class="message_time hidden-xs">Hace <?= $f_message->other->time ?></span>
            </li>
            <?php
            $no_messages = false;
        }
        if($messages->total == 0){?>
            <li class="empty_list">- No tienes mensajes nuevos -</li><?php
        }?>
    </ul>
</div>

<style>
    .list_messages .n_new_messages {
        margin-top: 7px;
        margin-left: 5px;
    }
    .list_messages .message_time {
        display: inline-block;
    }
</style>