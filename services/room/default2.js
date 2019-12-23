_Room._Report = {
    _vars : {
        medicines : false
    },

    initialize : function(wrapper){
        _Room._Report._view.load_DOM(wrapper);
        _Record.initialize(wrapper, wrapper.find('input[name=r_patient]').val());
    },

    _get :{

    },

    _set : {

    },

    _view : {
        load_DOM : function(wrapper){
            var event_id    = wrapper.find('input[name=r_event]').val(),
                int_save    = false,
                text_report = wrapper.find('.new_report');
            if(wrapper.find('input[name=r_finished]').val() != '1'){

                _Medicine._get.medicines(function(medicines){
                    var searcher = new Searcher(wrapper.find('input[name=inp_medicines]'), medicines);

                    var filters = {
                        id   : { key : 'id', value : false, type : 'like', op: 'or'},
                        name : { key : 'name', value : false, type : 'like', op: 'or'}
                    }
                    searcher.init(filters);
                    searcher.set_template('li_medicine');

                    searcher.callback(function(data){
                        _Room._Report._view.add_medicine(wrapper.find('ul.new_medicines'), data);
                    })
                })

                var room_time = wrapper.find('.room_time:first');

                wrapper.find('.bt-call').click(function(){
                    _Server.post_data('services/room/post/save_status.php', {id : event_id, t: 'call'}, function(response){
                        _Server.post_data('services/room/post/call.php', {e : event_id, a: 'call'}, function(call_data){
                            console.log(call_data);
                            room_time.text('Paciente llamado a las ' + response);
                            _Call.calling(call_data.call_name ,call_data.call_id, call_data.sip_id);
                            _Call._view.setTitle('Llamando..');
                        });
                    });
                })

                wrapper.find('.bt-start').click(function(){
                    wrapper.find('.bt-start').remove();
                    _Server.post_data('services/room/post/save_status.php', {id : event_id, t: 'start'}, function(response){
                        room_time.text('Cita iniciada a las ' + response);
                        wrapper.find('textarea.new_report').prop('disabled', false);
                        wrapper.find('.bt-finish').removeClass('hide');
                    });
                })

                wrapper.find('.bt-absence').click(function(){
                    if(confirm('¿El paciente no se ha presentado?')){
                        clearInterval(int_save);
                        _Server.post_data('services/room/post/save_status.php', {id : event_id, t: 'absence'}, function(response){
                            room_time.text('Cita marcada como incomparecencia a las ' + response);
                            wrapper.find('.room_actions').empty();
                        });
                    }
                })

                wrapper.find('.bt-finish').click(function(){
                    if(confirm('¿Estas seguro de finalizar la consulta?')){
                        _Server.post_data('services/room/post/save_status.php', {id : event_id, t: 'end'}, function(response){
                            clearInterval(int_save);
                            room_time.text('Cita finalizada a las ' + response);
                            wrapper.find('.room_actions').empty();
                            var report = encodeURIComponent(text_report.val());
                            _Server.post_data('services/room/post/save_status.php', {id : event_id, t: 'report', r: report}, function(){
                            });
                        });
                    }
                });

                var last_value = "";

                int_save = setInterval(function(){
                    var new_val = text_report.val();
                    if(new_val != last_value){
                        last_value = new_val;
                        _Server.post_data('services/room/post/save_status.php', {id : event_id, t: 'report', r: new_val}, function(){
                        });
                    }
                },5000);

            }
        },

        add_medicine : function(list, data){
            list.find('li.empty_list').remove();
            _Server.get_tmpl('services/medicine/view/edit_item.php', function(tmpl){
                tmpl = $.tmpl(tmpl, data);
                list.append(tmpl);
            })
        }
    }
}