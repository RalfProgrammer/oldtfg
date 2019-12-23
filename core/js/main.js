/**
 * Created by Denis on 22/03/14.
 */
$(document).ready(function(){
    console.info = function(){};

    _Navigator.initialize($('body'));
    _Sip.initialize();

    /*
    * denispfg -> denispfg@sip2sip.info
    * denispfg2 -> denispfg2@sip2sip.info
    */

//    var sipStack;
//    var registerSession;
////
//    var eventsListener = function(e){
//        console.log('Evento:');
//        console.log(e);
//        $('main').append('<span>' + e.type + '</span><br>');
//        if(e.type == 'started'){
//            // LOGIN
//            registerSession = sipStack.newSession('register', {
//                events_listener: { events: '*', listener: eventsListener }, // optional: '*' means all events
//                sip_caps: [
//                    { name: '+g.oma.sip-im', value: null },
//                    { name: '+audio', value: null },
//                    { name: 'language', value: '\"en,fr\"' }
//                ]
//            });
//            registerSession.register();
//        }
//        else if(e.type == 'i_new_message'){
//            e.newSession.accept(); // e.newSession.reject(); to reject the message
////            alert(e.getContentString());
//            console.log(e.getContentString());
////            console.info('SMS-content = ' + e.getContentString() + ' and SMS-content-type = ' + e.getContentType());
//        }
//        else if(e.type == 'i_new_call'){
//            e.newSession.accept();
//        }else if(e.type == 'connected' && e.session == registerSession){
//            console.log("registrado");
//        }else if(e.type == 'terminated'){
//            console.log("TERMINA");
//            SIPml.init(readyCallback, errorCallback);
//            createSipStack();
//        }
//    }
//
//    var readyCallback = function(e){
//        createSipStack(); // see next section
//    };
//    var errorCallback = function(e){
//        console.error('Failed to initialize the engine: ' + e.message);
//    }
//    SIPml.init(readyCallback, errorCallback);
//
//    function createSipStack(){
//        sipStack = new SIPml.Stack({
//                realm           : _User._sip.url, // mandatory: domain name
//                impi            : _User._sip.name, // mandatory: authorization name (IMS Private Identity)
//                impu            : _User._sip.id, // mandatory: valid SIP Uri (IMS Public Identity)
//                password        : _User._sip.pass, // optional
//                events_listener : { events: '*', listener: eventsListener }, // optional: '*' means all events
//                sip_headers: [
//                    { name: 'User-Agent', value: 'IM-client/OMA1.0 sipML5-v1.2014.03.10' },
//                    { name: 'Organization', value: 'Doubango Telecom' }
//                ]
//            }
//        );
//    }
//    sipStack.start();
//
//    var sip_select = $('select.sip_id');
//
//    var messageSession = sipStack.newSession('message', {
//        events_listener: { events: '*', listener: eventsListener } // optional: '*' means all events
//    });
//
//    $("a.button.message").click(function(){
//        messageSession.send(sip_select.val(), 'Holaaaaaaaa', 'text/plain;charset=utf-8');
//    });
//
//    var callSession = sipStack.newSession('call-audiovideo', {
//            video_local     : document.getElementById('sip_localVideo'),
//            video_remote    : document.getElementById('sip_remoteVideo'),
//            audio_remote    : document.getElementById('sip_remoteAudio'),
//            events_listener : { events: '*', listener: eventsListener } // optional: '*' means all events
//    });
//
//    $("a.button.call").click(function(){
//        callSession.call(sip_select.val());
//    });
});