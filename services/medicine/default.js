var _Medicine = {
    _vars : {
        list : false
    },

    initialize : function(wrapper){

    },

    _get : {
        medicines : function(callback){
            if(_Medicine._vars.list){
                if(callback && typeof(callback) == "function"){
                    callback(_Medicine._vars.list);
                }
            }else{
                _Server.get_data("services/medicine/get/medicines.php", false, function(data){
                    _Medicine._set.medicines(data);
                    if(callback && typeof(callback) == "function"){
                        _Medicine._get.medicines(callback);
                    }
                })
            }
        }
    },

    _set : {
        medicines : function(data){
            _Medicine._vars.list = data;
        }

    },

    _view : {
        load_DOM : function(wrapper){

        }
    }
}