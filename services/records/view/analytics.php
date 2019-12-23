<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_RECORD)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}

require_once($CONFIG->dir . 'services/records/Analytic.php');

if(!isset($user_id)){
    $user_id = get_param('u', 0);
}

if(!isset($edit_view)){
    $edit_view = get_param('e', false);
}

$analitycs = Analytic::patient($user_id);
?>

<div class="row analytics">
    <div class="col-xs-12 col-sm-6">
        <span class="analytic_header">Subpoblaciones Linfocitarias</span>
        <div class="wp_table">
            <table name="1">
                <tr>
                    <th>Fecha</th><?php
                    $atributtes = Analytic::attributes(1);
                    foreach($atributtes as $attr){?>
                        <th><?= $attr?></th><?php
                    }
                    if($edit_view){?>
                        <td></td><?php
                    }?>
                </tr><?php
                if(isset($analitycs->{1}) && count($analitycs->{1}) > 0){
                    foreach($analitycs->{1} as $analytic){?>
                        <tr name="<?= $analytic->id ?>">
                            <td width="125">
                                <?php
                                    if(!$edit_view){
                                        echo $analytic->date;
                                    }else{?>
                                        <input type="text" name="a_val-date" value="<?= $analytic->date ?>" data-saved="<?= $analytic->date ?>"><?php
                                    }
                                ?>
                            </td><?php
                            foreach($atributtes as $id => $attr){?>
                                <td>
                                    <?php
                                    if(!$edit_view){
                                        echo $analytic->result->{$id};
                                    }else{?>
                                        <input type="text" name="a_val-<?= $id?>" value="<?= $analytic->result->{$id} ?>" data-saved="<?= $analytic->result->{$id} ?>"><?php
                                    }
                                    ?>
                                </td><?php
                            }
                            if($edit_view){?>
                                <td class="item_actions">
                                    <i class="fa fa-trash-o"></i>
                                </td><?php
                            }?>
                        </tr><?php
                    }
                }else if(!$edit_view){?>
                    <tr>
                        <td colspan="<?= count($atributtes) + 1 ?>" class="empty_list">- No tiene -</td>
                    </tr><?php
                }?>
            </table>
        </div><?php
        if($edit_view){?>
            <div class="list_actions empty_list row">
                <div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-4 col-lg-2 col-lg-offset-8">
                    <a class="btn btn-default btn-sm bt-add" name="1">Añadir Nueva</a>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2">
                    <a class="btn btn-primary btn-sm bt-save" name="1">Guardar</a>
                </div>
            </div><?php
        }?>
    </div>
    <div class="col-xs-12 col-sm-6">
        <span class="analytic_header">Cargas Virales</span>
        <div class="wp_table">
            <table name="2">
                <tr>
                    <th>Fecha</th><?php
                    $atributtes = Analytic::attributes(2);
                    foreach($atributtes as $attr){?>
                        <th><?= $attr?></th><?php
                    }
                    if($edit_view){?>
                        <td></td><?php
                    }?>
                </tr><?php
                if(isset($analitycs->{2}) && count($analitycs->{2}) > 0){
                    foreach($analitycs->{2} as $analytic){?>
                        <tr name="<?= $analytic->id?>">
                            <td width="125">
                                <?php
                                if(!$edit_view){
                                    echo $analytic->date;
                                }else{?>
                                    <input type="text" name="a_val-date" value="<?= $analytic->date ?>" data-saved="<?= $analytic->date ?>"><?php
                                }
                                ?>
                            </td><?php
                            foreach($atributtes as $id => $attr){?>
                                <td>
                                    <?php
                                    if(!$edit_view){
                                        echo $analytic->result->{$id};
                                    }else{?>
                                        <input type="text" name="a_val-<?= $id?>" value="<?= $analytic->result->{$id} ?>" data-saved="<?= $analytic->result->{$id} ?>"><?php
                                    }
                                    ?>
                                </td><?php
                            }
                            if($edit_view){?>
                                <td class="item_actions">
                                    <i class="fa fa-trash-o"></i>
                                </td><?php
                            }?>
                        </tr><?php
                    }
                }else if(!$edit_view){?>
                    <tr>
                        <td colspan="<?= count($atributtes) + 1 ?>" class="empty_list">- No tiene -</td>
                    </tr><?php
                }?>
            </table>
        </div><?php
        if($edit_view){?>
            <div class="list_actions empty_list row">
                <div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-4 col-lg-2 col-lg-offset-8">
                    <a class="btn btn-default btn-sm bt-add" name="2">Añadir Nueva</a>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2">
                    <a class="btn btn-primary btn-sm bt-save" name="2">Guardar</a>
                </div>
            </div><?php
        }?>
    </div>
</div>