var _Permissions = {
    _vars : {
        list     : false,
        filter   : false,
        can_edit : false
    },

    initialize : function(wrapper){
        _Users._get.users(function(users){
            var user_list = wrapper.find('.user_rol_list');
            _Server.get_tmpl("services/permissions/view/user.php", function(tmpl){
                $.each(users , function(i, user){
                    _Permissions._add.user_to_list(user_list, user);
                })
                _Navigator.reloadOwl();
            });
            _Permissions.loadDOM(wrapper);
            _Permissions._vars.filter = new Filter(users);
            _Permissions._vars.filter.addFilter('fullname', { key : 'other.fullname', value : false, type : 'like', op: 'or'});
            _Permissions._vars.filter.addFilter('rol',  { key : 'rol', value : false, type : '=', op: 'and'});
            wrapper.find("a.bt-filter_name").click();
        })
    },

    loadDOM : function(wrapper){
        var filter = new Filter();
        wrapper.find("input.inp-name").keyup(function(){
            var name_filter = $(this).val();
            _Permissions._vars.filter.fillFilterValue('fullname', ((name_filter != "") ? name_filter : false));
            var users     = _Permissions._vars.filter.applyFilters(),
                user_list = wrapper.find('.user_rol_list');
            user_list.empty();
            user_list.append('<li class="empty_list">- No hay resultados -</li>');
            $.each(users , function(i, user){
                _Permissions._add.user_to_list(user_list, user);
            })
        });

        wrapper.find("select.sel-rol").change(function(){
            var rol_filter = $(this).val();
            _Permissions._vars.filter.fillFilterValue('rol', ((rol_filter > 0) ? rol_filter : false));
            var users     = _Permissions._vars.filter.applyFilters(),
                user_list = wrapper.find('.user_rol_list');
            user_list.empty();
            user_list.append('<li class="empty_list">- No hay resultados -</li>');
            $.each(users , function(i, user){
                _Permissions._add.user_to_list(user_list, user);
            })
        });

        wrapper.find('label.header_action').click(function(){
            var rol = $(this).data().id;
            _Permissions._process.create_perm(0, rol, function(perm){
                wrapper.find('.perm_list_rol[name=' + perm.rol + ']').append(
                    '<li name="' + perm.id + '"><i class="fa fa-angle-right"></i>' + perm.name + '</li>'
                );
            }, false);
        });

        wrapper.find('ul.perm_list_rol').on('click', 'li', function(){
            var $this   = $(this),
                id      = $this.attr('name'),
                rol_id  = $this.parents('ul.perm_list_rol:first').attr('name');

            _Permissions._process.create_perm(id, rol_id, function(data){
                $this.find('span').text(data.name);
            }, function(data){
                $this.fadeOut('fast', function(){
                    $this.remove();
                })
            })
        });

        wrapper.find('ul.user_rol_list').on('click', 'li', function(){
            var li_item     = $(this),
                id          = $(this).attr('name');

            _Permissions._process.edit_user_perm(id, function(user_data){
                _Users._set.user(user_data);
                var actual_filter = wrapper.find('select.sel-rol').val();
                if(actual_filter > 0 && actual_filter != user_data.rol){
                    li_item.fadeOut('fast', function(){
                        $(this).remove();
                    })
                }
            });
        });
    },

    _add : {
        user_to_list : function(list, user){
            _Server.get_tmpl("services/permissions/view/user.php", function(tmpl){
                list.find('.empty_list').remove();
                tmpl = $.tmpl(tmpl, user);
                var exist = list.find('li[name=' + user.id + ']');
                if(exist.length > 0){
                    exist.html(tmpl.children());
                }else{
                    list.append(tmpl);
                }
            })
        }
    },

    _get : {
         perms : function(user_id, callback){
            user_id = user_id || 0;
            _Server.get_data('services/permissions/get/perms.php', {u: user_id} , function(data){
                _Permissions._vars.list      = data.list;
                _Permissions._vars.can_edit  = data.can_edit;
                if(callback && typeof(callback) == "function")
                    callback(_Permissions._vars);
            }, function(error){
                alert(error);
            })
        }
    },

    _process : {
        edit_user_perm : function(id, on_save){
            var popup = new Popup();
            popup.setModal(true);
            popup.setTitle("Permisos usuario:");
            _Server.get_tmpl("services/permissions/view/user_permissions.php", function(tmpl){
                _Server.get_data("services/permissions/get/perms.php", {u: id}, function(perms_data){
                    var user = _Users._get.user(id);
                    tmpl     = $.tmpl(tmpl, {perms: perms_data , user: user});

                    var wrapper     = popup.setContent(tmpl),
                        select_rol  = wrapper.find('select[name=perm_user_rol]'),
                        select_perm = wrapper.find('select[name=perm_user_perm]');

                    var perm_user = (perms_data.user.rol_perms > 0) ?  perms_data.user.rol_perms : perms_data.user.rol;

                    _Permissions._process.print_perm(wrapper.find('.perm_wrapper'), perms_data.list[perm_user], perms_data.can_edit);

                    if(perms_data.can_edit){
                        select_rol.change(function(){
                            var rol_id       = $(this).val();
                            select_perm.empty();

                            $.each(perms_data.list, function(i, perm){
                                if(perm.rol == rol_id){
                                    if(perm.individual == user.id){
                                        select_perm.append('<option value="user">*Editados para el usuario</option>');
                                    }else{
                                        select_perm.append('<option value="' + perm.id +'">' + perm.name + '</option>');
                                    }
                                }
                            });

                            var perm_id = 0;
                            if(perms_data.user.rol == rol_id && perms_data.user.rol_perms > 0){
                                if(perms_data.user.rol_perms == perms_data.user.perm_own){
                                    select_perm.val('user');
                                }else{
                                    select_perm.val(perms_data.user.rol_perms);
                                }
                                perm_id = perms_data.user.rol_perms;
                            }else{
                                perm_id = rol_id;
                            }

                            _Permissions._process.print_perm(wrapper.find('.perm_wrapper'), perms_data.list[perm_id], perms_data.can_edit);
                        });

                        select_perm.change(function(){
                            var perm_id = $(this).val(),
                                rol_id  = wrapper.find('select[name=perm_user_rol]').val();

                            select_perm.find('option.perm_temp').remove();

                            switch(perm_id){
                                case '0'    : perm_id = rol_id; break;
                                case 'user' : perm_id = perms_data.user.perm_own;break;
                            }

                            if(perm_id){
                                _Permissions._process.print_perm(wrapper.find('.perm_wrapper'), perms_data.list[perm_id], perms_data.can_edit);
                            }

                        });

                        wrapper.find('.perm_wrapper').on('change', 'select', function(){
                            var own_perm = select_perm.find('option[value=user]');
                            if(own_perm.length > 0){
                                own_perm.prop('selected',true);
                            }else{
                                select_perm.append('<option value="user" class="perm_own perm_temp" selected >*Temporales para el usuario</option>')
                            }
                        });

                        wrapper.find('.bt-save').click(function(){
                            var data = {
                                id    : wrapper.find('select[name=perm_user_perm]').val(),
                                rol   : wrapper.find('select[name=perm_user_rol]').val(),
                                u     : user.id
                            }

                            if(data.id == 'user'){
                                data.values = {};
                                wrapper.find('.perm_wrapper li').each(function(){
                                    data.values[$(this).attr('name')] = $(this).find('select').val();
                                });
                                data.values = $.toJSON(data.values);
                            }

                            _Server.post_data('services/permissions/post/save_user_perm.php', data, function(response){
                                popup.close();
                                if(on_save && typeof(on_save) == "function")
                                    on_save(response);
                            }, function(error){
                                alert(error);
                            })
                        });
                    }
                });
            })
        },

        create_perm : function(p_id, p_rol, on_create, on_delete){
            var popup = new Popup();
            popup.setTitle("Crear permisos:");
            popup.setModal(true);
            _Permissions._get.perms(false, function(perms_data){
                _Server.get_tmpl("services/permissions/view/edit_permission.php", function(tmpl){
                    var actual_perm = perms_data.list[((p_id > 0) ? p_id : p_rol)];
                    tmpl = $.tmpl(tmpl, {data: {id: p_id, rol: p_rol, name: actual_perm.name}});
                    popup.setContent(tmpl);
                    var wrapper = popup.getContent();
                    wrapper = wrapper.find('.hv-perms_create');

                    _Permissions._process.print_perm(wrapper.find('.perm_wrapper'), actual_perm, perms_data.can_edit);

                    //Change rol
                    wrapper.find("select[name=perm_rol]").change(function(){
                        var rol = $(this).val();
                        _Permissions._process.print_perm(wrapper.find('.perm_wrapper'), perms_data.list[rol], perms_data.can_edit);
                    });

                    if(perms_data.can_edit){
                        wrapper.find('.bt-save').click(function(){
                            var data = {
                                id     : wrapper.attr('name'),
                                name   : encodeURIComponent(wrapper.find('input[name=perm_name]').val()),
                                rol    : wrapper.find('select[name=perm_rol]').val(),
                                values : {}
                            }
                            wrapper.find('.perm_wrapper li').each(function(){
                                data.values[$(this).attr('name')] = $(this).find('select').val();
                            });

                            if(data.name == ""){
                                alert('error', "introduzca un nombre");
                                return false;
                            }

                            data.values = $.toJSON(data.values);

                            _Server.post_data('services/permissions/post/save_perm.php', data, function(response){
                                alert('success', 'Guardado correctamente');
                                popup.close();
                                if(on_create && typeof(on_create) == "function")
                                    on_create(response);
                            }, function(error){
                                alert('error', error);
                            })
                        });

                        if(p_id > 4){
                            wrapper.find('.bt-delete').click(function(){
                                if(confirm("¿Estas seguro de borrarlo?")){
                                    _Server.post_data('services/permissions/post/delete_perm.php', {id: p_id}, function(response){
                                        popup.close();
                                        if(on_delete && typeof(on_delete) == "function")
                                            on_delete(response);
                                    }, function(error){
                                        alert(error);
                                    })
                                }
                            });
                        }
                    }
                })
            })
        },

        print_perm : function(wrapper, perm, can_edit){
            _Server.get_tmpl('services/permissions/view/perm.php', function(tmpl){
                tmpl = $.tmpl(tmpl, perm);
                wrapper.html(tmpl);

                if(!can_edit){
                    var parent = wrapper.parents('.popup_perms_wrapper');
                    parent.find('select').prop('disabled',true);
                    parent.find('input[type=text]').prop('readonly',true);
                }

            })
        }
    }
}