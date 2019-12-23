<?php
if(!isset($CONFIG))
    require_once('../../config.php');

if(!Permission::can_view(PERMISSION_MEDICINE)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}

require_once($CONFIG->dir . 'services/medicine/Medicine.php');
$medicines = Medicine::getByUser();

?>
<div class="tabs">
    <div class="tab" name="Actual">
        <div class="box"><?php
            $filter = 'actual';
            require $CONFIG->dir . 'services/medicine/view/list_user_medicine.php';
            ?>
        </div>
    </div>
    <div class="tab" name="Historico">
        <div class="box"><?php
            $filter = 'old';
            require $CONFIG->dir . 'services/medicine/view/list_user_medicine.php';?>
        </div>
    </div>
</div>