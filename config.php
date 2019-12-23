<?php
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);

unset($CONFIG);
$CONFIG = new stdClass();

$CONFIG->db_dir     = 'localhost';
$CONFIG->db_name    = 'hospital_virtual';
$CONFIG->db_user    = 'root';
$CONFIG->db_pass    = '';
$CONFIG->base_dir   = '/pfg/index.php';
$CONFIG->dir        = 'C:/xampp/htdocs/pfg/';
$CONFIG->www        = 'http://192.168.1.11/pfg/';'http://10.0.34.54/pfg/';//'http://localhost/pfg/';
$CONFIG->debug_mode = true;

//date_default_timezone_set('Europe/London');

require_once($CONFIG->dir . 'core/lib.php');
require_once($CONFIG->dir . 'core/db.php');
require_once($CONFIG->dir . 'core/classes/System.php');

if($CONFIG->debug_mode){

    function debug(){
        ini_set ('display_errors', 'on');
        ini_set ('log_errors', 'on');
        ini_set ('display_startup_errors', 'on');
        ini_set ('error_reporting', E_ALL);
    }

    ob_start();
    include_once("$CONFIG->dir/external/firePHP/fb.php");
    function debugPHP($obj = '-#debug#-', $text = '-#debug#-'){
        if($obj != '-#debug#-' && $text != '-#debug#-'){
            FB::log($obj, $text);
        }else{
            FB::log($obj);
        }
    }
}

session_start();

$USER = false;

if(isset($_SESSION['user'])){
    $USER = unserialize($_SESSION['user']);
    if(isset($USER->id) && $USER->id > 0){
        Permission::loadUserPerms();
        if($_SESSION['REMOTE_ADDR'] != $_SERVER['REMOTE_ADDR'] ||
            $_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']){
            session_destroy();
            header('Location: index.php');
            die();
        }
    }
    $now = strtotime('now');
    if(($_SESSION['last_action'] + 300)  < $now){
//        session_regenerate_id(true);
    }

    $_SESSION['last_action'] = $now;
    session_write_close();
}else{
    session_write_close();
    if($_SERVER['REQUEST_URI'] != $CONFIG->base_dir){
        header('Location: '. $CONFIG->base_dir);
        die();
    }
}

