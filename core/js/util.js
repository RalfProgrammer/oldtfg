var debug = function(value, name){
    name = name || false;
    if(name)
        console.log(' # ' +name + ': ');
    console.log(value);
    return true;
}
alert = function(type, message){
    message = message || type;
    var types = ['success', 'error'];
    if(types.indexOf(type) == -1) {
        type = 'error';
    }
    var item = $("<div class='notification " + type + "'>" + message + "</div>");
    $('body').append(item);
    setTimeout(function(){
        item.css('top', '0');
    },100)
    if(type =='success'){
        setTimeout(function(){
            item.css('top', '-50px');
            setTimeout(function(){
                item.remove();
            },1000)
        },5000)
    }else{
        var bg = $('<div class="notification_bg"></div>');
        $('body').append(bg);
        item.append('<i class="fa fa-times bt-close"></i>');
        item.find('.bt-close').click(function(){
            item.css('top', '-50px');
            setTimeout(function(){
                item.remove();
                bg.remove();
            },500)
        })
    }
}

$.fn.addLoader = function(wrapper){
    var container    = false,
        dv_container = false;

    switch (wrapper){
        case 'li':
            container    = $('<li><div class="loader"></div></li>');
            dv_container = container.find('div.loader');
            break;
        default :
            container    = $('<div class="loader"></div>');
            dv_container = container;

    }
    container.append('<div class="loader"><img alt="Cargando" src="' + base_url + '/resources/images/loader.gif"></div>');
    $(this).html(container);
}

$.fn.removeLoader = function(){
    $(this).find('.loader').remove();
}