var _Calendar = {
    _vars : {
        user        : false,
        user_rol    : false,
        events      : false,
        today       : false,
        act         : false,
        agenda      : false
    },

    initialize : function(user, wrapper){
        _Calendar._vars.user       = user || _User.id;
        _Calendar._vars.user_rol   = wrapper.find('input[name=user_rol]').val();
        _Calendar._vars.events     = false;
        _Calendar._vars.today      = new Date().getTime();
        _Calendar._vars.act        = 'today';
        if(_Calendar._vars.user_rol == 2){
            _Calendar._vars.agenda = wrapper.find('.doctor_agenda_header');
        }

        _Calendar._view.print_events(wrapper);
        _Calendar._view.load_DOM(wrapper);
    },

    _get : {
        events : function(user, callback){
            if(_Calendar._vars.user_rol == 2){
                var doctor_day = _Calendar._vars.agenda.find('input[name=doctor_day]').val();
                _Server.get_data('services/calendar/get/events.php', {u: user, d : doctor_day, a: _Calendar._vars.act}, function(response){
                    _Calendar._vars.events     = response.events;
                    _Calendar._vars.agenda.find('.event_day').text(response.day);
                    _Calendar._vars.agenda.find('input[name=doctor_day]').val(response.day_time);
                    if(typeof(callback) == 'function')
                        callback(_Calendar._vars.events);
                })
            }else{
                _Server.get_data('services/calendar/get/events.php', {u: user, d : _Calendar._vars.today}, function(response){
                    _Calendar._vars.events = response;
                    if(typeof(callback) == 'function')
                        callback(_Calendar._vars.events);
                })
            }
        },

        num_events : function(){
            var cont = 0;
            $.each(_Calendar._vars.events , function(){cont++;});
            return cont;
        },

        month_events : function(month, year , callback){
            _Server.get_data('services/calendar/get/month_events.php', {m: month, y: year, u: _Calendar._vars.user}, function(response){
                if(callback && typeof(callback) == 'function')
                    callback(response);
            });
        }
    },

    _set : {
    },

    _view : {
        load_DOM : function(wrapper){
            var searcher,
                wp_calendar = wrapper.find('.calendar');

            if(wp_calendar.length > 0){
                wp_calendar.clndr({
                    daysOfTheWeek: ['D', 'L', 'M', 'X', 'J', 'V', 'S'],
                    weekOffset : 1,
                    clickEvents: {
                        click: function(target) {
                            var date = target.date.toDate().getTime();
                            if(_Calendar._vars.user_rol == 1){
                                if(target.events.length > 0){
                                    _Calendar._view.open_events(date, date);
                                }
                            }else{
                                _Calendar._vars.act = 'today';
                                _Calendar._vars.agenda.find('input[name=doctor_day]').val((date/1000));
                                _Calendar._view.print_events(wrapper);
                            }
                        },
                        onMonthChange: function(month) {
                            _Calendar._view.fill_month_events(wp_calendar, parseInt(month.month()) + 1, parseInt(month.year()));
                        }
                    }
                });

                _Calendar._view.fill_month_events(wp_calendar, false, false);
            }

            _Users._process.filter_agenda(function(patients){
                searcher = new Searcher(wrapper.find('input[name=search_user]'), patients);
                var filters = {
                    fullname : { key : 'other.fullname', value : false, type : 'like', op: 'or'},
                    historic : { key : 'historic', value : false, type : 'like', op: 'or'},
                    staff    : { key : 'staff_id', value : false, type : 'like', op: 'or'},
                    rol      : { key : 'rol', value : 1, type : '=', op : 'and'}
                }
                searcher.set_template('search_agenda');
                searcher.init(filters);

                searcher.callback(function(data){
                    _Navigator.go('calendar!' + data.id, false);
                });

                wrapper.find('select.ev_user_type').change(function(){
                    searcher.add_filter('rol', { key : 'rol', value : $(this).val(), type : '=', op : 'and'});
                });
            });
            wrapper.find('.header_action').click(function(){
                _Calendar._view.request_event(_Calendar._vars.user, function(response){
                    _Calendar._view.fill_month_events(wp_calendar);
                    if(_Calendar._vars.user_rol == 2){
                        var from = _Calendar._vars.agenda.find('input[name=doctor_day]').val(),
                            to   = parseInt(from) + 86399;

                        if(response.other.start_time < from || response.other.start_time > to){
                            return false;
                        }
                    }
                    _Calendar._view.add_to_calendar(wrapper, response);
                })
            });

            wrapper.find('.event_list').on('click', 'li', function(){
                var event_id = $(this).attr('name'),
                    popup    = new Popup(),
                    li_event = $(this);
                popup.setTitle('Evento');
                popup.setModal(true);
                popup.setMaxSize(500);
                _Server.get_view('services/calendar/view/view_event.php', {e: event_id}, function(view){
                    var content = popup.setContent(view);
                    content.find('.bt-delete').click(function(){
                        if(confirm('¿Estas seguro de anularla?')){
                            _Server.post_data('services/calendar/post/delete_event.php', {e: event_id}, function(){
                                popup.close();
                                li_event.remove();
                                _Calendar._view.fill_month_events(wp_calendar);
                                var event_list = wrapper.find('.event_list');
                                if(event_list.find('li').length == 0){
                                    event_list.append('<li class="empty_list">- No tiene citas -</li>')
                                }
                            }, function(error){
                                alert('error', error);
                            })
                        }
                    });
                })
            });

            wrapper.find('.old_events').click(function(){
                var date = new Date().getTime();
                _Calendar._view.open_events(false, date);
            });

            if(_Calendar._vars.user_rol == 2){
                var parent = wrapper.find('.doctor_agenda_header');
                parent.find('.bt-previous').click(function(){
                    _Calendar._vars.act = 'previous';
                    _Calendar._view.print_events(wrapper);
                })

                parent.find('.bt-next').click(function(){
                    _Calendar._vars.act = 'next';
                    _Calendar._view.print_events(wrapper);
                })
            }
        },

        print_events : function(wrapper){
            _Server.get_tmpl('services/calendar/view/agenda_item.php', function(tmpl){
                var list = wrapper.find('ul.event_list');
                list.addLoader('li');
                _Calendar._get.events(_Calendar._vars.user, function(events){
                    list.removeLoader();
                    if(_Calendar._get.num_events() > 0){
                        $.each(events, function(i, data){
                            _Calendar._view.add_to_agenda(list, data, false);
                        });
                    }else{
                        _Calendar._view.add_no_events(list)
                    }
                    if(_Calendar._vars.user_rol == 2){
                        wrapper.find('.event_day').text(_Calendar._vars.agenda_day);
                    }
                });
            });
        },

        add_no_events : function(list){
            list.append('<li class="empty_list" style="padding: 10px">- No tiene citas -</li>');
        },

        request_event : function(patient, on_save){
            var popup = new Popup();
            popup.setTitle("Pedir cita");
            popup.setModal(true);
            var data = {};
            if(_Calendar._vars.user_rol == 1){
                data.p = _Calendar._vars.user;
            }else{
                data.d = _Calendar._vars.user;
                if(patient != data.d)
                    data.p = patient;
            }
            _Server.get_view('services/calendar/view/create_event.php', data, function(view){
                popup.setContent(view);
                var content = popup.getContent();

                content.find('input.inp_date').datepicker({
                    format    : 'dd/mm/yyyy',
                    weekStart : 1
                }).on('changeDate', function(ev){
                    _Calendar._process.search_date(content);
                });

                content.find('select.ev_search').change(function(){
                    var type = $(this).val();
                    content.find('.search_day .on').removeClass('on');
                    content.find('.search_day > div[name=' + type+']').addClass('on');
                    if(type == '2'){
                        _Calendar._process.search_date(content);
                    }
                });

                content.find('select[name=ev_patient]').change(function(){
                    _Calendar._process.search_date(content);
                })

                content.find('select[name=ev_doctor]').change(function(){
                    _Calendar._process.search_date(content);
                })

                content.find('a.bt-send').click(function(){
                    var button = $(this);
                    _Calendar._process.create_event(content, function(response){
                        alert('success', 'Cita Asignada correctamente');
                        button.removeLoader();
                        popup.close();
                        if(on_save && typeof(on_save) == 'function')
                            on_save(response);

                    }, function(error){
                        button.removeLoader();
                        alert('error', error);
                    });
                })
                content.find('a.bt-cancel').click(function(){
                    popup.close();
                });
            });

        },

        add_to_calendar : function(wrapper, data){
            var list  = wrapper.find('ul.event_list'),
                where = false;

            list.find('.empty_list').remove();

            list.find('li input[name=start_time]').each(function(){
                if(data.other.start_time < $(this).val()){
                    where = $(this).parent();
                    return false;
                }
            });

            _Calendar._view.add_to_agenda(list ,data, where);
        },

        add_to_agenda : function(list, data, where){
            where = where || false;

            if(!list.hasClass('event_list')) {
                list = list.find('.event_list');
            }

            _Server.get_tmpl('services/calendar/view/agenda_item.php', function(tmpl){
                tmpl = $.tmpl(tmpl, {data: data, user_rol : _Calendar._vars.user_rol});
                if(_Calendar._vars.user_rol == 1){
                    var li = list.find('li[name=' + data.id + ']');
                    if(li.length > 0){
                        list.html(tmpl.children());
                    }else{
                        if(where && where.length > 0){
                            where.before(tmpl);
                        }else{
                            list.append(tmpl);
                        }
                    }
                }else{
                     var li = list.find('li[name=' + data.id + ']');
                    if(li.length > 0){
                        list.html(tmpl.children());
                    }else{
                        if(where && where.length > 0){
                            where.before(tmpl);
                        }else{
                            list.append(tmpl);
                        }
                    }

                }
            })
        },

        fill_month_events : function(clndr, month, year){
            var clndr_instance = clndr.clndr();
            month = month || false;
            year  = year  || false;
            if(!month || !year){
                month = clndr_instance.month.month() + 1;
                year  = clndr_instance.month.year();
            }
            _Calendar._get.month_events(month, year, function(events){
                var clndr_events = [];
                $.each(events, function(i, ev){
                    clndr_events.push({
                        date    : ev.start,
                        title   : ev.request,
                        url     : ''
                    })
                });
                if(clndr_events.length > 0){
                    clndr_instance.setEvents(clndr_events);
                }
            })
        },

        change_day : function(wrapper, action){

        },

        open_events : function(from, to){
            var popup = new Popup();
            popup.setTitle('Listado de eventos');
            popup.setModal(true);
            _Server.get_view('services/calendar/view/event_list.php', {u: _Calendar._vars.user, f: from, t: to}, function(view){
               popup.setContent(view);
            });
        }
    },

    _process : {
        create_event : function(wrapper, success, error){
            var search_parent = wrapper.find('.search.on');
            var data = {
                patient : wrapper.find('[name=ev_patient]').val(),
                doctor  : wrapper.find('[name=ev_doctor]').val(),
                date    : search_parent.find('.sel_results').val(),
                request : wrapper.find('.ev_request').val(),
                type    : wrapper.find('.ev_type').val()
            }
            if(data.date == '0'){
                error('Seleccione un dia para la cita');
                return false;
            }
            _Server.post_data('services/calendar/post/save.php', data , success, error);
        },

        search_date : function(wrapper){
            var parent = wrapper.find('.search.on');

            var data = {
                patient : wrapper.find('[name=ev_patient]').val(),
                doctor  : wrapper.find('[name=ev_doctor]').val(),
                date    : parent.find('.inp_date').val(),
                type    : parent.attr('name')
            }

            if(data.date){
                data.date = data.date.split('/');
                data.date = data.date[2] + '-' + data.date[1] + '-' + data.date[0];
            }else{
                var date  = new Date(),
                    month = date.getMonth() + 1;
                month = (month < 10 ? '0' + month : month);

                parent.find('.inp_date').val(date.getDate() + '/' + month + '/' + date.getFullYear());

                data.date = date.getFullYear() + '-' + month + '-' + date.getDate();
            }
            var select = parent.find('.sel_results');
            select.empty();
            select.append('<option value="0">Buscando..</option>');
            _Server.get_data('services/calendar/get/search_date.php', data , function(response){
                select.empty();
                var cont = 0;
                $.each(response, function(i, value){
                    cont++;
                    select.append('<option value="' + i +'">' + value + '</option>');
                });
                switch(data.type){
                    case '1':
                        select.prepend('<option value="0" selected>' + cont + ' horas libres</option>');
                        break;
                    case '2':
                    case '3':
                        select.prepend('<option value="0" selected>Proximas ' + cont + ' citas libres</option>');
                        break;

                }
            }, function(error){
                alert(error);
                select.empty();
                select.append('<option value="0">- Seleccione un dia -</option>');
            })
        }
    }
}
