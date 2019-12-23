<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 22/03/14
 * Time: 13:26
 */

class System {

    const ENCRYPTION = 1;//1: sha1
    const SALT       = 'pfg_hv#2014';//1: sha1

    public static function login($dni, $password){
        if($User = self::authenticate($dni, $password)){
            return self::startUserSession($User);
        }
        return false;
    }

    public static function authenticate($dni, $password){
        if($User = User::get_by_dni($dni)){
            if($User->getPassword() == self::encrypt($password)){
                return $User;
            }
        }
        return false;
    }

    private function startUserSession($user){
        global $USER;
        $USER = $user;
        $USER->perms = false;
        Permission::loadUserPerms();
        session_start();
            if(Permission::can(PERMISSION_LOGIN) || Permission::is(ROL_ADMIN)){
                $_SESSION['user']            = serialize($USER);
                $_SESSION['REMOTE_ADDR']     = $_SERVER['REMOTE_ADDR'];
                $_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
                $_SESSION['last_action']     = strtotime('now');
            }else{
                $USER = false;
                session_unset();
                session_destroy();
            }

        session_regenerate_id(true);
        session_write_close();

        return (isset($USER->id) && $USER->id > 0) ? true : false;
    }

    public static function generateHash($length = 8, $encrypt = false){
        $hash  = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!¡?¿-_';
        for($i = 0; $i < $length; $i++)
            $hash .= $chars[mt_rand(0, 9999) % 68];
        return $encrypt ? self::encrypt($hash) : $hash;
    }

    public static function encrypt($pass){
        $encrypted = false;
        switch(self::ENCRYPTION){
            case 1: $encrypted = sha1($pass . self::SALT);break;
        }
        return $encrypted;
    }
} 