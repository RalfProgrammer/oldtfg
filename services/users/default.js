var _Users = {
    _vars : {
        list   : false,
        filter : false
    },

    initialize : function(wrapper){
        _Users._get.users(function(all_users){
            _Users._vars.filter = new Filter(all_users);
            _Users._vars.filter.addFilter('fullname', {key: 'other.fullname', value: false, type: 'like', op : 'or'});
            _Users._vars.filter.addFilter('id', {key: 'id', value: false, type: 'like', op : 'or'});
            _Users._vars.filter.addFilter('staff', {key: 'staff_id', value: false, type: 'like', op : 'or'});
            _Users._vars.filter.addFilter('historic', {key: 'historic', value: false, type: 'like', op : 'or'});
            _Users._vars.filter.addFilter('rol', {key: 'rol', value: false, type: '=', op : 'and'});
            _Users._vars.filter.fillFilterValue('rol', '1');
            var patients = _Users._vars.filter.applyFilters();
            if(patients.length > 0){
                var list = wrapper.find('ul.user_list');
                _Server.get_tmpl('services/users/view/user_item.php', function(tmpl){
                    $.each(patients, function(i, user){
                        _Users._view.add_to_list(list, user);
                    });
                });
            }
            _Users._view.load_DOM(wrapper);
        })
    },

    _get : {
        users : function(callback){
            if(_Users._vars.list){
                if(callback && typeof(callback) == "function"){
                    var array = $.map(_Users._vars.list, function(value, index){return [value];});
                    array = array.sort(function(a, b){return a.lastname > b.lastname;});
                    callback(array);
                }
            }else{
                _Server.get_data("services/users/get/users.php", false, function(data){
                    _Users._vars.list = data;
                    if(callback && typeof(callback) == "function"){
                        _Users._get.users(callback);
                    }
                })
            }
        },

        user : function(id){
            return _Users._vars.list[id];
        },

        num_users : function(){
            var total = 0;
            _Users._get.users(function(all_users){
                $.each(all_users, function(i,item){total++});
            });
            return total;
        }
    },

    _set : {
        users : function(){

        },

        user : function(data){
            _Users._vars.list[data.id] = data;
        },

        update_user_var : function(id, attr, value){
            _Users._vars.list[id][attr] = value;
        }
    },

    _process : {
        filter : function(text, callback){
            _Users._get.users(function(all_users){
                var reg_exp = false;
                if(text != ''){
                    reg_exp = new RegExp(text,"i");
                }
                var list = [];
                $.each(all_users, function(i, user){
                    if(!reg_exp || reg_exp.test(user.other.fullname) || reg_exp.test(user.other.identifier)){
                        list.push(user);
                    }
                });
                if(callback && typeof(callback))
                    callback(list);
            })
        },

        filter_patients : function(callback){
            _Users._get.users(function(all_users){
                var list = [];
                $.each(all_users, function(i, user){
                    if(user.rol == 1){
                        list.push(user);
                    }
                });
                if(callback && typeof(callback))
                    callback(list);
            })
        },

        filter_doctors : function(callback){
            _Users._get.users(function(all_users){
                var list = [];
                $.each(all_users, function(i, user){
                    if(user.rol == 2){
                        list.push(user);
                    }
                });
                if(callback && typeof(callback))
                    callback(list);
            })
        },

        filter_agenda : function(callback){
            _Users._get.users(function(all_users){
                var list = [];
                $.each(all_users, function(i, user){
                    if(user.rol == 1 || user.rol == 2){
                        list.push(user);
                    }
                });
                if(callback && typeof(callback))
                    callback(list);
            })
        },

        save : function(data, on_success, on_error){
            _Server.post_data('services/users/post/save.php', data, function(user_data){
                _Users._set.user(user_data);
                if(on_success && typeof(on_success) == 'function')
                    on_success(user_data);
            }, on_error);
        },

         delete : function(id, on_success, on_error){
            _Server.post_data('services/users/post/delete.php', {id: id}, function(user_id){
                delete _Users._vars.list[user_id];
                if(on_success && typeof(on_success) == 'function')
                    on_success(user_id);
            }, on_error);
        }
    },

    _view : {
        add_to_list: function(list, data){
            _Server.get_tmpl('services/users/view/user_item.php', function(tmpl){
                tmpl = $.tmpl(tmpl, data);
                var exist = list.find('li[name=' + data.id + ']');
                if(exist.length > 0){
                    exist.html(tmpl.children());
                }else{
                    list.append(tmpl);
                }
            })
        },

        load_DOM : function(wrapper){
            var _dom = {
                sel_rol   : wrapper.find('select.u_rol'),
                bt_search : wrapper.find('.bt-search'),
                bt_create : wrapper.find('.bt-create'),
                inp_text  : wrapper.find('input[type=text]'),
                ul_users  : wrapper.find('ul.user_list')
            }

            var filter = new Filter();

            _dom.bt_search.click(function(){
                var value = _dom.inp_text.val();
                value = value != '' ? value : false;
                _Users._vars.filter.fillFilterValue('id', value);
                _Users._vars.filter.fillFilterValue('staff', value);
                _Users._vars.filter.fillFilterValue('historic', value);
                _Users._vars.filter.fillFilterValue('fullname', value);
//                  _Users._process.filter(value, function(users){
                      _Users._view.fill_list(_dom.ul_users, _Users._vars.filter.applyFilters());
//                  })
            });
            _dom.inp_text.keyup(function(event){
                if ( event.keyCode == 13 ) {
                    _dom.bt_search.click();
                }else{
                    if(_dom.inp_text.val() == ''){
                        _dom.bt_search.click();
                    }
                }
            });

            _dom.bt_create.click(function(){
                var data      = {id: 0},
                    input_val = _dom.inp_text.val();

                if(parseInt( input_val )){
                    data.historic = input_val;
                    data.staff_id = input_val;
                }else{
                    data.name = input_val;
                }
                _Users._view.user(data, function(user_data){
                    _Users._view.add_to_list(_dom.ul_users, user_data);
                });
            });

            _dom.ul_users.on('click','li', function(){
                var user = _Users._get.user($(this).attr('name'));
                if(user){
                    _Users._view.user(user, function(user_data){
                        _Users._view.add_to_list(_dom.ul_users, user_data);
                    }, function(user_id){
                        _dom.ul_users.find('li[name=' + user_id +']').fadeOut('fast', function(){
                            $(this).remove();
                            if(_dom.ul_users.find('li').length == 0){
                                _dom.ul_users.append('<li style="text-align: center">- No hay usuarios -</li>');
                            }
                        })
                    });
                }
            });
            _dom.sel_rol.change(function(){
                _Users._vars.filter.fillFilterValue('rol', $(this).val());
                _Users._view.fill_list(_dom.ul_users, _Users._vars.filter.applyFilters());
            })
        },

        fill_list : function(list, data){
            list.empty();
            if(data.length > 0){
                $.each(data, function(i, user){
                    _Users._view.add_to_list(list, user);
                })
            }else{
                list.append('<li style="text-align: center">- No hay usuarios -</li>');

            }
        },

        user : function(user_data, on_save, on_delete){
            var popup = new Popup();
            popup.setTitle('Vista usuario');
            popup.setModal(true);

            if(!user_data.hasOwnProperty('other'))
                user_data.other = {};

            if(!user_data.hasOwnProperty('contact'))
                user_data.contact = {address: {}, phone : [], email: []};

            _Server.get_data("services/permissions/get/perms.php", {u: user_data.id}, function(perms_data){
                debug(perms_data.list, 'perms');
                _Server.get_tmpl('services/users/view/create_user.php', function(tmpl){
                    tmpl = $.tmpl(tmpl, {user: user_data, perms: perms_data.list});
                    var wrapper = popup.setContent(tmpl);

                    wrapper.find('input[name=birthdate]').datepicker({
                        format    : 'yyyy-mm-dd',
                        weekStart : 1
                    })

                    wrapper.find('select[name=rol]').change(function(){
                        var rol = $(this).val(),
                            perms = wrapper.find('select[name=perm]')
                        if(rol){
                            perms.html('<option value="0">Por defecto</option>');
                            $.each(perms_data.list, function(i, perm){
                                if(perm.rol == rol && (perm.individual == "0" || perm.individual == user_data.id)){
                                    if(perm.individual == "0"){
                                        perms.append('<option value="' + perm.id + '">' + perm.name + '</option>');
                                    }else{
                                        perms.append('<option value="' + perm.id + '">Editados para el usuario*</option>');
                                    }
                                }
                            });
                            if(user_data.permissions > 0){
                                perms.find('option[value=' + user_data.permissions + ']').attr('selected', 'selected');
                            }
                        }else{
                            perms.html('<option>Perfil del usuario</option>');
                        }
                    })

                    wrapper.find('.bt-password').click(function(){
                        var _this = $(this);
                        _Server.post_data('services/users/post/reset_password.php', {u: user_data.id}, function(){
                            _this.removeClass('btn-warning');
                            _this.addClass('btn-success');
                            _this.text('Generada');
                            setTimeout(function(){
                                _this.removeClass('btn-success');
                                _this.addClass('btn-warning');
                                _this.text('Regenerar Contraseña');
                            }, 5000)
                        }, function(error){
                            alert(error);
                        })
                    });

                    wrapper.find('.inp_identifier').blur(function(){
                        var data = {
                            id : $(this).val(),
                            r  : wrapper.find('select[name=rol]').val(),
                            u  : user_data.id
                            },
                            input = $(this);
                        _Server.get_data('services/users/get/valid_id.php', data, function(){
                            input.removeClass('has_error');
                            input.addClass('is_correct');
                        }, function(error){
                            alert('error', 'El identificador ya existe');
                            input.addClass('has_error');
                            input.removeClass('is_correct');
                        })
                    });

                    wrapper.find('.bt-save').click(function(){
                        var data = {
                            id : user_data.id
                        }

                        var error = false;

                        wrapper.find('.u_save:not(.no)').each(function(i, item){
                            var name   = $(item).attr('name'),
                                value  = $(item).val(),
                                encode = ["name", "lastname", "information"];

                            if(value){
                                data[name] = ((encode.indexOf(name) >= 0) ? encodeURIComponent(value) : value);
                            }else{
                                if($(item).hasClass('mandatory')){
                                    error = true;
                                    $(item).addClass('has_error');
                                }
                            }
                        })

                        if(error){
                            alert('Rellena todos los campos obligatorios');
                            return false;
                        }

                        if(user_data.rol && user_data.rol != data.rol){
                            if(!confirm('¿Estas seguro de cambiar al usuario de rol?')){
                                return false;
                            }
                        }

                        _Users._process.save(data, function(user_data){
                            popup.close();
                            if(on_save && typeof(on_save) == 'function')
                                on_save(user_data);
                        }, function(error){
                            alert(error);
                        })
                    });

                    wrapper.find('.bt-cancel').click(function(){
                        popup.close();
                    })

                    wrapper.find('.bt-delete').click(function(){
                        var button = $(this);
                        if(confirm('¿Estas seguro de borrarlo?')){
                            _Users._process.delete(user_data.id, function(user_deleted){
                                popup.close();
                                if(on_delete && typeof(on_delete) == 'function')
                                    on_delete(user_deleted);
                            }, function(error){
                                alert(error);
                            });
                        }
                    })

                    wrapper.find('select[name=rol]').change(function(){
                        wrapper.find('.rol_info .on').removeClass('on');
                        wrapper.find('.rol_info .u_save').addClass('no');
                        var val = $(this).val();
                        switch(val){
                            case '1':
                                var container = wrapper.find('.rol_info_values[name=patient]');
                                container.addClass('on');
                                container.find('.u_save').removeClass('no');
                                break;
                            case '2':
                            case '3':
                                var container = wrapper.find('.rol_info_values[name=staff]');
                                container.addClass('on');
                                container.find('.u_save').removeClass('no');
                                break;
                        }
                    })
                })
            })
        }
    }
}