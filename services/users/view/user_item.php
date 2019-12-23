<?php
if(!isset($CONFIG))
    require_once('../../../config.php')
?>
<li name="${id}">
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-md-6 col-lg-3">
            <img class="avatar" src="${other.avatar_src}">
            ${lastname}, ${name}
        </div>
        <div class="hidden-xs col-sm-4 col-md-2 col-lg-2">
            {{if rol == <?= ROL_USER ?>}}
                Paciente
            {{else}}
                {{if rol == <?= ROL_DOCTOR ?>}}
                    Personal Sanitario
                {{else}}
                    {{if rol == <?= ROL_AUXILIAR ?>}}
                        Personal Administrativo
                    {{else}}
                        {{if rol == <?= ROL_ADMIN ?>}}
                            Administrador
                        {{/if}}
                    {{/if}}
                {{/if}}
            {{/if}}
        </div>
        <div class="hidden-xs hidden-sm col-md-2 col-lg-2">
            ${other.identifier}
        </div>
        <div class="hidden-xs hidden-sm col-md-2 col-lg-2">
            ${dni}
        </div>
        <div class="hidden-xs hidden-sm hidden-md col-lg-2">
            ${birthdate}
        </div>
        <div class="hidden-xs hidden-sm hidden-md col-lg-1">
            {{if sex == "male"}}
                Hombre
            {{else}}
                Mujer
            {{/if}}
        </div>
    </div>
</li>