<?php
if(!isset($CONFIG))
    require_once('../../config.php');

?>
<div class="tabs">
    <div class="tab" name="Portada">
        <?php
        switch($USER->rol){
            case ROL_USER       : require($CONFIG->dir . '/views/summary/patients.php');break;
            case ROL_DOCTOR     : require($CONFIG->dir . '/views/summary/doctors.php');break;
            case ROL_AUXILIAR   : require($CONFIG->dir . '/views/summary/auxiliars.php');break;
            case ROL_ADMIN      : require($CONFIG->dir . '/views/summary/admins.php');break;
        }?>
    </div>
    <div class="tab news" name="Noticias">
        <div class="box">
            <h4>Un ‘eco’ permitiría obviar las biopsias para pacientes con VIH y hepatitis C</h4>
            Prácticamente todo paciente prefiere un escáner a un pinchazo. Y si encima la información que se obtiene es más útil,
            quedan pocas dudas. Eso es lo que sucede con el fibroscán, una técnica de imagen que permite medir el grado de fibrosis
            del hígado (su endurecimiento o pérdida de funcionalidad producido por la hepatitis C), si se compara con el sistema
            tradicional: una punción.
            <h6 style="text-align: right">18 MAR 2014</h6>
        </div>
        <div class="box">
            <h4>Un gel vaginal protege contra el VIH después de la relación sexual</h4>
            Los geles vaginales con antivirales (los llamados microbicidas) son una de las esperanzas para frenar la expansión del VIH.
            Básicamente consisten en cremas que deben actuar destruyendo el virus antes de que llegue a las mucosas de la mujer y se
            asiente. Así, ellas tendrán el control de la infección, sobre todo en entornos en los que les cuesta negociar el uso del
            preservativo con su pareja. Pero, hasta ahora, se estaban ensayando los geles para usarlos antes de la relación. La posibilidad
            de que funcionen después, como publica Science Translational Medicine de un ensayo en macacos, da aún más opciones.
            <h6 style="text-align: right">13 MAR 2014</h6>
        </div>
    </div>
</div>
<style>
    .alerts .alert_title i{
        margin-right: 10px;
    }
    .alerts .alert_message{
        padding-bottom: 10px;
    }
    .alerts .alert_message:not(:last-child){
        border-bottom: 1px solid #ccc;
        margin-bottom: 20px;
    }
</style>