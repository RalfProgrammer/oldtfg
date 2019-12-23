<?php if(!isset($CONFIG))
require_once('../../config.php');

if(!Permission::can_view(PERMISSION_RECORD)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}

Log::create('records');

require_once($CONFIG->dir . 'services/room/Report.php');

if($USER->rol == 1){
    require($CONFIG->dir . 'services/records/view/record.php');
    ?>
    <script>
        $(function(){
            _Record.initialize( $('main'), <?= $USER->id?>);
        })
    </script><?php
}else{?>
    <div class="tabs">
        <div class="tab" name="Historiales Medicos">
            <div class="row">
                <div class="col-xs-12 col-sm-2 col-md-1 col-lg-1">
                    <select class="sel_ord">
                        <option value="lastname">Ordenado</option>
                        <option value="lastname">Apellidos</option>
                        <option value="name">Nombre</option>
                        <option value="historic">NªHistorial</option>
                        <option value="dni">DNI</option>
                    </select>
                </div>
                <div class="col-xs-6 col-sm-2 col-md-2">
                    <select class="sel_sex">
                        <option value="">Hombres y Mujeres</option>
                        <option value="male">Hombres</option>
                        <option value="female">Mujeres</option>
                    </select>
                </div>
                <div class="col-xs-6 col-sm-2 col-md-2">
                    <select class="sel_old">
                        <option value="">Cualquier edad</option>
                        <option value="0-18">- 18 años</option>
                        <option value="18-24">18 a 24 años</option>
                        <option value="24-40">24 a 40 años</option>
                        <option value="40-65">40 a 65 años</option>
                        <option value="65-1000">+ 65 años</option>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-7">
                    <input type="text" placeholder="Nombre, historial o dni" class="inp_search">
                </div>
            </div>
            <div class="list_records">
                <span class="empty_list">Cargando...</span>
            </div>
        </div>
    </div><?php
}?>