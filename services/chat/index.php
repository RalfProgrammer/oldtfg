<?php
if(!isset($CONFIG))
    require_once('../../config.php');

if(!Permission::can_view(PERMISSION_CHAT)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}

Log::create('chat');

$all_users = User::get_all();

function cmp($a, $b){
    return strcmp($a->name, $b->name);
}

usort($all_users, "cmp");

$patients = array_filter($all_users, create_function('$u','return $u->rol == ' . ROL_USER . ' && $u->id != ' . $USER->id . ';'));
$doctors  = array_filter($all_users, create_function('$u','return $u->rol == ' . ROL_DOCTOR . ' && $u->id != ' . $USER->id . ';'));
$personal = array_filter($all_users, create_function('$u','return $u->rol == ' . ROL_AUXILIAR . ' && $u->id != ' . $USER->id . ';'));
$admins   = array_filter($all_users, create_function('$u','return $u->rol == ' . ROL_ADMIN . ' && $u->id != ' . $USER->id . ';'));

?>
<div class="tabs hv-chat_index">
    <div class="tab" name="Conversaciones">
        <ul class="all_conversations"></ul>
    </div>
    <?php
    if(Permission::can(PERMISSION_CHAT, 2)){?>
        <div class="tab" name="Usuarios">
            <div class="box row">
                <h5 class="header_box">
                    <i class="fa fa-user"></i>Busca entre tus contactos
                </h5>
                <div class="col-xs-12 col-sm-8 col-md-10">
                    <input type="text" class="inp-search" placeholder="Busca usuario">
                </div>
                <div class="col-xs-12 col-sm-4 col-md-2">
                    <a class="btn btn-primary bt-search inp_height">Buscar</a>
                </div>
            </div>
            <div class="row chat_list_all"><?php
                if(count($patients) > 0){?>
                    <div class="col-xs-12 col-sm-6">
                        <div class="box">
                            <h5 class="header_box">
                                <i class="fa fa-users"></i>Pacientes
                            </h5>
                            <ul class="list_limit_height-xs" name="<?= ROL_USER ?>"><?php
                                foreach($patients as $user){?>
                                <li name="<?= $user->id ?>">
                                    <img class="avatar" src="<?= $user->other->avatar_src?>"><?= $user->other->fullname?>
                                    </li><?php
                                }?>
                            </ul>
                        </div>
                    </div><?php
                }
                if(count($doctors) > 0){?>
                    <div class="col-xs-12 col-sm-6">
                        <div class="box">
                            <h5 class="header_box">
                                <i class="fa fa-user-md"></i>Personal Sanitario
                            </h5>
                            <ul class="list_limit_height-xs" name="<?= ROL_DOCTOR?>"><?php
                                foreach($doctors as $user){?>
                                    <li name="<?= $user->id ?>">
                                        <img class="avatar" src="<?= $user->other->avatar_src?>"><?= $user->other->fullname?>
                                    </li><?php
                                }?>
                            </ul>
                        </div>
                    </div><?php
                }
                if(count($personal) > 0){?>
                    <div class="col-xs-12 col-sm-6">
                        <div class="box">
                            <h5 class="header_box">
                                <i class="fa fa-hospital-o"></i>Personal Administrativo
                            </h5>
                            <ul class="list_limit_height-xs" name="<?= ROL_AUXILIAR?>"><?php
                                foreach($personal as $user){?>
                                <li name="<?= $user->id ?>">
                                    <img class="avatar" src="<?= $user->other->avatar_src?>"><?= $user->other->fullname?>
                                    </li><?php
                                }?>
                            </ul>
                        </div>
                    </div><?php
                }
                if(count($admins) > 0){?>
                    <div class="col-xs-12 col-sm-6">
                        <div class="box">
                            <h5 class="header_box">
                                <i class="fa fa-laptop"></i>Administradores
                            </h5>
                            <ul class="list_limit_height-xs" name="<?= ROL_ADMIN?>"><?php
                                foreach($admins as $user){?>
                                    <li name="<?= $user->id ?>">
                                        <img class="avatar" src="<?= $user->other->avatar_src?>"><?= $user->other->fullname?>
                                    </li><?php
                                }?>
                            </ul>
                        </div>
                    </div><?php
                }?>
            </div>
        </div><?php
    }?>
</div>