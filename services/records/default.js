var _Record = {
    _vars : {
        user: false
    },

    initialize : function(wrapper, user){
        _Record._vars.user = user;
        wrapper.find('.record-reports .flip_f').css('height', wrapper.find('.record-basic_info').innerHeight() + 'px');
        _Record._view.load_DOM(wrapper);
    },

    _view : {
        load_DOM : function(wrapper){
            wrapper.find('.historic_report').on('click', 'div.report_header', function(){
                var parent = $(this).parent();
                parent.toggleClass('on', !parent.hasClass('on'));
            });

            wrapper.find('.header_action').click(function(){
                var parent = $(this).parents('div.flip:first');
                if(parent.length > 0){
                    if(!parent.hasClass("turn")){
                        parent.addClass('turn');
                        _Record._view._edit[parent.attr('name')](parent.find('.flip_b'), function(){
                            parent.find('.flip_f').css('height', parent.find('.flip_b').innerHeight() + 'px');
                        });
                    }else{
                        if(parent.find('.inp_edited').length > 0){
                            if(!confirm('Tienes cambios sin guardar, ¿Estas seguro de volver?')){
                                return false
                            }
                        }
                        parent.find('.flip_b').find('.flip_content').empty();
                        parent.removeClass('turn');
                        _Record._view._print[parent.attr('name')](parent.find('.flip_f'), function(){
                            parent.find('.flip_f').css('height', '');
                        });
                    }
                }
            });

            wrapper.find('a.bt-create-event').click(function(){
                _Calendar._view.request_event(_Record._vars.user)
            })

            wrapper.find('a.bt-send-message').click(function(){
                _Navigator.go('chat!' + _Record._vars.user);
            })

            wrapper.find('a.bt-open-alerts').click(function(){
                var popup = new Popup();
                popup.setTitle('Alertas del paciente');
                popup.setModal(true);
                popup.show();
                _Record._view._edit.alerts(popup.getContent());
            });

            wrapper.find('.flip_b').on('focusout', 'input[type=text]', function(){
                var inp = $(this);
                if(inp.val() != inp.data().saved){
                    inp.addClass('inp_edited');
                }else{
                    inp.removeClass('inp_edited');
                }
            });

            _Record._view._load.medicines(wrapper.find('.box[name=medicines] .flip_content:first'));
        },

        _edit : {
            alerts : function(wrapper, callback){
                _Server.get_view('services/records/view/warnings.php', {u: _Record._vars.user, f: true}, function(response){
                    wrapper.html(response);
                    if(callback && typeof(callback)){
                        callback();
                    }
                });
            },

            protocols : function(wrapper, callback){
                _Server.get_view('services/records/view/protocols_edit.php', {u: _Record._vars.user}, function(response){
                    var content = wrapper.find('.flip_content');
                    content.html(response);
                    var list    = content.find('.protocol_list');
                    content.find('.bt-add').click(function(){
                        _Server.get_tmpl('services/records/view/protocol_new.php', function(tmpl){
                            tmpl = $.tmpl(tmpl, {});
                            list.append(tmpl);
                            list.scrollTop(55 * list.find('li').length);
                            wrapper.parent().find('.flip_f').css('height', wrapper.innerHeight() + 'px');

                            tmpl.find('input.date').each(function(){
                                $(this).datepicker({
                                    format    : 'yyyy-mm-dd',
                                    weekStart : 1
                                });
                            })
                        })
                    })

                    content.find('input.date').each(function(){
                        $(this).datepicker({
                            format    : 'yyyy-mm-dd',
                            weekStart : 1
                        });
                    })

                    content.on('click', 'i.fa-trash-o', function(){
                        if(confirm('¿Estas seguro de borrarlo?')){
                            var item = $(this).parents('li:first'),
                                id   = item.attr('name');

                            item.fadeOut('fast');
                            if(id != '0'){
                                _Server.post_data('services/records/post/delete_protocol.php', {id: id}, function(info){
                                    item.remove();
                                    alert('success', info);
                                },function(error){
                                    alert('error', error);
                                    item.fadeIn('fast');
                                })
                            }else{
                                item.remove();
                            }
                            wrapper.parent().find('.flip_f').css('height', wrapper.innerHeight() + 'px');
                        }
                    })

                    content.find('.bt-save').click(function(){
                        var parent    = $(this).parents('li:first'),
                            protocols = [],
                            error     = false;
                            count     = {add: 0, edit: 0}

                        wrapper.find('.protocol_list .inp_edited').each(function(){
                            var li_item = $(this).parents('li:first');
                            if(!li_item.hasClass('added')){
                                li_item.addClass('added');
                                var data = {
                                    id      : encodeURIComponent(li_item.attr('name')),
                                    u       : _Record._vars.user,
                                    name    : li_item.find('input[name=name]').val(),
                                    start   : li_item.find('input[name=start]').val(),
                                    end     : li_item.find('input[name=end]').val()
                                }
                                if(data.name == "" || data.start == "" || data.end == ""){
                                    error = true;
                                    return false;
                                }
                                if(data.id != "0"){
                                    count.edit++;
                                }else{
                                    count.add++;
                                }
                                protocols.push(data);
                            }
                        });
                        wrapper.find('.protocol_list .added').removeClass('added');

                        if(error){
                            alert('error', 'Tienes campos sin rellenar');
                            return false;
                        }else{
                            if(count.add > 0 || count.edit > 0){
                                var text = '';
                                if(!confirm('¿Estas seguro de guardar los cambios? (' + count.add + ' nuevos y ' + count.edit + ' editados)')){
                                    return false;
                                }
                            }else{
                                alert('error', 'No hay cambios que guardar');
                                return false;
                            }
                        }

                        _Server.post_data('services/records/post/save_protocols.php', {data: $.toJSON(protocols)}, function(id){
                            wrapper.find('.inp_edited').removeClass('inp_edited');
                            alert('success', 'Cambios guardados');
                            wrapper.find('.header_action').click();
                        },function(error){
                            alert('error', error);
                        })
                    })

                    if(callback && typeof(callback)){
                        callback();
                    }
                })
            },

            analytics : function(wrapper, callback){
                _Server.get_view('services/records/view/analytics.php', {u: _Record._vars.user, e: true}, function(response){
                    var content = wrapper.find('.flip_content');
                    content.html(response);
                    var list_1 = content.find('.analytics table[name=1]'),
                        list_2 = content.find('.analytics table[name=2]');

                    content.find('.bt-add').click(function(){
                        var parent = wrapper.find('table[name=' + $(this).attr('name') +']');
                        _Server.get_tmpl('services/records/view/analytic_new.php', function(tmpl){
                            tmpl = $.tmpl(tmpl, {type: parent.attr('name')});
                            parent.prepend(tmpl);
                            wrapper.parent().css('height', wrapper.innerHeight() + 'px');
                        })
                    })

                    content.on('click', 'i.fa-trash-o', function(){
                        if(confirm('¿Estas seguro de borrarlo?')){
                            var item = $(this).parents('tr:first'),
                                id   = item.attr('name');

                            item.fadeOut('fast');
                            if(id != '0'){
                                _Server.post_data('services/records/post/delete_analytic.php', {id: id}, function(info){
                                    item.remove();
                                    alert('success', info);
                                },function(error){
                                    alert('error', error);
                                    item.fadeIn('fast');
                                })
                            }else{
                                item.remove();
                            }
                            wrapper.parent().css('height', wrapper.innerHeight() + 'px');
                        }
                    })

                    content.find('.bt-save').click(function(){
                        var parent = wrapper.find('table[name=' + $(this).attr('name') +']'),
                            analytics = [],
                            error     = false,
                            count     = {add: 0, edit: 0};

                        parent.find('.inp_edited').each(function(){
                            var tr_item = $(this).parents('tr:first');
                            if(!tr_item.hasClass('added')){
                                tr_item.addClass('added');
                                var data = {
                                    id      : tr_item.attr('name'),
                                    u       : _Record._vars.user,
                                    type    : parent.attr('name'),
                                    result  : {}
                                }

                                tr_item.find('input[type=text]').each(function(){
                                    var value = $(this).val();
                                    if(value != ""){
                                        data.result[$(this).attr('name').split('-')[1]] = value;
                                    }else{
                                        error = true;
                                    }
                                });
                                if(error){
                                    return false;
                                }

                                if(data.id != "0"){
                                    count.edit++;
                                }else{
                                    count.add++;
                                }

                                data.result = $.toJSON(data.result);
                                analytics.push(data);
                            }
                        });

                        parent.find('.added').removeClass('added');

                        if(error){
                            alert('error', 'Tienes campos sin rellenar');
                            return false;
                        }else{
                            if(count.add > 0 || count.edit > 0){
                                var text = '';
                                if(!confirm('¿Estas seguro de guardar los cambios? (' + count.add + ' nuevas y ' + count.edit + ' editadas)')){
                                    return false;
                                }
                            }else{
                                alert('error', 'No hay cambios que guardar');
                                return false;
                            }
                        }


                        _Server.post_data('services/records/post/save_analytics.php', {data: $.toJSON(analytics)}, function(ids){
                            parent.find('.inp_edited').removeClass('inp_edited');
                            alert('success', 'Cambios guardadas');
                            $.each(ids, function(i, id){
                                parent.find('tr[name=0]:first').attr('name', id);
                            })
                        },function(error){
                            alert(error);
                        })
                    })

                    if(callback && typeof(callback)){
                        callback();
                    }
                })
            },

            medicines : function(wrapper, callback){
                _Server.get_view('services/medicine/view/edit_user_medicine.php', {u: _Record._vars.user}, function(response){
                    var content = wrapper.find('.flip_content');
                    content.html(response);

                    content.find('.bt-save').click(function(){

                    });

                    content.find('.item_actions').click(function(){
                        var item  = $(this),
                            id    = item.parents('li:first').attr('name'),
                            popup = new Popup();

                        popup.setTitle('Cancelar Tratamiento');
                        popup.setMaxSize(400);
                        popup.setModal(true);

                        _Server.get_view('services/medicine/view/cancel_medicine.php', {m : id}, function(view){
                            var parent = popup.setContent(view);
                            parent.find('.bt-yes').click(function(){
                                var text = encodeURIComponent(parent.find('textarea').val());
                                if(text == ""){
                                    alert('error', 'Rellena el motivo');
                                    return false;
                                }
                                _Server.post_data('services/medicine/post/cancel.php', {m : id, t : text}, function(response){
                                    alert('success', response);
                                    item.remove();
                                    popup.close();
                                }, function(error){
                                    alert('error', error);
                                })
                            });

                            parent.find('.bt-no').click(function(){
                                popup.close();
                            })
                        })
                    });

                    var wrapper_add = wrapper.find('.add_medication')
                    _Medicine._get.medicines(function(medicines){
                        var md_input = wrapper.find('.med_input'),
                            searcher = new Searcher(md_input, medicines);

                        searcher.set_template('li_medicine');
                        searcher.init({
                            id   : { key : 'id', value : false, type : 'like', op: 'or'},
                            name : { key : 'name', value : false, type : 'like', op: 'or'}
                        });

                        searcher.callback(function(data){
                            wrapper_add.find('span.med_id').attr('name', data.id);
                            wrapper_add.find('label.med_name').text(data.name);
                            wrapper_add.addClass('active');
                        });
                    });
                    wrapper_add.find('.fa-times').click(function(){
                        wrapper_add.find('span.med_id').attr('name', '0');
                        wrapper_add.find('label.med_name').text("");
                        wrapper_add.removeClass('active');
                    });

                    wrapper_add.find('input.med_date').datepicker({
                        format    : 'yyyy-mm-dd',
                        weekStart : 1
                    });

                    wrapper_add.find('.bt-add').click(function(){
                        var data = {
                            u      : _Record._vars.user,
                            id     : wrapper_add.find('.med_id').attr('name'),
                            start  : wrapper_add.find('.med_date').val(),
                            intvl1 : wrapper_add.find('.med_dosis').val(),
                            intvl2 : wrapper_add.find('.med_cycle_num').val(),
                            intvl3 : wrapper_add.find('.med_cycle_type').val()
                        }

                        var error = false;
                        error = (data.id == '' || data.id == '0') ? 'Elige un tratamiento' : error;
                        error = (!error && data.start == '') ? 'Elige la fecha de inicio' : error;
                        error = (!error && (data.intvl1 == '' || data.intvl2 == ''|| data.intvl3 == ''))
                                ? 'Elige la dosis' : error;

                        if(error){
                            alert('error', error);
                            return false;
                        }

                        if(confirm('¿Estas seguro de añadir el tratamiento?')){
                            _Server.post_data('services/medicine/post/save.php', data, function(response){
                                alert('success', 'Tratamiento guardado');
                                _Record._view._edit.medicines(wrapper, function(){
                                    wrapper.parent().find('.flip_f').css('height', wrapper.innerHeight() + 'px');
                                });
                            },function(error){
                                alert('error', error);
                            })
                        }
                    })

                    if(callback && typeof(callback) == 'function')
                        callback();
                })

            }
        },

        _print : {
            protocols : function(wrapper, callback){
                _Server.get_view('services/records/view/protocols.php', {u: _Record._vars.user}, function(response){
                    wrapper.find('.flip_content').html(response);
                    if(callback && typeof(callback)){
                        callback();
                    }
                })
            },

            analytics : function(wrapper,callback){
                _Server.get_view('services/records/view/analytics.php', {u: _Record._vars.user}, function(response){
                    wrapper.find('.flip_content').html(response);
                    if(callback && typeof(callback)){
                        callback();
                    }
                })
            },

            medicines : function(wrapper,callback){
                _Server.get_view('services/medicine/view/user_medicine.php', {u: _Record._vars.user}, function(response){
                    wrapper.find('.flip_content').html(response);
                    _Record._view._load.medicines(wrapper.find('.flip_content'));
                    if(callback && typeof(callback)){
                        callback();
                    }
                })
            }
        },

        _load : {
            medicines : function(wrapper){
                wrapper.find('li').click(function(){
                    var popup = new Popup(),
                        id    = $(this).attr('name');
                    popup.setTitle('Tratamiento');
                    popup.setMaxSize(400);
                    popup.setModal(true);
                    _Server.get_view('services/medicine/view/medicine.php', {m : id}, function(view){
                        popup.setContent(view);
                    })
                })
            }
        }
    }
}