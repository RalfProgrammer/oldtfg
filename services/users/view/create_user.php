<?php
if(!isset($CONFIG))
    require_once('../../../config.php');
?>

<div class="hv-manage-user">
    <span class="section-header"><i class="fa fa-key"></i>Rol del usuario:</span>
    <div class="row">
        <div class="col-xs-12 col-sm-6 {{if user.id}}col-md-5{{/if}}">
            Rol*
            <select class="u_save mandatory" name="rol">
                <option value="">- Selecciona el rol -</option>
                <option {{if user.rol == <?= ROL_USER ?>}}selected{{/if}} value="1">Paciente</option>
                <option {{if user.rol == <?= ROL_DOCTOR ?>}}selected{{/if}} value="3">Personal Sanitario</option>
                <option {{if user.rol == <?= ROL_AUXILIAR ?>}}selected{{/if}} value="2">Personal Administrativo</option>
                <option {{if user.rol == <?= ROL_ADMIN ?>}}selected{{/if}} value="4">Administrador</option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-6 {{if user.id}}col-md-4{{/if}}">
            Perfil*
            <select class="u_save mandatory" name="perm">
                {{if user.rol > 0}}
                    <option value="0" {{if user.permissions == 0}}selected{{/if}}>Por defecto</option>
                    {{each(i, perm) perms}}
                        {{if perm.rol == user.rol && perm.id > 0}}
                            {{if perm.individual > 0}}
                                {{if perm.individual == user.id}}
                                    <option value="${perm.id}"{{if user.permissions == perm.id}}selected{{/if}}>Editados para el usuario*</option>
                                {{/if}}
                            {{else}}
                                <option value="${perm.id}"{{if user.permissions == perm.id}}selected{{/if}}>${perm.name}</option>
                            {{/if}}
                        {{/if}}
                    {{/each}}
                {{else}}
                    <option value="">- Selecciona el Pefil -</option>
                {{/if}}
            </select>
        </div>
        <div class="col-xs-12 col-sm-12 {{if user.id}}col-md-3{{/if}}">
            {{if user.id}}
                Contraseña
                <a class="btn btn-warning bt-password inp_height">Resetear</a>
            {{/if}}
        </div>
    </div>
    <span class="section-header"><i class="fa fa-user"></i>Datos Personales:</span>
    <div class="row">
        <div class="col-xs-12 col-sm-4">
            Nombre*
            <input class="u_save mandatory" type="text" name="name" value="${user.name}" placeholder="Nombre">
        </div>
        <div class="col-xs-12 col-sm-8">
            Apellidos*
            <input class="u_save mandatory" type="text" name="lastname" value="${user.lastname}" placeholder="Apellidos">
        </div>
        <div class="col-xs-12 col-sm-4">
            DNI*
            <input class="u_save mandatory" type="text" name="dni" value="${user.dni}" placeholder="DNI">
        </div>
        <div class="col-xs-12 col-sm-4">
            Sexo*
            <select class="u_save mandatory" name="sex">
                <option value="">Sexo</option>
                <option {{if user.sex == "male"}}selected{{/if}} value="male">Hombre</option>
                <option {{if user.sex == "female"}}selected{{/if}} value="female">Mujer</option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-4">
            Fecha de nacimiento*
            <input class="u_save mandatory" type="text" name="birthdate" value="${user.birthdate}" placeholder="Fecha de nacimiento">
        </div>
    </div>

    <span class="section-header"><i class="fa fa-home"></i>Datos de contacto:</span>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6">
            Domicilio
            <input class="u_save" type="text" name="address_dir" value="${user.contact.address.dir}" placeholder="Domicilio">
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            Ciudad
            <input class="u_save" type="text" name="address_ciu" value="${user.contact.address.ciu}" placeholder="Domicilio">
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3">
            C.P
            <input class="u_save" type="text" name="address_cp" value="${user.contact.address.cp}" placeholder="Codigo Postal">
        </div>
        <div class="col-xs-12 col-sm-6">
            Telefono
            <input class="u_save" type="text" name="phone" value="${user.other.phones}" placeholder="Telefono">
        </div>
        <div class="col-xs-12 col-sm-6">
            Email
            <input class="u_save" type="text" name="email" value="${user.other.emails}" placeholder="Email">
        </div>
    </div>

    <span class="section-header"><i class="fa fa-hospital-o"></i>Datos clinicos:</span>
    <div class="row">
        <div class="col-xs-12">
            Grupo sanguineo*
            <select class="u_save mandatory" name="blood">
                <option value="">Grupo sanguineo</option>
                <option value="0-" {{if user.blood == "0-"}}selected{{/if}}>0-</option>
                <option value="0+" {{if user.blood == "0+"}}selected{{/if}}>0+</option>
                <option value="A-" {{if user.blood == "A-"}}selected{{/if}}>A-</option>
                <option value="A+" {{if user.blood == "A+"}}selected{{/if}}>A+</option>
                <option value="B-" {{if user.blood == "B-"}}selected{{/if}}>B-</option>
                <option value="B+" {{if user.blood == "B+"}}selected{{/if}}>B+</option>
                <option value="AB-" {{if user.blood == "AB-"}}selected{{/if}}>AB-</option>
                <option value="AB+" {{if user.blood == "AB+"}}selected{{/if}}>AB+</option>
            </select>
        </div>
        <div class="rol_info col-xs-12" style="padding: 0;">
            {{var is_staff   = false}}
            {{var is_patient = false}}
            {{if user.rol == 1}}
                {{set is_patient = true}}
            {{else}}
                {{if user.rol == 2 || user.rol == 3}}
                    {{set is_staff = true}}
                {{/if}}
            {{/if}}
            <div class="row rol_info_values {{if is_patient}}on{{/if}}" name="patient">
                <div class="col-xs-12 col-sm-6">
                    Numero de historial
                    <input class="u_save {{if !is_patient}}no{{/if}} inp_identifier" type="text" value="${user.historic}" name="historic" placeholder="Sin rellenar se genera automatico">
                </div>
                <div class="col-xs-6 col-sm-3">
                    Altura (cm)
                    <input class="u_save {{if !is_patient}}no{{/if}}" type="text" value="${user.height}" name="height" placeholder="Altura">
                </div>
                <div class="col-xs-6 col-sm-3">
                    Peso (kg)
                    <input class="u_save {{if !is_patient}}no{{/if}}" type="text" value="${user.weight}" name="weight" placeholder="Peso">
                </div>
            </div>
            <div class="row rol_info_values {{if is_staff}}on{{/if}}" name="staff">
                <div class="col-xs-12 col-sm-4">
                    Identificador de empleado
                    <input class="u_save {{if !is_staff}}no{{/if}} inp_identifier" type="text" value="${user.staff_id}" name="staff_id" placeholder="Sin rellenar se genera automatico">
                </div>
                <div class="col-xs-12 col-sm-6">
                    Rama del empleado*
                    <select class="u_save mandatory {{if !is_staff}}no{{/if}}" name="branch">
                        <option value="">- Selecciona la especialidad -</option><?php
                        $branchs = Staff::getBranchNames();
                        foreach($branchs as $key => $branch){?>
                            <option value="<?= $key?>" {{if user.branch == <?= $key ?>}}selected{{/if}}><?= $branch?></option><?php
                        }?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-2">
                    Horario*
                    <select class="u_save mandatory {{if !is_staff}}no{{/if}}" name="horary">
                        <option value="">- Horario -</option>
                        <option value="M"  {{if user.horary == "M"}}selected{{/if}}>Mañana</option>
                        <option value="ME" {{if user.horary == "ME"}}selected{{/if}}>Mañana y Tarde</option>
                        <option value="MN" {{if user.horary == "MN"}}selected{{/if}}>Mañana y Noche</option>
                        <option value="MEN" {{if user.horary == "MEN"}}selected{{/if}}>Mañana , Tarde y Noche</option>
                        <option value="E" {{if user.horary == "E"}}selected{{/if}}>Tarde</option>
                        <option value="EN" {{if user.horary == "EN"}}selected{{/if}}>Tarde y Noche</option>
                        <option value="N" {{if user.horary == "N"}}selected{{/if}}>Noche</option>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-4 ">
                    Consulta
                    <input class="u_save {{if !is_staff}}no{{/if}}" type="text" value="${user.room}" name="room" placeholder="Consulta">
                </div>
                <div class="col-xs-12 col-sm-4">
                    Despacho
                    <input class="u_save {{if !is_staff}}no{{/if}}" type="text" value="${user.office}" name="office" placeholder="Despacho">
                </div>
                <div class="col-xs-12 col-sm-4">
                    Telefono
                    <input class="u_save {{if !is_staff}}no{{/if}}" type="text" value="${user.h_phone}" name="h_phone" placeholder="Telefono Hospital">
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            Información adicional
            <textarea class="u_save" name="information" placeholder="Informacion adicional">${user.information}</textarea>
        </div>
    </div>
    <div class="row buttons">
        <div class="col-xs-12 {{if user.id}}col-sm-3 col-md-2 col-sm-offset-3 col-md-offset-6{{else}}col-sm-4 col-sm-offset-4 col-md-2 col-md-offset-8{{/if}} ">
            <a class="btn btn-primary bt-save">Guardar</a>
        </div>
        {{if user.id }}
            <div class="col-xs-12 col-sm-3 col-md-2">
                <a class="btn btn-danger bt-delete">Borrar</a>
            </div>
        {{/if}}
        <div class="col-xs-12 {{if user.id}}col-sm-3 col-md-2{{else}}col-sm-4 col-md-2{{/if}}">
            <a class="btn btn-default bt-cancel">Cancelar</a>
        </div>
    </div>
</div>
<style>
    .hv-manage-user .buttons {
        margin: 10px 0;
    }
    .hv-manage-user .buttons a {
        margin: 5px 0;
    }
    .hv-manage-user textarea{
        height: 90px;
    }
    .hv-manage-user .rol_info_values{
        display: none;
    }
    .hv-manage-user .rol_info_values.on{
        display: block;
    }
</style>