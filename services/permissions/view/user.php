<?php
if(!isset($CONFIG))
    require_once('../../../config.php');
?>
<li name="${id}">
    <div class="row">
        <div class="col-xs-10 col-sm-8 dots" title="${other.fullname}">
            <img class="avatar" src="${other.avatar_src}">
            ${lastname}, ${name}
        </div>
        <div class="col-xs-2 col-sm-2">
            {{if rol == <?= ROL_USER ?>}}
                Paciente
            {{else}}
                {{if rol == <?= ROL_DOCTOR ?>}}
                    Sanitario
                {{else}}
                    {{if rol == <?= ROL_AUXILIAR ?>}}
                        Aministrativo
                    {{else}}
                        Administrador
                    {{/if}}
                {{/if}}
            {{/if}}
        </div>
        <div class="hidden-xs col-sm-2">
            {{if rol_perms == 0}}
                Por defecto
            {{else}}
                Otro
            {{/if}}
        </div>
    </div>
</li>