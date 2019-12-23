<li class="medicine_edit_item" name="${id}">
    <div class="row">
        <div class="col-xs-12" style="line-height: 30px">
            <i class="fa fa-chevron-right"></i> ${name}
        </div>
        <div style="padding-left: 10%" class="col-xs-12">
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-2">
                    <select name="m_interval_1"><?php
                        for($i = 1; $i < 10; $i++){?>
                            <option value="<?= $i ?>"><?= $i ?></option><?php
                        }?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-2">
                    <select name="m_interval_2"><?php
                        for($i = 1; $i < 15; $i++){?>
                            <option value="<?= $i ?>">cada <?= $i ?></option><?php
                        }?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-2">
                    <select name="m_interval_3">
                        <option value="h">Hora/s</option>
                        <option value="d">Dia/s</option>
                        <option value="w">Semana/s</option>
                        <option value="m">Mes/es</option>
                        <option value="y">Año/s</option>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <input type="text" name="m_start" placeholder="Inicio">
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <input type="text" name="m_end" placeholder="Fin">
                </div>
                <div class="col-xs-12">
                    <textarea name="m_details" placeholder="otros detalles"></textarea>
                </div>
            </div>
        </div>
    </div>
</li>

<style>
    .medicine_edit_item {
        border-bottom: 1px solid #ccc;
    }
</style>