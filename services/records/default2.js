var _Records = {
    _vars : {

    },

    initialize : function(wrapper){
        _Users._get.users(function(all_users){
            var filter = new Filter(all_users);
            filter.addFilter('rol', { key: 'rol', value: '1', type: '=', op : 'or'});
            filter.setOrder('lastname');
            var results = filter.applyFilters();
            _Server.get_tmpl('services/records/view/record_item.php', function(){
                _Records._view.show_results(wrapper, filter);
                _Records._view.load_DOM(wrapper, filter);
            })
        })
    },

    _view : {
        load_DOM : function(wrapper, filter){
            wrapper.find('select.sel_ord').change(function(){
                var value = $(this).val();
                filter.setOrder(value);
                _Records._view.show_results(wrapper, filter);
            })

            wrapper.find('select.sel_sex').change(function(){
                var value = $(this).val();
                if(value){
                    filter.addFilter('sex', { key : 'sex', value: value, type: '=', op : 'and'});
                }else{
                    filter.removeFilter('sex');
                }
                _Records._view.show_results(wrapper, filter);
            });
            wrapper.find('select.sel_old').change(function(){
                var value = $(this).val();
                value = value.split('-');
                if(value){
                    filter.addFilter('min', { key : 'other.years', value: value[0], type: '>=', op : 'and'});
                    filter.addFilter('max', { key : 'other.years', value: value[1], type: '<', op : 'and'});
                }else{
                    filter.removeFilter('min');
                    filter.removeFilter('max');
                }
                _Records._view.show_results(wrapper, filter);
            })

            wrapper.find('input.inp_search').keyup(function(){
                _Records._view.show_results(wrapper, filter);
            })

            wrapper.find('.list_records').on('click', 'div.record_item', function(){
                _Navigator.go('record!' + $(this).attr('name'));
            })
        },

        print_record : function(list, data){
            _Server.get_tmpl('services/records/view/record_item.php', function(tmpl){
                list.find('.empty_list').remove();
                tmpl = $.tmpl(tmpl, data);
                var exist = list.find('div[name=' + data.id + ']');
                if(exist.length > 0){
                    exist.html(tmpl.children());
                }else{
                    list.append(tmpl);
                }
            });
        },

        show_results : function(wrapper, filter){
            var list    = wrapper.find('.list_records'),
                results = filter.applyFilters();

            list.empty();
            if(results.length > 0){
                var text = wrapper.find('input.inp_search').val();
                if(text != ""){
                    var name_filter = new Filter(results);
                    name_filter.addFilter('fullname' , { key : 'other.fullname', value: text, type: 'like', op : 'or'});
                    name_filter.addFilter('historic' , { key : 'historic', value: text, type: 'like', op : 'or'});
                    name_filter.addFilter('dni'      , { key : 'dni', value: text, type: 'like', op : 'or'});

                    results = name_filter.applyFilters();
                }

                if(results.length > 0){
                    $.each(results, function(i, data){
                        _Records._view.print_record(list, data);
                    });
                    return true;
                }
            }
            list.append('<div class="col-xs-12"><div class="box empty_list">- No hay resultados -</div></div>')
        }
    }
}