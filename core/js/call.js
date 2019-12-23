var _Call = {
    _vars : {
        id      : 0,
        name    : '',
        wrapper : false,
        body    : false,
        calling : false,
        videos  : false
    },

    initialize : function(on_load){
        _Server.get_tmpl('views/call.php', function(view){
            $("body").append(view);
            _Call._vars.wrapper = $('.hv-call');
            _Call._vars.body    = _Call._vars.wrapper.find('.call_body');
            _Call._vars.calling = _Call._vars.wrapper.find('.call_calling');
            _Call._vars.video   = _Call._vars.wrapper.find('.call_video');
            _Call._view.load_DOM(_Call._vars.wrapper);
            if(on_load && typeof(on_load) == "function")
                on_load();
        })
    },

    calling : function(call_name, call_id, sip_id){
        _Call._vars.id   = call_id;
        _Call._vars.name = call_name;
        _Call._vars.wrapper.addClass('on');
        _Call._view.clean_classes();
        _Call._vars.body.addClass('calling');
        _Call._view.setCaller(call_name);
        _Sip.sipCall(sip_id);

        setTimeout(function(){
            if(_Call._vars.body.hasClass('calling')){
                _Call.finished(true);
            }
        }, 30000);
    },

    incoming: function(call_name, call_id, on_accept, on_reject){
        _Call._vars.wrapper.addClass('on');
        _Call._view.clean_classes();
        _Call._vars.body.addClass('incoming');
        _Call._view.setCaller(call_name);
        _Call._vars.id   = call_id;
        _Call._vars.name = call_name;

        var bt_enter  = _Call._vars.body.find('.bt-enter'),
            bt_reject = _Call._vars.body.find('.bt-reject');
        bt_enter.bind('click.yes', function(){
            bt_enter.unbind('click.yes');
            bt_reject.unbind('click.no');
            if(on_accept && typeof(on_accept) == 'function')
                on_accept()
        })
        bt_reject.bind('click.no', function(){
            bt_enter.unbind('click.yes');
            bt_reject.unbind('click.no');
            if(on_reject && typeof(on_reject) == 'function')
                on_reject()
        })
    },

    connect : function(){
        _Call._view.clean_classes();
        _Call._vars.body.addClass('connect');
    },

    finished : function(your){
        _Sip.sipHangUp();
        _Call._view.clean_classes();
        _Call._vars.body.addClass('finished');
        if(your){
            _Call._vars.body.find('._finished h5').text('Ha finalizado la llamada');
        }else{
            _Call._vars.body.find('._finished h5').text(_Call._vars.name + ' ha finalizado la llamada');
        }
        _Server.post_data('services/room/post/call_status.php', {c : _Call._vars.id, a: 'end'}, false);
        _Call._vars.id   = 0;
        _Call._vars.name = '';
    },

    close : function(){
        _Call._view.clean_classes();
        _Call.finished(true);
    },

    _view : {
        load_DOM : function(wrapper){
            wrapper.find('.call_header-right i').click(function(){
                switch($(this).data().action){
                    case 'min'      : _Call._view.minimize(wrapper);break;
                    case 'normal'   : _Call._view.normal(wrapper);break;
                    case 'max'      : _Call._view.maximize(wrapper);break;
                    case 'rest'     : _Call._view.restore(wrapper);break;
                    case 'close'    : _Call._view.close(wrapper);break;
                }
            });
        },

        minimize: function(wrapper){
            if(wrapper.hasClass('maximize')){
                wrapper.removeClass('maximize');
                setTimeout(function(){
                    _Call._view.minimize(wrapper);
                },500)
            }else{
                var height        = wrapper.innerHeight(),
                    header_height = wrapper.find('.call_header').innerHeight();

                wrapper.css('bottom', 0);
                wrapper.css('bottom', '-' + (height - header_height) + 'px');
                wrapper.addClass('minimize');
            }

        },

        normal: function(wrapper){

            wrapper.removeClass('minimize');
            wrapper.css('bottom', '0');
        },

        maximize: function(wrapper){
            wrapper.removeClass('minimize');
            wrapper.addClass('maximize');
        },

        restore: function(wrapper){
            wrapper.removeClass('maximize');
        },

        close: function(wrapper){
            if(confirm('¿Estas seguro de cerrarla?')){
                _Call.close();
                wrapper.removeClass('on');
            }
        },

        setCaller : function(text){
            _Call._vars.wrapper.find('.call_name').text(text);
        },

        clean_classes : function(){
            _Call._vars.body
                .removeClass('calling')
                .removeClass('incoming')
                .removeClass('connect')
                .removeClass('rejected')
                .removeClass('finished');

        }
    }
}