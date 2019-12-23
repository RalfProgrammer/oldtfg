var _User = {
    initialize : function(wrapper){
        _User._view.load_DOM(wrapper);
    },
    _view : {
        load_DOM : function(wrapper){
            wrapper.find('.bt-save_email').click(function(){
                var data = {
                    em_1   : encodeURIComponent(wrapper.find('input[name=email_1]').val()),
                    em_2   : encodeURIComponent(wrapper.find('input[name=email_2]').val())
                }
                if(data.em_1 == ""){
                    alert('error', 'rellena el campo de email');
                    return false;
                }
                if(data.em_2 == ""){
                    alert('error', 'Rellena el campo de repetir email');
                    return false;
                }
                if(data.em_1 != data.em_2){
                    alert('error', 'Los emails no coinciden');
                    return false;
                }
                _Server.post_data('services/users/post/save_profile.php', {a : 'email', data : $.toJSON(data)} , function(info){
                    alert('success', info);
                    wrapper.find('.email_wrapper').find('input[type=text]').val("");
                    wrapper.find('input[name=email_act]').val(decodeURIComponent(data.em_1));
                }, function(error){
                    alert('error', error);
                })
            })

            wrapper.find('.bt-save_password').click(function(){
                var data = {
                    pass_1   : encodeURIComponent(wrapper.find('input[name=password_1]').val()),
                    pass_2   : encodeURIComponent(wrapper.find('input[name=password_2]').val()),
                    pass_old : encodeURIComponent(wrapper.find('input[name=password_old]').val())
                }
                if(data.pass_1 == ""){
                    alert('error', 'rellena el campo de nueva contraseña');
                    return false;
                }
                if(data.pass_2 == ""){
                    alert('error', 'Rellena el campo de repetir contraseña');
                    return false;
                }
                if(data.pass_old == ""){
                    alert('error', 'Introduce tu contraseña actual');
                    return false;
                }
                if(data.pass_1 != data.pass_2){
                    alert('error', 'Las contraseñas no coinciden');
                    return false;
                }
                _Server.post_data('services/users/post/save_profile.php', {a : 'password', data : $.toJSON(data)} , function(info){
                    alert('success', info);
                    wrapper.find('.password_wrapper').find('input[type=password]').val("");

                }, function(error){
                    alert('error', error);
                })
            })
        }
    }
}