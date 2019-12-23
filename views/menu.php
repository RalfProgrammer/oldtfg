<?php
if(!isset($main_menu))
    $main_menu = false;

if(!$main_menu){?>
    <article class="user">
        <img src="<?= $USER->getSrcAvatar()?>" class="avatar big">
        <div>
            <span class="name"><?= $USER->name?></span>
            <span class="lastname"><?= $USER->lastname?></span>
        </div>
    </article><?php
}?>


<ul>
    <?php
    if(Permission::can_view(PERMISSION_RECORD)){
        $name = '';
        switch($USER->rol){
            case 1 : $name = 'Mi historial';break;
            case 2 :
            case 3 :
            case 4 :
                $name = 'Historiales Medicos';break;
        }?>
        <li>
            <a name="record" href="?v=record">
                <span class="fa-stack fa-2x menu_icon">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-folder-open-o fa-stack-1x fa-inverse"></i>
                </span>
                <label><?= $name ?></label>
            </a>
        </li><?php
    }
    if(Permission::can_view(PERMISSION_CHAT)){?>
        <li>
            <a name="chats" href="?v=chats">
                <span class="fa-stack fa-2x menu_icon">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-comments-o fa-stack-1x fa-inverse"></i>
                </span>
                <label>Mensajeria</label>
            </a>
        </li><?php
    }
    if(Permission::can_view(PERMISSION_ROOM)){
        $name = '';
        switch($USER->rol){
            case 1 : $name = 'Sala de espera';break;
            case 2 : $name = 'Mi consulta';break;
            case 3 :
            case 4 :
                $name = 'Consulta Medica';break;
        }?>
        <li>
            <a name="room" href="?v=room">
                <span class="fa-stack fa-2x menu_icon">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-stethoscope fa-stack-1x fa-inverse"></i>
                </span>
                <label>Sala de espera</label>
            </a>
        </li><?php
    }
    if(Permission::can_view(PERMISSION_CALENDAR)){
        $name = '';
        switch($USER->rol){
            case ROL_USER : $name = 'Mis citas';break;
            case ROL_DOCTOR : $name = 'Mi agenda';break;
            case ROL_AUXILIAR :
            case ROL_ADMIN :
                $name = 'Gestión de citas';break;
        }?>
        <li>
            <a name="calendar" href="?v=calendar">
                <span class="fa-stack fa-2x menu_icon">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-calendar fa-stack-1x fa-inverse"></i>
                </span>
                <label><?= $name ?></label>
            </a>
        </li><?php
    }
    if(Permission::can_view(PERMISSION_STAFF)){?>
        <li>
            <a name="staff" href="?v=staff">
                <span class="fa-stack fa-2x menu_icon">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-user-md fa-stack-1x fa-inverse"></i>
                </span>
                <label>Gestión del Personal</label>
            </a>
        </li><?php
    }
    if(Permission::can_view(PERMISSION_PATIENT)){?>
        <li>
            <a name="patients" href="?v=patients">
                <span class="fa-stack fa-2x menu_icon">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-group fa-stack-1x fa-inverse"></i>
                </span>
                <label>Gestión de Pacientes</label>
            </a>
        </li><?php
    }
    if(Permission::can_view(PERMISSION_USERS)){?>
        <li>
            <a name="users" href="?v=users">
                <span class="fa-stack fa-2x menu_icon">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-h-square fa-stack-1x fa-inverse"></i>
                </span>
                <label>Usuarios</label>
            </a>
        </li><?php
    }
    if(Permission::can_view(PERMISSION_ROLES)){?>
        <li>
            <a name="roles" href="?v=roles">
                <span class="fa-stack fa-2x menu_icon">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-key fa-stack-1x fa-inverse"></i>
                </span>
                <label>Gestion de roles</label>
            </a>
        </li><?php
    }

    if(Permission::can_view(PERMISSION_PROFILE)){?>
        <li>
            <a name="profile" href="?v=profile">
                    <span class="fa-stack fa-2x menu_icon">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa fa-cog fa-stack-1x fa-inverse"></i>
                    </span>
                <label>Ajustes</label>
            </a>
        </li><?php
    }?>

    <li href="">
        <a href="core/logout.php" name="logout">
            <span class="fa-stack fa-2x menu_icon">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-power-off fa-stack-1x fa-inverse"></i>
            </span>
            <label>Salir</label>
        </a>
    </li>
</ul>