var _Server = {
    tmpls : {},

    get_view : function(url, data, on_success, on_error){
        $.get(url, data )
            .done(function(html){
                if(on_success && typeof(on_success) == "function")
                    on_success(html);
            })
            .fail(function(){
                if(on_error && typeof(on_error) == "function")
                    on_error();
            })
    },

    get_tmpl : function(url, on_success, on_error){
        if(_Server.tmpls.hasOwnProperty(url)){
            if(on_success && typeof(on_success) == "function")
                on_success(_Server.tmpls[url]);
        }else{
            _Server.get_view(url, false, function(tmpl){
                _Server.tmpls[url] = tmpl;
                if(on_success && typeof(on_success) == "function")
                    on_success(tmpl);

            }, on_error)
        }
    },

    post_data : function(url, data, on_success, on_error){
        $.post( url, data)
            .done(function( response ) {
                try{
                    response = $.evalJSON(response);
                    if(response.success){
                        if(on_success && typeof(on_success) == "function")
                            on_success(response.info);
                    }else{
                        if(on_error && typeof(on_error) == "function")
                            on_error(response.info);
                    }
                }catch(e){
                    console.log(e);
                    if(on_error && typeof(on_error) == "function")
                        on_error("error data");
                }
            })
            .fail(function(){
                if(on_error && typeof(on_error) == "function")
                    on_error("error peticion");
            })
    },

    get_data : function(url, data, on_success, on_error){
        $.get(url, data)
            .done(function(response){
                try{
                    response = $.evalJSON(response);
                    if(response.success){
                        if(on_success && typeof(on_success) == "function")
                            on_success(response.info);
                    }else{
                        if(on_error && typeof(on_error) == "function")
                            on_error(response.info);
                    }
                }catch(e){
                    if(on_error && typeof(on_error) == "function")
                        on_error("error data");
                }
            })
            .fail(function(){
                if(on_error && typeof(on_error) == "function")
                    on_error("error peticion");
            })
    }

}