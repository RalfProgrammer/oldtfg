var Searcher = function(input, collection){
    var _self   = this,
        methods = false;

    var _vars = {
        input    : input,
        wrapper  : false,
        no_bind  : [],
        filter   : new Filter(collection),
        callback : false,
        tmpl     : false
    }

    this.show            = function(filter){ methods.show(filter)}
    this.hide            = function(){ methods.hide()}
    this.edit_collection = function(){ methods.edit_collection()}
    this.set_template    = function(name){ _vars.tmpl = name; };
    this.get_template    = function(name){ return tmpls[name]};
    this.init            = function(filters){ return methods.init(filters)};
    this.no_bind         = function(elements){ methods.no_bind(elements)}
    this.callback        = function(callback){ _vars.callback = callback}
    this.is_active       = function(){ return _vars.wrapper.hasClass('on')}
    this.add_filter      = function(name, filter){ methods.add_filter(name, filter)}
    this.remove_filter   = function(name){ methods.remove_filter(name)}

    _vars.no_bind.push(_vars.input);

    methods = {
        init : function(filters){
            var wp_seacher = $(tmpls.wrapper);

            $('body').append(wp_seacher);

            _vars.wrapper = wp_seacher;
            $.each(filters, function(name, filter){
                methods.add_filter(name, filter);
            })

            _vars.wrapper.on('click', 'li', function(){
                methods.selected($(this));
            })

            var ul_searcher = _vars.wrapper.find('ul'),
                keys        = [13, 38, 40];

            _vars.input.bind('keyup.move_searcher',function(e){
                var code = (e.keyCode ? e.keyCode : e.which);
                if(_vars.input.val() != ""){
                    if(keys.indexOf(code) >= 0){
                        var actual_li    = ul_searcher.find('li.selected'),
                            actual_index = actual_li.index(),
                            ul_length    = ul_searcher.find('li').length;

                        switch (code){
                            case 38 : //arriba
                                actual_li.removeClass('selected');
                                if(actual_li.length > 0){
                                    if(actual_index == 0){
                                        ul_searcher.find('li:last').addClass('selected');
                                    }else{
                                        ul_searcher.find('li:eq(' + (actual_index - 1) + ')').addClass('selected');
                                    }
                                }else{
                                    ul_searcher.find('li:last').addClass('selected');
                                }
                                break;
                            case 40 : //abajo
                                actual_li.removeClass('selected');
                                if(actual_li.length > 0){
                                    if(actual_index == (ul_length - 1)){
                                        ul_searcher.find('li:first').addClass('selected');
                                    }else{
                                        ul_searcher.find('li:eq(' + (actual_index + 1) + ')').addClass('selected');
                                    }
                                }else{
                                    ul_searcher.find('li:first').addClass('selected');
                                }
                                break;
                            case 13 :
                                if(actual_index >= 0)
                                    methods.selected(actual_li);
                                break;
                        }
                    }else{
                        methods.show();
                    }
                }else{
                    methods.hide();
                }
            })
        },

        print : function(){
            _vars.wrapper.css('top', (_vars.input.offset().top + _vars.input.outerHeight()) + 'px');
            _vars.wrapper.css('left', _vars.input.offset().left + 'px');
            _vars.wrapper.css('width', _vars.input.outerWidth() + 'px');
        },

        show : function(){
            methods.print();
            _vars.wrapper.addClass('on');

            //Previene que se cierre
            $.each(_vars.no_bind, function(i , element){
                element.addClass('sh_nc');
            })

            //Rellena los filtros que estaban a false con el valor del input
            var all_filters = _vars.filter.getFilters(),
                input_val   = _vars.input.val();
            $.each(all_filters, function(key, filter){
                if(!filter.value)
                    _vars.filter.fillFilterValue(key, input_val);
            })

            //Hace la busqueda
            var filtered    = _vars.filter.applyFilters(_vars.input.val()),
                ul_searcher = _vars.wrapper.find('ul').empty();

            var tmpl = (_vars.tmpl) ? tmpls[_vars.tmpl] : tmpls.li;
            $.each(filtered , function(i, item){
                ul_searcher.append($.tmpl(tmpl, item));
            });

            if(ul_searcher.find('li').length == 0){
                ul_searcher.append('<li class="sh_no_clk" style="text-align: center">- No hay coincidencias -</li>')
            }

            $('body').bind('click.out_searcher', function(event){
                if($(event.target).parents('.sh_nc').length == 0 && !$(event.target).hasClass('sh_nc')){
                    methods.hide();
                }
            });
        },

        selected : function(li){
            if(!li.hasClass('sh_no_clk')){
                var item = _vars.filter.getById(li.attr('name'));
                if(item){
                    _vars.input.val("");
                    if(_vars.callback && typeof(_vars.callback) == 'function')
                        _vars.callback(item);
                    methods.hide();
                }
            }
        },

        hide : function(){
            _vars.wrapper.removeClass('on');
            $('body').unbind('click.out_searcher');
            $(window).unbind('keyup.move_searcher');

            $.each(_vars.no_bind, function(i , element){
                element.removeClass('sh_nc');
            })
        },

        no_bind : function(element){
            _vars.no_bind.push(element);
        },

        add_filter : function(name, filter){
            _vars.filter.addFilter(name, filter);
        },

        remove_filter : function(name){
            _vars.filter.removeFilter(name);
        }
    }

    var tmpls = {
        wrapper :
            '<div class="searcher sh_nc" style="">' +
                '<ul></ul>' +
            '</div>',

        li : '<li name="${id}">${other.fullname}</li>',

        li_medicine : '<li name="${id}">${name}</li>',

        search_agenda :
            '<li name="${id}">' +
                '${other.fullname}' +
                '{{if rol == "1"}}' +
                    ' (NH: ${historic} )' +
                '{{else}}'+
                    ' (ID: ${staff_id} )' +
                '{{/if}}'+
            '</li>'
    }

    $(window).resize(function(){
        methods.print();
    });
}