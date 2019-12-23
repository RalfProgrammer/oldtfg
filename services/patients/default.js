var _Patients = {
    _vars : {
        filter : false
    },

    initialize : function(wrapper){
        _Patients._get.patients(function(all_patients){
            console.log(all_patients);

            _Patients._vars.filter = new Filter(all_patients);
            _Patients._vars.filter.addFilter('fullname', { key: 'other.fullname', value: false, type: 'like', op : 'or'});
            _Patients._vars.filter.addFilter('historic', { key: 'historic', value: false, type: 'like', op : 'or'});

            var list = wrapper.find('.patients_list');
            if(all_patients.length != 0){
                _Server.get_tmpl('services/patients/view/patient_item.php', function(tmpl){
                    $.each(all_patients, function(i, patient){
                        _Patients._view.add_to_list(list, patient);
                    });
                });
            }else{
                list.html('<li class="empty_list">- No hay pacientes -</li>');
            }
            _Patients._view.load_DOM(wrapper);
        })
    },

    _get : {
        patients : function(callback){
            var list = [];
            _Users._get.users(function(all_users){
                console.log(all_users);
                $.each(all_users, function(i, user){
                    if(user.rol == 1){
                        list.push(user);
                    }
                });
                list.sort(function(a, b){return a.lastname > b.lastname;})
                if(callback && typeof(callback) == 'function')
                    callback(list);
            });
        }
    },

    _set : {

    },

    _view : {
        load_DOM : function(wrapper){
            wrapper.find('input[name=searcher_patients]').keyup(function(){
                _Patients._view.search(wrapper, $(this).val());
            });

            wrapper.find('ul.patients_list').on('click', 'li', function(){
                _Patients._view.open_patient($(this).attr('name'));
            })
        },

        add_to_list : function(list, data){
            _Server.get_tmpl('services/patients/view/patient_item.php', function(tmpl){
                list.find('.empty_list').remove();
                tmpl = $.tmpl(tmpl, data);
                var exist = list.find('li[name=' + data.id + ']');
                if(exist.length > 0){
                    exist.html(tmpl.children());
                }else{
                    list.append(tmpl);
                }
            })
        },

        search : function(wrapper, text){
            _Patients._get.patients(function(patients){
                _Patients._vars.filter.fillFilterValue('fullname', text);
                _Patients._vars.filter.fillFilterValue('historic', text);
                var results = _Patients._vars.filter.applyFilters(),
                    list    = wrapper.find('ul.patients_list');
                list.html('<li class="empty_list" style="text-align: center">- No hay resultados -</li>');
                $.each(results, function(i, value){
                    _Patients._view.add_to_list(list, value);
                })

            })
        },

        open_patient : function(id){
            var popup = new Popup();
            popup.setTitle('Paciente');
            popup.setModal(true);

            _Server.get_view('services/patients/view/patient_details.php', {id : id} , function(html){
                var wrapper  = popup.setContent(html),
                    ids      = [],
                    searcher = false,
                    doctor_l = wrapper.find('.doctors_list ul');

                wrapper.find('.bt-save').click(function(){
                    var data = {
                        id : id,
                        h : parseInt(wrapper.find('input[name=p_height]').val()),
                        w : parseInt(wrapper.find('input[name=p_weight]').val())
                    }
                    if(isNaN(data.h)){
                        alert('success', 'Valor incorrecto en la altura');
                        return false;
                    }
                    if(isNaN(data.w)){
                        alert('error', 'Valor incorrecto en el peso');
                        return false;
                    }

                    _Server.post_data('services/patients/post/attributes.php', data, function(){
                        alert('success', 'Guardado correctamente');
                    }, function(error){
                        alert('error', error);
                    })
                });

                _Users._process.filter_doctors(function(doctors){
                    searcher = new Searcher(wrapper.find('.inp-search'), doctors);
                    var ids = [];
                    doctor_l.find('li.doctor_item').each(function(){ids.push($(this).attr('name'));});

                    var filters = {
                        fullname : { key : 'other.fullname', value : false, type : 'like', op: 'or'},
                        historic : { key : 'historic', value : false, type : 'like', op: 'or'},
                        ids      : { key : 'id' , value : ids, type : '!in', op : 'and'}
                    }
                    searcher.init(filters);
                });

                searcher.callback(function(data){
                    _Patients._view.add_to_doctor_list(doctor_l, data);
                    var ids = [];
                    doctor_l.find('li.patient_item').each(function(){ids.push($(this).attr('name'));});
                    searcher.add_filter('ids', { key : 'id' , value : ids, type : '!in', op : 'and'});
                    _Server.post_data('services/patients/post/save_relation.php', {p: id, d: data.id}, function(){
                            alert('success', 'Asignado correctamente');
                        }
                        ,function(error){
                            alert('error', error);
                        });
                });

                wrapper.find('select.filter_branch').change(function(){
                    var branch = $(this).val();
                    if(branch != "0"){
                        searcher.add_filter('branch', { key : 'branch' , value : branch, type : '=', op : 'and'});
                    }else{
                        searcher.remove_filter('branch');
                    }
                });

                doctor_l.on('click', 'i.bt-delete-doctor', function(){
                    if(confirm('¿Estas seguro de quitarle la asignacion?')){
                        var parent = $(this).parent('li'),
                            data   = {
                                p : id,
                                d : parent.attr('name')
                            }
                        parent.fadeOut('fast');
                        _Server.post_data('services/patients/post/delete_relation.php', data, function(){
                            parent.remove();
                            var ids         = [];
                            doctor_l.find('li.doctor_item').each(function(){ids.push($(this).attr('name'));});

                            searcher.add_filter('ids', { key : 'id' , value : ids, type : '!in', op : 'and'});
                            if(doctor_l.find('li').length == 0){
                                doctor_l.append('<li class="empty_list" style="text-align: center">- No tiene asignado nadie -</li>');
                            }
                            alert('success', 'P.Sanitario quitado');
                        },function(error){
                            parent.fadeIn('fast');
                            alert(error);
                        });
                    }
                });
            });
        },

        add_to_doctor_list : function(list, data){
            list.find('li.empty_list').remove();
            list.append(
                '<li class="doctor_item" name="' + data.id + '">'+
                    '<div class="row">' +
                        '<div class="col-xs-12 col-sm-6 col-md-4">' +
                            '<img class="avatar" src="' + data.other.avatar_src + '">' +
                                data.lastname + ', ' + data.name +
                        '</div>' +
                        '<div class="hidden-xs col-sm-3 col-md-3">'+
                            data.staff_id +
                        '</div>' +
                        '<div class="hidden-xs col-sm-3 col-md-3">'+
                            data.other.horary_text +
                        '</div>' +
                        '<div class="hidden-xs hidden-sm col-md-2">'+
                            data.other.branch_name +
                        '</div>'+
                    '</div>' +
                    '<i class="fa fa-trash-o bt-delete-doctor"></i>' +
                '</li>');
        }
    },

    _process : {

    }
}