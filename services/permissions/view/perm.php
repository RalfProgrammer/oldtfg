<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

$structure = Permission::structurePermissions();
?>
<ul class="perm row <?php if(Permission::can_edit(PERMISSION_ROLES)){echo 'editable';}?>" name="${id}"><?php
    foreach($structure as $perm){?>
        <li name="<?= $perm['id'] ?>" class="col-xs-12 col-sm-6">
            <div class="perm_val row">
                <div class="col-xs-8">
                    <i class="fa <?= $perm['icon']?>"></i>
                    <span class="dots"><?= $perm['name']?></span>
                </div>
                <div class="col-xs-4">
                    <?php
                    if($perm['levels']){?>
                        <select>
                            <option value="0"><i class="fa fa-times"></i>Deshabilitado</option><?php
                            foreach($perm['levels'] as $key => $value){?>
                                <option value="<?= $key ?>" {{if values[<?= $perm['id'] ?>] == '<?= $key ?>'}}selected{{/if}}><?= $value?></option><?php
                            }?>
                        </select><?php
                    }?>
                </div>
            </div>
        </li><?php
    }?>
</ul>

<style>
    ul.perm .perm_val div:first-child{
        position: relative;
    }
    ul.perm .perm_val div:first-child i{
        position: absolute;
        left: 0;
        top: 8px;
        font-size: 18px;
        color: #666;
        cursor: default;
    }
    ul.perm .perm_val div:first-child span{
        padding-top: 8px;
        padding-left: 20px;
    }
    ul.perm li select{
        height: 35px;
        padding: 6px 5px;
    }
</style>