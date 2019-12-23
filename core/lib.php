<?php
/**
 * User: Denis
 * Date: 20/03/14
 */

require_once($CONFIG->dir . 'core/classes/Log.php');
require_once($CONFIG->dir . 'core/classes/Permission.php');
require_once($CONFIG->dir . 'core/classes/System.php');
require_once($CONFIG->dir . 'core/classes/User.php');
require_once($CONFIG->dir . 'services/users/Staff.php');
require_once($CONFIG->dir . 'services/users/Patient.php');

function get_js_files(){
    $js = array(
        'external/js/jquery/jquery-2.1.0.min.js',
        'external/js/jquery/jquery.touchSwipe.min.js',
        'external/js/jquery/jquery.tmpl.js',
        'external/js/jquery/jquery.json-2.4.min.js',
        'external/js/jquery/jquery.event.move.js',
//        'external/js/jquery/jquery.event.ue.js',
        'external/js/SIPml-api.js',
        'core/js/core.js',
        'core/js/util.js',
        $js[] = 'core/js/server.js'
    );
    if(!is_logged()){
        $js[] = 'core/js/index.js';
    }
    if(is_logged()){
        $js[] = 'core/js/main.js';
        $js[] = 'core/js/navigator.js';
        $js[] = 'core/js/popup.js';
        $js[] = 'core/js/searcher.js';
        $js[] = 'core/js/filter.js';
        $js[] = 'core/js/call.js';
        $js[] = 'core/js/sip.js';
        $js[] = 'core/js/user.js';
        $js[] = 'views/summary/default.js';
        $js[] = 'services/calendar/default.js';
        $js[] = 'services/chat/default.js';
        $js[] = 'services/chat/chat.js';
        $js[] = 'services/room/default.js';
        $js[] = 'services/room/default2.js';
        $js[] = 'services/permissions/default.js';
        $js[] = 'services/users/default.js';
        $js[] = 'services/staff/default.js';
        $js[] = 'services/patients/default.js';
        $js[] = 'services/medicine/default.js';
        $js[] = 'services/records/default.js';
        $js[] = 'services/records/default2.js';
        $js[] = 'external/js/owl/owl.carousel.min.js';
        $js[] = 'external/datepicker/bootstrap-datepicker.js';
        $js[] = 'external/js/CLNDR/moment-2.5.1.js';
        $js[] = 'external/js/CLNDR/underscore.js';
        $js[] = 'external/js/CLNDR/clndr.js';
    }
    return $js;
}

function get_css_files(){
    return array(
        'core/css/core.css',
        'core/css/main.css',
        'core/css/structure.css',
        'core/css/flip.css',
        'views/css/chat.css',
        'views/css/videoconference.css',
        'external/css/owl/owl.carousel.css',
        'external/css/owl/owl.theme.css',
        'external/css/owl/owl.transitions.css',
        'external/bootstrap/css/bootstrap.css',
        'external/bootstrap/css/bootstrap-theme.css',
        'external/datepicker/datepicker.css',
        'external/css/fontawesome/css/font-awesome.min.css',
        'services/chat/chat.css',
        'services/calendar/default.css',
        'services/records/default.css',
        'external/js/CLNDR/clndr.css'
    );
}

function get_param($name, $default = false){
    $value = isset($_POST[$name]) ? $_POST[$name] : (isset($_GET[$name]) ? $_GET[$name] : $default);
    return (is_array($value)) ? $value : strip_tags($value);
}

function echo_json($success, $info, $die = true){
    echo json_encode(array(
        'success' => $success,
        'info'    => $info
    ));
    if($die)
        die();
}

function echo_view($info){
    echo '<span style="text-align: center;display: block;width: 100%;padding: 25px">' . $info . '</span>';
    die();
}

function echo_error_view(){
    global $CONFIG;
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}

function is_logged(){
    global $USER;
    return ($USER && $USER->id > 0);
}

