<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

?>
<div class="box">
    <h5 class="header_box dots">
        <i class="fa fa-medkit"></i>Tu medicación actual
    </h5><?php
    $user_id    = $USER->id;
    $filter     = 'actual';
    include($CONFIG->dir . 'services/medicine/view/user_medicine.php');
    ?>
</div>