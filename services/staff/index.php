<?php
if(!isset($CONFIG))
    require_once('../../config.php');

if(!Permission::can_view(PERMISSION_STAFF)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}

$branchs = Staff::getBranchNames();

?>
<div class="tabs hv-staff">
        <div class="tab" name="Personal">
            <div class="box">
                <h5 class="header_box"><i class="fa fa-user-md"></i>Listado del personal</h5>
                <div class="row filter_staff">
                    <div class="col-xs-12 col-sm-4">
                        <div class="row">
                            <div class="col-xs-6">
                                <select class="sel-branch">
                                    <option value="0">Cualquier Rama</option>
                                    <option value="-1">Administrativo</option>
                                    <?php
                                    foreach($branchs as $i => $branch){?>
                                        <option value="<?= $i ?>"><?= $branch ?></option><?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-xs-6">
                                <select class="sel-horary">
                                    <option value="0">Cualquier horario</option>
                                    <option value="M">Mañana</option>
                                    <option value="E">Tarde</option>
                                    <option value="N">Noche</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <input type="text" class="inp-name" placeholder="Nombre o identificador" >
                    </div>
                    <div class="col-xs-12 col-sm-2">
                        <a class="btn btn-primary bt-search inp_height"><i class="fa fa-search"></i>Buscar</a>
                    </div>
                </div>
                <div class="row staff_list_header">
                    <div class="col-xs-12 col-sm-8 col-md-6 col-lg-3"><h5>Apellidos, Nombre</h5></div>
                    <div class="hidden-xs col-sm-4 col-md-2 col-lg-2"><h5>Rama</h5></div>
                    <div class="hidden-xs hidden-sm col-md-2 col-lg-2"><h5>Consulta</h5></div>
                    <div class="hidden-xs hidden-sm col-md-2 col-lg-2"><h5>Telefono</h5></div>
                    <div class="hidden-xs hidden-sm hidden-md col-lg-2"><h5>Oficina</h5></div>
                    <div class="hidden-xs hidden-sm hidden-md col-lg-1"><h5>Horario</h5></div>
                </div>
                <ul class="staff_list list_limit_height-lg">

                </ul>
            </div>
        </div>
</div>
<style>
    .hv-staff .filter_staff{
        margin-bottom: 10px;
    }
    .hv-staff .staff_list_header{
        padding: 5px 0;
        display: block;
    }
    .hv-staff .staff_list li{
        line-height: 30px;
    }
</style>