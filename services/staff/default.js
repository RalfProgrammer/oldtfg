var _Staff = {
    _vars : {

    },

    initialize : function(wrapper){
        _Staff._get.staff(function(all_staff){
            if(all_staff.length != 0){
                _Server.get_tmpl('services/staff/view/staff_item.php', function(tmpl){
                    $.each(all_staff, function(i, user){
                        _Staff._view.add_to_list(wrapper, user, false);
                    });
                });
            }
            _Staff._view.load_DOM(wrapper);
        })
    },

    _get : {
        staff : function(callback){
            var list = [];
            _Users._get.users(function(all_users){
                $.each(all_users, function(i, user){
                    if(user.rol == 2 || user.rol == 3){
                        list.push(user);
                    }
                });
                if(callback && typeof(callback) == 'function')
                    callback(list);
            });
        }
    },

    _set : {

    },

    _process : {
        filter : function(text, branch, horary, callback){
            var reg_exp = false;
            if(text != ''){
                reg_exp = new RegExp(text,"i");
            }

            var horary_name = "";
            switch(horary){
                case 'M' : horary_name = 'morning';break;
                case 'E' : horary_name = 'evening';break;
                case 'N' : horary_name = 'night';break;
                default  : horary_name = false;
            }

            _Staff._get.staff(function(all_staff){
                var list = [];
                $.each(all_staff, function(i, staff){
                    if(branch == 0 || staff.branch == branch){
                        if(!reg_exp || reg_exp.test(staff.other.fullname) || reg_exp.test(staff.staff_id)){
                            if(!horary_name || staff.other.horary_val[horary_name]){
                                list.push(staff);
                            }
                        }
                    }
                });
                if(callback && typeof(callback))
                    callback(list);
            })
        }
    },

    _view : {
        load_DOM : function(wrapper){
            var _dom = {
                sel_branch : wrapper.find('select.sel-branch'),
                sel_horary : wrapper.find('select.sel-horary'),
                bt_search  : wrapper.find('.bt-search'),
                inp_text   : wrapper.find('input[type=text].inp-name')
            }

            _dom.bt_search.click(function(){
                var parent = $(this).parents('.filter_staff'),
                    turn   = _dom.sel_horary.val(),
                    text   = parent.find('.inp-name').val(),
                    branch = _dom.sel_branch.val();

                _Staff._process.filter(text, branch, turn, function(staff){
                    var turn_list = wrapper.find('ul.staff_list');
                    turn_list.empty();
                    if(staff.length > 0){
                        $.each(staff, function(i, user){
                            _Staff._view.add_to_list(wrapper, user , turn_list);
                        })
                    }else{
                        turn_list.append('<li style="text-align: center">- No hay usuarios -</li>');
                    }
                })
            });

            _dom.inp_text.keyup(function(event){
                if ( event.keyCode == 13 ) {
                    wrapper.find('.bt-search').click();
                }else{
                    if(_dom.inp_text.val() == ''){
                        wrapper.find('.bt-search').click();
                    }
                }
            });

            _dom.sel_horary.change(function(){
                wrapper.find('.bt-search').click();
            });
            _dom.sel_branch.change(function(){
                wrapper.find('.bt-search').click();
            });

            wrapper.find('.staff_list').on('click', 'li.staff_item', function(){
                var id = $(this).attr('name');
                _Staff._view.staff(id, function(){

                });
            })

        },

        add_to_list : function(wrapper, data, list){
            list = list || false;
            _Server.get_tmpl('services/staff/view/staff_item.php', function(tmpl){
                tmpl = $.tmpl(tmpl, data);
                if(list){
                    list.append(tmpl);
                }else{
                    wrapper.find('.staff_list').append(tmpl.clone());
                }
            });
        },

        add_to_patient_list : function(list, data){
            list.find('li.empty_list').remove();
            list.append(
                '<li class="patient_item" name="' + data.id + '">'+
                    '<div class="row">' +
                        '<div class="col-xs-12 col-sm-8">' +
                            '<img class="avatar" src="' + data.other.avatar_src + '">' +
                            data.other.fullname +
                        '</div>' +
                        '<div class="hidden-xs col-sm-4">' +
                            data.historic +
                        '</div>' +
                    '</div>' +
                    '<i class="fa fa-trash-o bt-delete-patient"></i>' +
                '</li>');
        },

        staff :function(id, on_edit){
            var popup = new Popup();
            popup.setTitle('Personal Hospital');
            popup.setModal(true);

            _Server.get_view('services/staff/view/staff_details.php', {id : id} , function(html){
                var wrapper  = popup.setContent(html),
                    ids      = [],
                    searcher = false;


                wrapper.find('.bt-save').click(function(){
                    var data = {
                        id : id,
                        b : wrapper.find('select[name=s_branch]').val(),
                        t : wrapper.find('select[name=s_horary]').val(),
                        r : wrapper.find('input[name=s_room]').val(),
                        o : wrapper.find('input[name=s_office]').val(),
                        p : wrapper.find('input[name=s_hphone]').val()
                    }
                    if(!data.b || !data.t){
                        alert('error', 'Debes rellenar la especialidad y el horario');
                        return false;
                    }
                    _Server.post_data('services/staff/post/attributes.php', data, function(){
                        alert('success', 'Guardado correctamente');
                    }, function(error){
                        alert('error', error);
                    })
                });

                _Users._process.filter_patients(function(patients){
                    searcher = new Searcher(wrapper.find('.inp-search'), patients);
                    var ids = [];
                    wrapper.find('.patients_list li.patient_item').each(function(){ids.push($(this).attr('name'));});

                    var filters = {
                        fullname : { key : 'other.fullname', value : false, type : 'like', op: 'or'},
                        historic : { key : 'historic', value : false, type : 'like', op: 'or'},
                        ids      : { key : 'id' , value : ids, type : '!in', op : 'and'}
                    }
                    searcher.init(filters);
                });

                searcher.callback(function(data){
                    _Staff._view.add_to_patient_list(wrapper.find('.patients_list ul'), data);
                    var ids = [];
                    wrapper.find('.patients_list li.patient_item').each(function(){ids.push($(this).attr('name'));});
                    searcher.add_filter('ids', { key : 'id' , value : ids, type : '!in', op : 'and'});
                    _Server.post_data('services/patients/post/save_relation.php', {d: id, p: data.id}, function(){
                        alert('success', 'Asignado correctamente');
                    }
                    ,function(error){
                        alert(error);
                    });
                });

                wrapper.find('.patients_list ul').on('click', 'i.bt-delete-patient', function(){
                    if(confirm('¿Estas seguro de quitarle el paciente?')){
                        var parent = $(this).parent('li'),
                            data   = {
                                d : id,
                                p : parent.attr('name')
                            }
                        parent.fadeOut('fast');
                        _Server.post_data('services/patients/post/delete_relation.php', data, function(){
                            alert('success', 'Eliminada correctamente');
                            parent.remove();
                            var ids         = [],
                                ul_patients = wrapper.find('.patients_list ul');
                            ul_patients.find('li.patient_item').each(function(){ids.push($(this).attr('name'));});

                            searcher.add_filter('ids', { key : 'id' , value : ids, type : '!in', op : 'and'});
                            if(ul_patients.find('li').length == 0){
                                ul_patients.append('<li class="empty_list" style="text-align: center">- No tiene asignado ningun paciente -</li>');
                            }
                        },function(error){
                            parent.fadeIn('fast');
                            alert('error', error);
                        });
                    }
                });
            })
        }
    }
}