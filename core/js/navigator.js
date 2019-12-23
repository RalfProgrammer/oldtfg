var _Navigator = {
    _vars : {
        wrapper : false
    },

    initialize : function(wrapper){

        history.pushState('main', 'Pagina principal', "?v=main");
        _Navigator._vars.wrapper = wrapper.find('main');


        wrapper.find('header nav.header i:first').click(function(){
            _Navigator.move_menu('click');
        });

        $('nav.top').on('click', 'div', function(){
            var owl = $('.owl-carousel');
            if(owl.length > 0){
                var num_tab = $(this).index();
                var move    = owl.data('owlCarousel').currentItem;
                move -= num_tab;
                var up = move < 0;
                move = (move < 0) ? move *-1 : move;
                for(var i = 0; i < move; i++){
                    if(up){
                        owl.trigger('owl.next');
                    }else{
                        owl.trigger('owl.prev');
                    }
                }
            }
        });

        _Navigator.print_tabs();

//        $('main').bind('movestart', function(e){
//            $('main').prepend('<label>Empieza</label>');
//            console.log('empieza');
//            e.preventDefault();
//        })

//        var swipe_start = 0;
//        wrapper.find('main').swipe({
//            swipeStatus : function(event, phase, direction){
//                if(phase == "start")
//                    swipe_start = event.touches[0].clientX;
//
//                if(phase == "end"){
//                    if(direction == "right" && swipe_start < 50){
//                        _Navigator.move_menu('right');
//                    }else if(direction == "left" && swipe_start > 250){
//                        _Navigator.move_menu('left');
//                    }
//                }
//            }
//        });

        wrapper.find('nav.main').find('ul li a').bind('click', function(event){
            var name = $(this).attr('name');
            if(name != 'logout')
                event.preventDefault();
            _Navigator.go(name, false);
        })
        wrapper.find('nav.left').find('ul li a').bind('click', function(event){
            var name = $(this).attr('name');
            if(name != 'logout')
                event.preventDefault();
            _Navigator.go(name, false);
            _Navigator.move_menu('click');
        })
    },

    go : function(view, from_historic){
        var params  = view.split("!")
        if(_Navigator._go_to.hasOwnProperty(params[0])){
            if(!from_historic){
                history.pushState(view, "Tool " + view, "?v=" + view);
            }
            _Navigator._go_to[params[0]](params[1], function(){
                _Navigator.print_tabs();
            });
        }
    },

    _go_to : {

        calendar : function(user, callback){
            user = user || false;
            _Server.get_view("services/calendar/index.php" , {u: user}, function(html){
                _Navigator._vars.wrapper.html(html);
                _Calendar.initialize(user, _Navigator._vars.wrapper);
                if(callback && typeof(callback) == "function")
                    callback();
            });
        },

        chats : function (params, callback){
            _Server.get_view("services/chat/index.php" , {}, function(html){
                _Navigator._vars.wrapper.html(html);
                _Chats.initialize(_Navigator._vars.wrapper);
                if(callback && typeof(callback) == "function")
                    callback();
            });
        },

        chat : function (user, callback){
            _Server.get_view("services/chat//view/chat.php" , {u: user}, function(html){
                _Navigator._vars.wrapper.html(html);
                var Conversation = new Chat(user, _Navigator._vars.wrapper);
                if(callback && typeof(callback) == "function")
                    callback();
            });
        },

        staff : function (params, callback){
            _Server.get_view("services/staff/index.php" , {}, function(html){
                _Navigator._vars.wrapper.html(html);
                _Staff.initialize(_Navigator._vars.wrapper);
                if(callback && typeof(callback) == "function")
                    callback();
            });
        },

        patients : function (params, callback){
            _Server.get_view("services/patients/index.php" , {}, function(html){
                _Navigator._vars.wrapper.html(html);
                _Patients.initialize(_Navigator._vars.wrapper);
                if(callback && typeof(callback) == "function")
                    callback();
            });
        },

        main : function(params, callback){
            _Server.get_view("views/summary/index.php" , {}, function(html){
                _Navigator._vars.wrapper.html(html);
                _Summary.initialize(_Navigator._vars.wrapper);
                if(callback && typeof(callback) == "function")
                    callback();
            });
        },

        room : function(event, callback){
            if(event){
                _Server.get_view("services/room/view/doctor_room.php" , {ev: event}, function(html){
                    _Navigator._vars.wrapper.html(html);
                    _Room._Report.initialize(_Navigator._vars.wrapper);
                    if(callback && typeof(callback) == "function")
                        callback();
                });
            }else{
                _Server.get_view("services/room/index.php" , {}, function(html){
                    _Navigator._vars.wrapper.html(html);
                    _Room.initialize(_Navigator._vars.wrapper);
                    if(callback && typeof(callback) == "function")
                        callback();
                });
            }
        },

        record : function(user, callback){
            if(user){
                _Server.get_view("services/records/view/record.php" , {u: user}, function(html){
                    _Navigator._vars.wrapper.html(html);
                    _Record.initialize(_Navigator._vars.wrapper, user);
                    if(callback && typeof(callback) == "function")
                        callback();
                });
            }else{
                _Server.get_view("services/records/index.php" , {}, function(html){
                    _Navigator._vars.wrapper.html(html);
                    _Records.initialize(_Navigator._vars.wrapper, user);
                    if(callback && typeof(callback) == "function")
                        callback();
                });
            }
        },

        users : function(params, callback){
            _Server.get_view("services/users/index.php", {}, function(html){
                _Navigator._vars.wrapper.html(html);
                _Users.initialize(_Navigator._vars.wrapper);
                if(callback && typeof(callback) == "function")
                    callback();

            });
        },

        roles : function(params, callback){
            _Server.get_view("services/permissions/index.php", {}, function(html){
                _Navigator._vars.wrapper.html(html);
                _Permissions.initialize(_Navigator._vars.wrapper);
                if(callback && typeof(callback) == "function")
                    callback();

            });
        },

        profile : function(params, callback){
            _Server.get_view("services/users/view/edit_profile.php", {}, function(html){
                _Navigator._vars.wrapper.html(html);
                _User.initialize(_Navigator._vars.wrapper);
                if(callback && typeof(callback) == "function")
                    callback();

            });
        }
    },

    move_menu : function(action){
        var is_on = false;
        switch (action){
            case 'left' : is_on = true;break;
            case 'right': is_on = false;break;
            default :
                is_on = $("header").hasClass('move_right');
        }
        $("body > :not(nav.left)").toggleClass('move_right', !is_on);
    },

    print_tabs : function(){
        $('nav.top').html();
        var tabs     = "",
            num_tabs = $("main .tabs > .tab").length;

        $("main .tabs > .tab").each(function(i, item){
            tabs += '<div class="div_' + num_tabs + '">' + $(item).attr('name') + '</div>';
        });

        $('nav.top').html(tabs);
        $('nav.top div:first').addClass('on');

        if(num_tabs > 1){
            $('main .tabs').owlCarousel({
                singleItem : true,
                pagination : false,
                afterMove  : function(){
                    var owl = $(".owl-carousel").data('owlCarousel');
                    $('nav.top div.on').removeClass('on');
                    $('nav.top div:nth-child(' + (parseInt(owl.currentItem) + 1) + ')').addClass('on');
                }
            });
        }
    },
    reloadOwl : function(){
        var owl = $('main .tabs').data('owlCarousel');
        if(owl)
            owl.reload();
    }
}
window.addEventListener('popstate', function(event) {
    _Navigator.go(event.state, true);
})