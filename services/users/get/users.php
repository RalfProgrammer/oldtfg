<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

$all_users = User::get_all();

echo json_encode(array(
    "success" => true,
    "info"    => $all_users
));