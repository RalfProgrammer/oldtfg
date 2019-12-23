var Chat = function(user, wrapper){
    var _vars = {
        id      : user,
        your    : {
            name   : false,
            avatar : false
        },
        other    : {
            name   : false,
            avatar : false
        },
        messages : {},
        wrapper  : wrapper,
        list     : wrapper.find('ul.messages'),
        textarea : wrapper.find('textarea.chat_text')
    }

    this.addMessage = function(message){}

    wrapper.find('.bt-send').click(function(){
        methods.send(function(data){
            methods.print(data, function(li){
                $('html, body').animate({
                    scrollTop: li.offset().top
                }, 250);
            });
        })
    })

    _Server.get_tmpl('services/chat/view/message.php', function(){
        _Server.get_data('services/chat/get/chat.php', {u: user}, function(response){
            _vars.your      = response.your;
            _vars.other     = response.other;
            _vars.messages  = response.messages;

            $.each(_vars.messages, function(i, message){
                methods.print(message);
            })

            var no_read = _vars.list.find('li.no_read');
            if(no_read.length > 0){
                no_read.first().before('<li class="box messages_new" style="text-align: center"> ' + no_read.length + ' mensajes nuevos</li>');
                $('html, body').animate({
                    scrollTop: (_vars.list.find('li.messages_new').offset().top - 90)
                }, 250);
            }else{
                $('html, body').animate({
                    scrollTop: _vars.list.find('li:last').offset().top
                }, 250);
            }
        })
    })

    var methods = {
        print : function(message, on_print){
            _Server.get_tmpl('services/chat/view/message.php', function(tmpl){
                if(message.from == _vars.id){
                    message.name   = _vars.other.name;
                    message.avatar = _vars.other.avatar;
                    message.type   = '';
                }else{
                    message.name   = 'Tu';
                    message.avatar = _vars.your.avatar;
                    message.type  = 'your'
                }
                console.log(message);
                tmpl = $.tmpl(tmpl, message);
                _vars.list.append(tmpl);
                if(on_print && typeof(on_print) == 'function')
                    on_print(tmpl);
            })
        },

        send : function(on_send){
            var data = {
                u : _vars.id,
                t : encodeURIComponent(_vars.textarea.val())
            }
            _vars.textarea.val("");
            _Server.post_data('services/chat/post/message.php', data, function(response){
                if(on_send && typeof(on_send) == 'function')
                    on_send(response);
            })
        }
    }

}