<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

require_once($CONFIG->dir . 'services/records/Analytic.php');

?>
{{if type == 1}}
    <tr name="0">
        <td>
            <input type="text" name="a_val-date" value="" placeholder="Fecha">
        </td>
            <?php
            $atributtes = Analytic::attributes(1);
            foreach($atributtes as $id => $attr){?>
                <td>
                    <input type="text" name="a_val-<?= $id ?>" value="" placeholder="<?= $attr ?>" data-saved="">
                </td><?php
            }?>
        <td class="item_actions">
            <i class="fa fa-trash-o"></i>
        </td>
    </tr>
{{else}}
    {{if type == 2}}
        <tr name="0">
            <td>
                <input type="text" name="a_val-date" value="" placeholder="Fecha">
            </td>
            <?php
            $atributtes = Analytic::attributes(2);
            foreach($atributtes as $id => $attr){?>
                <td>
                    <input type="text" name="a_val-<?= $id ?>" value="" placeholder="<?= $attr ?>" data-saved="">
                </td><?php
            }?>

            <td class="item_actions">
                <i class="fa fa-trash-o"></i>
            </td>
        </tr>
    {{/if}}
{{/if}}