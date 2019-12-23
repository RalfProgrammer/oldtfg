var _Index = {
    initialize : function(wrapper){
        console.log('init');
        _Index._view.load_DOM(wrapper);
    },

    _view : {
        load_DOM : function(wrapper){
            var _wrappers = {
                login    : wrapper.find('.access_login'),
                remember : wrapper.find('.request_password')
            }
            wrapper.find('nav.remember_password').click(function(){
                _wrappers.login.hide();
                _wrappers.remember.show();
            });
            wrapper.find('nav.back_login').click(function(){
                _wrappers.remember.hide();
                _wrappers.login.show();
            });

            wrapper.find('.bt-request').click(function(){
                console.log('click');
                var text = wrapper.find('input[name=rem_password]').val();
                if(text == ""){
                    alert('error', 'Rellena el campo de dni o email');
                    return false;
                }
                _Index._process.request_password(text);
            })
        }
    },

    _process : {
        request_password : function(text){
            _Server.post_data('services/users/post/request_password.php', {t : text}, function(info){
                alert('success', info);
            });
        }
    }
}

$(document).ready(function(){
    _Index.initialize($('body'));
})