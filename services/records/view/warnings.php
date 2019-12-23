<?php
if(!isset($CONFIG))
    require_once('../../../config.php');

if(!Permission::can_view(PERMISSION_RECORD)){
    require_once($CONFIG->dir . 'views/error_view.php');
    die();
}

if(!isset($user_id) || !$user_id)
    $user_id = get_param('u', false);

if(!isset($force))
    $force = get_param('f', false);//insertado detras del hitorial

require_once($CONFIG->dir . 'services/records/Warning.php');

$warnings = Warning::getPatientWarnings($user_id);

$no_read = array_filter($warnings, create_function('$w', 'return ($w->other->read) ? false : true;'));

if(!$force)
    $warnings = $no_read;

$num_no_read = count($no_read);

if($num_no_read > 0 || $force){?>

    <div class="hv-record_warning">
        <h4>
            <i class="fa fa-warning"></i><?= $num_no_read ?> avisos nuevos
        </h4><?php
        if($USER->rol != 1){?>
            <label class="bt-add"><i class="fa fa-plus"></i>Añadir</label>
            <div class="new_warning">
                <textarea placeholder="Escribe tu alerta" class="warning_text"></textarea>
                <div class="row">
                    <div class="col-xs-12 col-sm-8 col-md-10">
                        <ul class="warning_scope">
                            <li>Receptores: </li>
                            <li><input value="1" class="warning_scope" type="checkbox">Paciente</li>
                            <li><input value="2" class="warning_scope" type="checkbox">Personal sanitario</li>
                            <li><input value="3" class="warning_scope" type="checkbox">Personal administrativo</li>
                            <li><input value="4" class="warning_scope" type="checkbox">Administradores</li>
                        </ul>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-2">
                        <a class="btn btn-primary bt-save_warning">Guardar</a>
                    </div>
                </div>
            </div><?php
        }?>
        <ul class="list_warnings">
            <?php
            foreach($warnings as $warning){
                $Creator = new User($warning->getCreator());?>
                <li name="<?= $warning->getId() ?>" class="<?= ($warning->other->read)? 'read' : '' ?>">
                    <?php
                    if($USER->id == $Creator->getId()){?>
                        <i class="fa fa-trash-o bt-delete_warning"></i><?php
                    }?>
                    <img src="<?= $Creator->getSrcAvatar()?>" class="avatar">
                    <div class="warning_text">
                        <h6><?= $Creator->getFullName() . ' ' . $warning->getDate()?></h6>
                        <p><?= $warning->getText() ?></p>
                    </div>
                </li><?php
            }?>
        </ul>
        <?php
            if(!$force){?>
                <div class="row">
                    <div class="hidden-xs col-sm-3 col-nd-4 col-lg-5"></div>
                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2">
                        <a class="btn btn-default bt-close">Vale</a>
                    </div>
                    <div class="hidden-xs col-sm-3 col-md-4 col-lg-5"></div>
                </div><?php
            }
        ?>

        <style>
            <?php
            if(!$force){?>
                .hv-record_warning{
                    position: absolute;
                    background-color: #fff;
                    border: 8px solid #f0ad4e;
                    border-radius: 5px;
                    padding: 10px;
                    z-index: 1000;
                    width: 75%;
                    left: 0;
                    right: 0;
                    margin: 0 auto;
                    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.15);
                }

                @media (max-width: 767px) {
                    .hv-record_warning {
                        width: 95%;
                    }
                }<?php
            }else{?>
                .hv-record_warning{
                    position: relative;
                }
                .hv-record_warning > h4{
                    font-size: 14px;
                    font-weight: normal;
                }
                .hv-record_warning i.fa-warning{
                    display: none;
                }<?php
            }?>
            .hv-record_warning .bt-add{
                position: absolute;
                top: 5px;
                right: 10px;
                font-size: 16px;
                cursor: pointer;
            }<?php
            if($force){?>
                .hv-record_warning .bt-add{
                    top: 0;
                }<?php
            }?>
            .hv-record_warning .bt-add i{
                color: #666;
                margin-right: 5px;
                cursor: pointer;
            }
            .hv-record_warning h4 i{
                margin-right: 5px;
                color: #eea236;
            }
            .hv-record_warning .list_warnings{
                max-height: 250px;
                overflow: auto;
            }
            .hv-record_warning .list_warnings li{
                border: 1px solid #d8d8d8;
                padding: 5px;
                position: relative;
            }
            .hv-record_warning .list_warnings li.read{
                background-color: #f7f7f7;
            }
            .hv-record_warning .list_warnings li img{
                position: absolute;
                top: 5px;
                left: 5px;
            }
            .hv-record_warning .list_warnings li > div.warning_text{
                padding-left: 50px;
            }
            .hv-record_warning .list_warnings li > div.warning_text h6{
                margin-bottom: 5px;
            }
            .hv-record_warning .list_warnings li > div.warning_text p{
                margin-bottom: 0;
                max-height: 100px;
                overflow: auto;
            }
            .hv-record_warning .new_warning{
                margin-bottom: 10px;
                display: none;
            }
            .hv-record_warning .new_warning.on{
                display: block;
            }
            .hv-record_warning .warning_scope,
            .hv-record_warning .warning_scope li input,
            .hv-record_warning .warning_scope li {
                display: inline-block;
                margin-right: 5px;
            }
            .hv-record_warning .warning_scope li {
                margin-right: 10px;
                margin-left: 5px;
            }
            .hv-record_warning .bt-delete_warning {
                position: absolute;
                top: 5px;
                right: 5px;
                cursor: pointer;
                z-index: 10;
            }
        </style>

        <script>
            $(function(){
                var wrapper = $('.hv-record_warning'),
                    _dom    = {
                        create : wrapper.find('.new_warning')
                    },
                    patient = 0;

                wrapper.find('.bt-add').click(function(){
                    _dom.create.toggleClass('on', !_dom.create.hasClass('on'));
                });

                wrapper.find('.bt-save_warning').click(function(){
                    var data = {
                        text    : encodeURIComponent(_dom.create.find('.warning_text').val()),
                        scope   : '',
                        patient : <?= $user_id ?>
                    }
                    _dom.create.find('input[type=checkbox].warning_scope').each(function(){
                        if($(this).is(':checked')){
                            data.scope += $(this).val();
                        }
                    });

                    if(data.text == ""){
                        alert('error', 'Rellena el mensaje');
                        return false;
                    }
                    if(data.scope == ""){
                        alert('error', 'Selecciona algun receptor');
                        return false;
                    }

                    _Server.post_data('services/records/post/save_warning.php', data, function(info){
                        alert('success', 'guardada correctamente');
                        wrapper.find('.list_warnings').prepend(
                            '<li name="' + info.id + '" class="read">' +
                                '<i class="fa fa-trash-o bt-delete_warning"></i>'+
                                '<img src="' + _User.avatar + '" class="avatar">' +
                                '<div class="warning_text">' +
                                    '<h6>' + _User.name + ' ' + info.date + '</h6>' +
                                    '<p>' + info.text + '</p>' +
                                '</div>' +
                            '</li>'
                        );
                    })
                })

                wrapper.on('click','.bt-delete_warning', function(){
                    if(confirm('¿Estas seguro de borrarlo?')){
                        var parent = $(this).parents('li:first');
                        parent.fadeOut(function(){
                            _Server.post_data('services/records/post/delete_warning.php', {id: parent.attr('name')}, function(){
                                parent.remove();
                            },function(error){
                                alert('error', error);
                                parent.fadeIn('fast');
                            });
                        })
                    }
                })

                wrapper.find('.bt-close').click(function(){
                    $('.popup_modal').remove();
                    var ids = [];
                    wrapper.find('.list_warnings li:not(.read)').each(function(){
                        ids.push($(this).attr('name'));
                    });
                    wrapper.remove();
                    if(ids.length > 0)
                        _Server.post_data('services/records/post/save_warnings_read.php', {ids: ids})
                })
            })
        </script>
    </div><?php
    if(!$force){?>
        <div class="popup_modal"></div><?php
    }
}?>