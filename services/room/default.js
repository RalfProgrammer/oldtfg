var _Room = {
    _vars : {
        event_id : false
    },

    initialize : function(wrapper){
        var event_id = wrapper.find('input[name=event_id]');
        _Room._vars.event_id = event_id.val();
        event_id.remove();

        wrapper.find('.event_note').css('height', wrapper.find('.event_details').innerHeight() + 'px');
        _Room._view.load_DOM(wrapper);
    },

    _get : {

    },

    _view : {
        load_DOM : function(wrapper){
            var timesave;
            wrapper.find('textarea.room_note').keyup(function(){
                if(timesave)
                    clearInterval(timesave);

                timesave = setInterval(function(){
                    clearInterval(timesave);
                    _Room._process.save_note(wrapper);
                }, 5000);
            })
            wrapper.find('input[name=room_note_visible]').change(function(){
                if(wrapper.find('textarea.room_note').val() != ""){
                    _Room._process.save_note(wrapper);
                }
            });

            //MEDICO
            wrapper.find('.patients_list').on('click', 'li', function(){
                _Navigator.go('room!' + $(this).attr('name'));
            });
        }
    },

    _process : {
        save_note : function(wrapper){
            var note_id = wrapper.find('input[name=room_note_id]'),
                data = {
                id      : note_id.val(),
                text    : encodeURIComponent(wrapper.find('textarea.room_note').val()),
                event   : _Room._vars.event_id,
                visible : (wrapper.find('input[name=room_note_visible]').is(':checked') ? 1 : 0)
            }

            _Server.post_data('services/note/post/save.php' , data, function(response){
                note_id.val(response.id);
            });
        }
    }
}