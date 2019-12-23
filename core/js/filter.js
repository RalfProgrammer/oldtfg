var Filter = function(collection){
    var _vars = {
        data    : collection,
        filters : {},
        order   : false,
        values  : {}
    }
    var methods = false;

    this.addFilter       = function(name, filter){ methods.addFilter(name, filter); }
    this.removeFilter    = function(name){ methods.removeFilter(name); }
    this.applyFilters    = function(){ return methods.applyFilters(); }
    this.fillFilterValue = function(name, value){ return methods.fillFilterValue(name, value); }
    this.getById         = function(id){return methods.getById(id); }
    this.getFilters      = function(){ return _vars.filters; }
    this.getCollection   = function(){ return _vars.data; }
    this.setOrder        = function(order){ methods.setOrder(order); }

    methods = {
        addFilter : function(name, filter){
            if(typeof(filter.key) == 'string'){
                filter.key = (filter.key).split('.');
            }
            _vars.filters[name] = filter;
        },

        setOrder : function(order){
            _vars.order =  order;
        },

        removeFilter : function(name){
            delete _vars.filters[name];
        },

        fillFilterValue : function(filter_name, filter_value){
            _vars.values[filter_name] = filter_value;
        },

        applyFilters : function(){
            var collection = [],
                result     = false,
                n_filters  = 0;

            $.each(_vars.data, function(i, item){
                result    = false;
                n_filters = 0;
                $.each(_vars.filters, function(key, filter){
                    var item_val   = item,
                        filter_val = filter.value || _vars.values[key],//valor del filtro pasado o valor dinamico
                        test       = (filter_val) ? false : true;

                    if(filter_val){
                        $.each(filter.key, function(i, key){
                            item_val = item_val[key];
                        })

                        switch (filter.type){
                            case 'like':
                                var reg_exp = new RegExp(filter_val, "i");
                                test = reg_exp.test(item_val);
                                break;
                            case '=':
                                test = item_val == filter_val;
                                break;
                            case '!=':
                                test = item_val != filter_val;
                                break;
                            case '!in':
                                test = filter_val.indexOf(item_val) == -1;
                                break;
                            case 'in':
                                test = filter_val.indexOf(item_val) > -1;
                                break;
                            case '<':
                                test = item_val < filter_val;
                                break;
                            case '<=':
                                test = item_val <= filter_val;
                                break;
                            case '>':
                                test = item_val > filter_val;
                                break;
                            case '>=':
                                test = item_val >= filter_val;
                                break;
                        }
                    }

                    switch(filter.op){
                        case 'or' : result = result || test;break;
                        case 'and': result = result && test;break;
                        default: result = result || test;
                    }

                    n_filters++;
                });

                if(result || n_filters == 0)
                    collection.push(item);

            });

            if(collection.length > 0 && _vars.order){
                collection = collection.sort(methods.order);
            }
            return collection;
        },

        getById : function(id){
            var search = false;
            $.each(_vars.data, function(i, item){
                if(item.id == id){
                    search = item;
                    return false;
                }
            })
            return search;
        },

        order : function(a, b){
            var attr = _vars.order;
            if (a[attr] < b[attr])
                return -1;
            if (a[attr] > b[attr])
                return 1;
            return 0;
        }
    }
}