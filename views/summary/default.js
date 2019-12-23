var _Summary = {
    initialize : function(wrapper){
        var type = wrapper.find('input[name=summary_type]').val();
        if(_Summary._view.hasOwnProperty('_' + type)){
            _Summary._view['_' + type].load_DOM(wrapper);
        }
        _Summary._view._common.load_DOM(wrapper);
        _Navigator.reloadOwl();
    },

    _view : {
        _admin : {
            load_DOM: function(wrapper){

            }
        },

        _auxiliar : {
            load_DOM: function(wrapper){

            }
        },

        _doctor : {
            load_DOM: function(wrapper){
                var _dom = {
                    inp_search    : wrapper.find('input.search-patients'),
                    list_patients : wrapper.find('ul.search-results'),
                }

                _dom.list_patients.on('click', 'li', function(){
                    _Navigator.go('record!' + $(this).attr('name'));
                })

                var filter;
                _Patients._get.patients(function(all_patients){
                    filter = new Filter(all_patients);
                    filter.addFilter('fullname', { key: 'other.fullname', value: false, type: 'like', op : 'or'});
                    filter.addFilter('historic', { key: 'historic', value: false, type: 'like', op : 'or'});
                    filter.addFilter('dni', { key: 'dni', value: false, type: 'like', op : 'or'});
                    filter.setOrder('lastname');
                });
                _dom.inp_search.keyup(function(){
                    var text = $(this).val();
                    filter.fillFilterValue('fullname', text);
                    filter.fillFilterValue('historic', text);
                    filter.fillFilterValue('dni'     , text);
                    var results = filter.applyFilters();

                    _dom.list_patients.empty();
                    if(results.length > 0){
                        $.each(results, function(i, user){
                            _dom.list_patients.append(
                                '<li class="patient_item" name="' + user.id + '">' +
                                    '<div class="row">'+
                                        '<div class="col-xs-12 col-sm-6">' +
                                            '<img src="' + user.other.avatar_src + '" class="avatar">' +
                                            user.lastname + ', ' + user.name +
                                        '</div>'+
                                        '<div class="hidden-xs col-sm-3">' +
                                            user.historic +
                                        '</div>' +
                                        '<div class="hidden-xs col-sm-3">' +
                                            user.dni +
                                        '</div>' +
                                    '</div>' +
                                '</li>'
                            );
                        })
                    }else{
                        _dom.list_patients.html('<li class="empty_list">- Ningun resultado -</li>');
                    }
                })
            }
        },

        _patient : {
            load_DOM: function(wrapper){

            }
        },

        _common : {
            load_DOM : function(wrapper){
                var _dom = {
                    list_messages : wrapper.find('ul.list_messages'),
                    list_events   : wrapper.find('ul.list_events')
                }

                _dom.list_messages.on('click', 'li', function(){
                    var user = $(this).attr('name');
                    _Navigator.go('chat!' + user);
                })

                _dom.list_events.on('click', 'li', function(){
                    _Navigator.go('calendar');
                })

            }
        }

    }
}