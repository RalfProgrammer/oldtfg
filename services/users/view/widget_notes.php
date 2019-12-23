<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

require_once($CONFIG->dir . 'services/note/Note.php');

$notes = Note::getByEventAndUser(0, false);
?>
<div class="box">
    <h5 class="header_box dots">
        <i class="fa fa-comments-o"></i><?= count($notes) ?> Notas guardadas <label class="header_action"><i class="fa fa-plus"></i>Añadir</label>
    </h5>
    <ul class="list_messages"><?php
        $no_messages = true;
        foreach($notes as $note){?>
            <li name="<?= $note->id?>">
                <span class="dots"><?= $note->text ?></span>
            </li><?php
        }
        if(count($notes) == 0){?>
            <li class="empty_list">- No tienes -</li><?php
        }?>
    </ul>
</div>