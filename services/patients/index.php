<?php
if(!isset($CONFIG))
    require_once('../../config.php');

if(!Permission::can_view(PERMISSION_PATIENT)){
    echo_error_view();
}

Log::create('patients');
?>

<div class="tabs">
    <div class="tab" name="Pacientes">
        <div class="box">
            <h5 class="header_box"><i class="fa fa-group"></i>Listado de pacientes</h5>
            <div class="row">
                <div class="col-xs-12 col-sm-10">
                    <input type="text" placeholder="Buscar por nombre o historial" name="searcher_patients">
                </div>
                <div class="col-xs-12 col-sm-2">
                    <a class="btn btn-primary bt-search inp_height"><i class="fa fa-search"></i>Buscar</a>
                </div>
            </div>
            <div class="row patients_list_header">
                <div class="col-xs-12 col-sm-8 col-md-6 col-lg-3"><h5>Apellidos, Nombre</h5></div>
                <div class="hidden-xs col-sm-4 col-md-2 col-lg-2"><h5>Nº Historial</h5></div>
                <div class="hidden-xs hidden-sm col-md-2 col-lg-2"><h5>Dni</h5></div>
                <div class="hidden-xs hidden-sm col-md-2 col-lg-2"><h5>Telefono</h5></div>
                <div class="hidden-xs hidden-sm hidden-md col-lg-2"><h5>Nacimiento</h5></div>
                <div class="hidden-xs hidden-sm hidden-md col-lg-1"><h5>Sexo</h5></div>
            </div>
            <ul class="patients_list list_limit_height-lg">
                <li class="empty_list">Cargando...</li>
            </ul>
        </div>
    </div>
</div>