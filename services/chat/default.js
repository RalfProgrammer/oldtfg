var _Chats = {
    _vars : {
        chats  : false,
        filter : false
    },

    initialize : function(wrapper){
        _Users._get.users(function(all_users){
            _Chats._vars.filter = new Filter(all_users);
            _Chats._vars.filter.addFilter('fullname', { key: 'other.fullname', value: false, type: 'like', op : 'or'});

            _Chats._get.chats(function(chats){
                var list = wrapper.find('.all_conversations');
                if(_Chats._get.num_chats() > 0){
                    _Server.get_tmpl('services/chat/view/conversation.php', function(tmpl){
                        $.each(chats, function(i, chat){
                            _Chats._view.add_conversation(list, chat);
                        });
                        _Navigator.reloadOwl();
                    });
                }else{
                    list.append('<li><div class="box" style="text-align: center">- No tienes conversaciones -</div></li>');
                }
            })

            _Chats._view.load_DOM(wrapper);

        });
    },

    _get : {
        chats : function(callback){
//            if(_Chats._vars.chats != false){
//                if(callback && typeof(callback) == 'function')
//                    callback(_Chats._vars.chats);
//            }else{
                _Server.get_data('services/chat/get/chats.php', false , function(response){
                    _Chats._vars.chats = response;
                    if(callback && typeof(callback) == 'function')
                        callback(_Chats._vars.chats);
                })
//            }
        },

        num_chats : function(){
            var cont = 0;
            $.each(_Chats._vars.chats, function(i,j){cont++;});
            return cont;
        }
    },

    _set : {

    },

    _view : {
        load_DOM : function(wrapper){
            var _dom = {
                bt_search    : wrapper.find('.bt-search'),
                inp_search   : wrapper.find('.inp-search'),
                list_all     : wrapper.find('.chat_list_all'),
                list_actives : wrapper.find('.chat_list_actives')
            }
            _dom.bt_search.click(function(){
                _Chats._process.search(_dom);
            });
            _dom.inp_search.keyup(function(){
                _Chats._process.search(_dom);
            });

            wrapper.find('.all_conversations').on('click', 'li.chat_list_conversation', function(){
                if($(this).attr('name'))
                    _Navigator.go('chat!' + $(this).attr('name'), false);
            });

            wrapper.find('.chat_list_all').on('click', 'li', function(){
                if($(this).attr('name'))
                    _Navigator.go('chat!' + $(this).attr('name'), false);
            });
        },

        add_conversation : function(list, data){
            _Server.get_tmpl('services/chat/view/conversation.php', function(tmpl){
                data.other.last_msg = data.other.last_msg.replace(/<br \/>/gi, ' ');
                tmpl = $.tmpl(tmpl, data);
                var li = list.find('li[name=' + data.user_B + ']');
                if(li.length > 0){
                    li.html(tmpl.children());
                }else{
                    list.append(tmpl);
                }
            })
        },

        tmpls : {
            chat_list_item :
                '<li name="${id}">' +
                    '<img class="avatar" src="${other.avatar_src}">${other.fullname}' +
                 '</li>'
        }
    },

    _process : {
        search : function(_dom){
            _Chats._vars.filter.fillFilterValue('fullname', _dom.inp_search.val());
            _dom.list_all.find('ul').empty();
            var filtered = _Chats._vars.filter.applyFilters();
            filtered.sort(function(a,b){return a.other.fullname > b.other.fullname});
            $.each(filtered, function(i, user){
                _dom.list_all.find('ul[name=' + user.rol + ']').append($.tmpl(_Chats._view.tmpls.chat_list_item, user));
            });
            _dom.list_all.find('ul').each(function(i, list){
                if($(list).find('li').length == 0)
                    $(list).append('<li style="text-align: center">- No hay resultados -</li>');
            })
        }

    }
}