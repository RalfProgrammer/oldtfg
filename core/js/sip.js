_Sip = {
    popupCall       : false,
    sTransferNumber : false,
    oRingTone       : false,
    oRingbackTone   : false,
    oSipStack       : false,
    oSipSessionRegister : false,
    oSipSessionCall     : false,
    oSipSessionTransferCall : false,
    videoRemote     : false,
    videoLocal      : false,
    audioRemote     : false,
    bFullScreen     : false,
    oNotifICall     : false,
    oReadyStateTimer : false,
    bDisableVideo   : false,
    viewVideoLocal  : false,
    viewVideoRemote : false,
    oConfigCall     : false,
    ringtone    : false,
    ringbacktone    : false,

    initialize : function(){
        if(window.console) {
            window.console.info("location=" + window.location);
        }
        _Call.initialize(function(){
            _Sip.popupCall      = _Call;

            _Sip.videoLocal     = document.getElementById("sip_localVideo");
            _Sip.videoRemote    = document.getElementById("sip_remoteVideo");
            _Sip.audioRemote    = document.getElementById("sip_remoteAudio");

            _Sip.ringtone       = document.getElementById("sip_ringtone");
            _Sip.ringbacktone   = document.getElementById("sip_ringbacktone");

            // set debug level
            SIPml.setDebugLevel("info");//error or info
            SIPml.init(function(){
                if(_Sip.test_browser()){
                    // attachs video displays
                    if (SIPml.isWebRtc4AllSupported()) {
                        _Sip.viewVideoLocal  = document.getElementById("sip_divLocalVideo");
                        _Sip.viewVideoRemote = document.getElementById("sip_divRemoteVideo");
                        WebRtc4all_SetDisplays(viewVideoLocal, viewVideoRemote); // FIXME: move to SIPml.* API
                    }
                    else{
                        _Sip.viewVideoLocal  = _Sip.videoLocal;
                        _Sip.viewVideoRemote = _Sip.videoRemote;
                    }

                    if (!SIPml.isWebRtc4AllSupported() && !SIPml.isWebRtcSupported()) {
                        if (confirm('Your browser don\'t support WebRTC.\naudio/video calls will be disabled.\nDo you want to download a WebRTC-capable browser?')) {
                            window.location = 'https://www.google.com/intl/en/chrome/browser/';
                        }
                    }

                    _Sip.oConfigCall = {
                        audio_remote    : _Sip.audioRemote,
                        video_local     : _Sip.viewVideoLocal,
                        video_remote    : _Sip.viewVideoRemote,
                        bandwidth       : { audio:undefined, video:undefined },
                        video_size      : { minWidth:undefined, minHeight:undefined, maxWidth:undefined, maxHeight:undefined },
                        events_listener : { events: '*', listener: _Sip.onSipEventSession },
                        sip_caps        : [
                            { name: '+g.oma.sip-im' },
                            { name: '+sip.ice' },
                            { name: 'language', value: '\"en,fr\"' }
                        ]
                    };

                    _Sip.sipRegister();

                }
            });
        });
    },

    sipRegister : function(){
        try {
            // enable notifications if not already done
            if (window.webkitNotifications && window.webkitNotifications.checkPermission() != 0) {
                window.webkitNotifications.requestPermission();
            }
            _Sip.oSipStack = new SIPml.Stack({
                    realm               : _User._sip.url,
                    impi                : _User._sip.name,
                    impu                : _User._sip.id,
                    password            : _User._sip.pass,
                    display_name        : _User.name,
                    websocket_proxy_url : null,
                    outbound_proxy_url  : null,
                    ice_servers         : null,
                    enable_rtcweb_breaker   : false,
                    events_listener     : { events: '*', listener: _Sip.onSipEventStack },
                    enable_early_ims    : true,
                    enable_media_stream_cache   : false,
                    bandwidth           : null,
                    video_size          : null,
                    sip_headers: [
                        { name: 'User-Agent', value: 'IM-client/OMA1.0 sipML5-v1.2014.03.10' },
                        { name: 'Organization', value: 'Doubango Telecom' }
                    ]
                }
            );
            if (_Sip.oSipStack.start() != 0) {
                console.log("REGISTER: Failed to start the SIP stack");
            }else {
                return;
            }
        }catch (e) {
            console.log("REGISTER catch: " +  e);
        }
    },

    onSipEventSession : function(e){
        switch (e.type) {
            case 'connecting':
            case 'connected':
                var bConnected = (e.type == 'connected');
                if(bConnected){
                    alert('success', 'Conectado correctamente');
                }

                if (e.session == _Sip.oSipSessionCall) {
                    if (bConnected) {
                        _Sip.stopRingbackTone();
                        _Sip.stopRingTone();

                        if (_Sip.oNotifICall) {
                            _Sip.oNotifICall.cancel();
                            _Sip.oNotifICall = null;
                        }

                        _Sip.popupCall.connect();
                    }

                    console.log("SESSION_EVENT conected2: " +  e.description);

                    if (SIPml.isWebRtc4AllSupported()) { // IE don't provide stream callback
                        _Sip.uiVideoDisplayEvent(true, true);
                        _Sip.uiVideoDisplayEvent(false, true);
                    }
                }
                break;

            case 'terminating':
            case 'terminated':
                if (e.session == _Sip.oSipSessionRegister) {
                    _Sip.oSipSessionCall     = null;
                    _Sip.oSipSessionRegister = null;

                    _Sip.sipRegister();
                }
                else if (e.session == _Sip.oSipSessionCall) {
                    _Sip.uiCallTerminated(e.description);
                }
                break;
//
//            case 'm_stream_video_local_added':
//                if (e.session == oSipSessionCall) {
//                    uiVideoDisplayEvent(true, true);
//                }
//                break;
//
//            case 'm_stream_video_local_removed':
//                if (e.session == oSipSessionCall) {
//                    uiVideoDisplayEvent(true, false);
//                }
//                break;
//
//            case 'm_stream_video_remote_added':
//                if (e.session == oSipSessionCall) {
//                    uiVideoDisplayEvent(false, true);
//                }
//                break;
//
//            case 'm_stream_video_remote_removed':
//                if (e.session == oSipSessionCall) {
//                    uiVideoDisplayEvent(false, false);
//                }
//                break;
//
//            case 'm_stream_audio_local_added':
//            case 'm_stream_audio_local_removed':
//            case 'm_stream_audio_remote_added':
//            case 'm_stream_audio_remote_removed':
//                break;
//
//            case 'i_ect_new_call':
//                oSipSessionTransferCall = e.session;
//                break;
//
//            case 'i_ao_request':
//                if(e.session == oSipSessionCall){
//                    var iSipResponseCode = e.getSipResponseCode();
//                    if (iSipResponseCode == 180 || iSipResponseCode == 183) {
//                        startRingbackTone();
//                        txtCallStatus.innerHTML = '<i>Remote ringing...</i>';
//                    }
//                }
//                break;
//
//            case 'm_early_media':
//                if(e.session == oSipSessionCall){
//                    stopRingbackTone();
//                    stopRingTone();
//                    txtCallStatus.innerHTML = '<i>Early media started</i>';
//                }
//                break;
//
//            case 'm_local_hold_ok':
//                if(e.session == oSipSessionCall){
//                    if (oSipSessionCall.bTransfering) {
//                        oSipSessionCall.bTransfering = false;
//                        // this.AVSession.TransferCall(this.transferUri);
//                    }
//                    btnHoldResume.value = 'Resume';
//                    btnHoldResume.disabled = false;
//                    txtCallStatus.innerHTML = '<i>Call placed on hold</i>';
//                    oSipSessionCall.bHeld = true;
//                }
//                break;
//
//            case 'm_local_hold_nok':
//                if(e.session == oSipSessionCall){
//                    oSipSessionCall.bTransfering = false;
//                    btnHoldResume.value = 'Hold';
//                    btnHoldResume.disabled = false;
//                    txtCallStatus.innerHTML = '<i>Failed to place remote party on hold</i>';
//                }
//                break;
//
//            case 'm_local_resume_ok':
//                if(e.session == oSipSessionCall){
//                    oSipSessionCall.bTransfering = false;
//                    btnHoldResume.value = 'Hold';
//                    btnHoldResume.disabled = false;
//                    txtCallStatus.innerHTML = '<i>Call taken off hold</i>';
//                    oSipSessionCall.bHeld = false;
//
//                    if (SIPml.isWebRtc4AllSupported()) { // IE don't provide stream callback yet
//                        uiVideoDisplayEvent(true, true);
//                        uiVideoDisplayEvent(false, true);
//                    }
//                }
//                break;
//
//            case 'm_local_resume_nok':
//                if(e.session == oSipSessionCall){
//                    oSipSessionCall.bTransfering = false;
//                    btnHoldResume.disabled = false;
//                    txtCallStatus.innerHTML = '<i>Failed to unhold call</i>';
//                }
//                break;
//
//            case 'm_remote_hold':
//                if(e.session == oSipSessionCall){
//                    txtCallStatus.innerHTML = '<i>Placed on hold by remote party</i>';
//                }
//                break;
//
//            case 'm_remote_resume':
//                if(e.session == oSipSessionCall){
//                    txtCallStatus.innerHTML = '<i>Taken off hold by remote party</i>';
//                }
//                break;
//
//            case 'o_ect_trying':
//                if(e.session == oSipSessionCall){
//                    txtCallStatus.innerHTML = '<i>Call transfer in progress...</i>';
//                }
//                break;
//
//            case 'o_ect_accepted':
//                if(e.session == oSipSessionCall){
//                    txtCallStatus.innerHTML = '<i>Call transfer accepted</i>';
//                }
//                break;
//
//            case 'o_ect_completed':
//            case 'i_ect_completed':
//                if(e.session == oSipSessionCall){
//                    txtCallStatus.innerHTML = '<i>Call transfer completed</i>';
//                    btnTransfer.disabled = false;
//                    if (oSipSessionTransferCall) {
//                        oSipSessionCall = oSipSessionTransferCall;
//                    }
//                    oSipSessionTransferCall = null;
//                }
//                break;
//
//            case 'o_ect_failed':
//            case 'i_ect_failed':
//                if(e.session == oSipSessionCall){
//                    txtCallStatus.innerHTML = '<i>Call transfer failed</i>';
//                    btnTransfer.disabled = false;
//                }
//                break;
//
//            case 'o_ect_notify':
//            case 'i_ect_notify':
//                if(e.session == oSipSessionCall){
//                    txtCallStatus.innerHTML = "<i>Call Transfer: <b>" + e.getSipResponseCode() + " " + e.description + "</b></i>";
//                    if (e.getSipResponseCode() >= 300) {
//                        if (oSipSessionCall.bHeld) {
//                            oSipSessionCall.resume();
//                        }
//                        btnTransfer.disabled = false;
//                    }
//                }
//                break;
//
//            case 'i_ect_requested':
//                if(e.session == oSipSessionCall){
//                    var s_message = "Do you accept call transfer to [" + e.getTransferDestinationFriendlyName() + "]?";//FIXME
//                    if (confirm(s_message)) {
//                        txtCallStatus.innerHTML = "<i>Call transfer in progress...</i>";
//                        oSipSessionCall.acceptTransfer();
//                        break;
//                    }
//                    oSipSessionCall.rejectTransfer();
//                }
//                break;
        }

    },

    onSipEventStack : function(e){
        switch (e.type) {
            case 'started':
                // catch exception for IE (DOM not ready)
                try {
                    // LogIn (REGISTER) as soon as the stack finish starting
                    _Sip.oSipSessionRegister = this.newSession('register', {
                        expires: 200,
                        events_listener: { events: '*', listener: _Sip.onSipEventSession },
                        sip_caps: [
                            { name: '+g.oma.sip-im', value: null },
                            { name: '+audio', value: null },
                            { name: 'language', value: '\"en,fr\"' }
                        ]
                    });
                    _Sip.oSipSessionRegister.register();
                }
                catch (e) {
                    console.log("STACK_EVENT started catch: " +  e);
                }
                break;

            case 'stopping':
            case 'stopped':
            case 'failed_to_start':
            case 'failed_to_stop':
                var bFailure = (e.type == 'failed_to_start') || (e.type == 'failed_to_stop');

                _Sip.oSipStack           = null;
                _Sip.oSipSessionRegister = null;
                _Sip.oSipSessionCall     = null;

                _Sip.stopRingbackTone();
                _Sip.stopRingTone();

                console.log("STACK_EVENT stop : " + e.description);
                break;

            case 'i_new_call':
                if (_Sip.oSipSessionCall) {
                    // do not accept the incoming call if we're already 'in call'
                    e.newSession.hangup(); // comment this line for multi-line support
                }
                else {
                    _Sip.oSipSessionCall = e.newSession;

                    _Server.get_data('services/room/get/actual_call.php', false, function(data){
                        // start listening for events
                        _Sip.oSipSessionCall.setConfiguration(_Sip.oConfigCall);

                        _Sip.startRingTone();

                        var sRemoteNumber = (_Sip.oSipSessionCall.getRemoteFriendlyName() || 'unknown');

                        _Sip.showNotifICall(data.caller);
                        _Sip.popupCall.incoming(data.caller, data.id, function(){
                            _Sip.oSipSessionCall.accept(_Sip.oConfigCall);
                            _Navigator.go('room!' + data.event);
                            _Sip.popupCall.connect();

                            _Server.post_data('services/room/post/call_status.php', {c : data.id, a: 'start'}, false, false);
                        }, function(){
                            e.newSession.hangup();
                            _Sip.popupCall.finished(true);
                        })
                    })
                }
                break;

            case 'm_permission_requested':
            {
//                divGlassPanel.style.visibility = 'visible';
                break;
            }
            case 'm_permission_accepted':
            case 'm_permission_refused':
            {
                if(e.type == 'm_permission_refused'){
                    alert('false', 'No se puede iniciar la llamada al rechazar los permisos');
                }
                break;
            }
//
//            case 'starting': default: break;
        }

    },

    sipCall : function(sip_name, event){
        var s_type = "call-audiovideo";
        _Sip.oSipSessionCall = _Sip.oSipStack.newSession(s_type, _Sip.oConfigCall);
        _Sip.startRingbackTone();

        _Sip.oConfigCall.display_name = event;
        if (_Sip.oSipSessionCall.call(sip_name) != 0) {
            _Sip.oSipSessionCall = null;
            alert('error', 'Error al inicial la conexion');
            return false;
        }
    },

    sipHangUp : function(){
        if (_Sip.oSipSessionCall) {
            _Sip.oSipSessionCall.hangup({events_listener: { events: '*', listener: _Sip.onSipEventSession }});
        }
    },

    uiVideoDisplayEvent : function(b_local, b_added) {
//        var o_elt_video = b_local ? _Sip.videoLocal : _Sip.videoRemote;
//
//        if (b_added) {
//            if (SIPml.isWebRtc4AllSupported()) {
//                if (b_local){
//                    if(window.__o_display_local) window.__o_display_local.style.visibility = "visible";
//                }else {
//                    if(window.__o_display_remote) window.__o_display_remote.style.visibility = "visible";
//                }
//            }
//            else {
//                o_elt_video.style.opacity = 1;
//            }
//        }
//        else {
//            if (SIPml.isWebRtc4AllSupported()) {
//                if (b_local){
//                    if(window.__o_display_local)
//                        window.__o_display_local.style.visibility = "hidden";
//                }else {
//                    if(window.__o_display_remote)
//                        window.__o_display_remote.style.visibility = "hidden";
//                }
//            }
//            else{
//                o_elt_video.style.opacity = 0;
//            }
//        }
    },

    uiCallTerminated : function(s_description){
        _Sip.oSipSessionCall = null;

        _Sip.stopRingbackTone();
        _Sip.stopRingTone();

        if (_Sip.oNotifICall) {
            _Sip.oNotifICall.cancel();
            _Sip.oNotifICall = null;
        }

        _Sip.popupCall.finished(false);

        _Sip.uiVideoDisplayEvent(true, false);
        _Sip.uiVideoDisplayEvent(false, false);

    },

    startRingTone : function() {
        try { _Sip.ringtone.play(); }
        catch (e) { }
    },

    stopRingTone : function() {
        try { _Sip.ringtone.pause(); }
        catch (e) { }
    },

    startRingbackTone : function() {
        try { _Sip.ringbacktone.play(); }
        catch (e) { alert('error', e)}
    },

    stopRingbackTone : function() {
        try { _Sip.ringbacktone.pause(); }
        catch (e) { }
    },

    showNotifICall : function(s_number){
        // permission already asked when we registered
        if (window.webkitNotifications && window.webkitNotifications.checkPermission() == 0) {
            if (_Sip.oNotifICall) {
                _Sip.oNotifICall.cancel();
            }
            _Sip.oNotifICall = window.webkitNotifications.createNotification('images/sipml-34x39.png', 'Llamada entrante', 'Llamada de ' + s_number);
            _Sip.oNotifICall.onclose = function () {
                _Sip.oNotifICall = null;
            };
            _Sip.oNotifICall.show();
        }
    },

    test_browser : function(){
        // check webrtc4all version
        if (SIPml.isWebRtc4AllSupported() && SIPml.isWebRtc4AllPluginOutdated()) {
            if (confirm("Your WebRtc4all extension is outdated ("+SIPml.getWebRtc4AllVersion()+"). A new version with critical bug fix is available. Do you want to install it?\nIMPORTANT: You must restart your browser after the installation.")) {
                window.location = 'http://code.google.com/p/webrtc4all/downloads/list';
                return false;
            }
        }

        // check for WebRTC support
        if (!SIPml.isWebRtcSupported()) {
            // is it chrome?
            if (SIPml.getNavigatorFriendlyName() == 'chrome') {
                if (confirm("You're using an old Chrome version or WebRTC is not enabled.\nDo you want to see how to enable WebRTC?")) {
                    window.location = 'http://www.webrtc.org/running-the-demos';
                }
                return false;
            }

            // for now the plugins (WebRTC4all only works on Windows)
            if (SIPml.getSystemFriendlyName() == 'windows') {
                // Internet explorer
                if (SIPml.getNavigatorFriendlyName() == 'ie') {
                    // Check for IE version
                    if (parseFloat(SIPml.getNavigatorVersion()) < 9.0) {
                        if (confirm("You are using an old IE version. You need at least version 9. Would you like to update IE?")) {
                            window.location = 'http://windows.microsoft.com/en-us/internet-explorer/products/ie/home';
                        }
                        return false;
                    }

                    // check for WebRTC4all extension
                    if (!SIPml.isWebRtc4AllSupported()) {
                        if (confirm("webrtc4all extension is not installed. Do you want to install it?\nIMPORTANT: You must restart your browser after the installation.")) {
                            window.location = 'http://code.google.com/p/webrtc4all/downloads/list';
                        }
                        return galse;
                    }
                    // break page loading ('window.location' won't stop JS execution)
                    if (!SIPml.isWebRtc4AllSupported()) {
                        return false;
                    }
                }
                else if (SIPml.getNavigatorFriendlyName() == "safari" || SIPml.getNavigatorFriendlyName() == "firefox" || SIPml.getNavigatorFriendlyName() == "opera") {
                    if (confirm("Your browser don't support WebRTC.\nDo you want to install WebRTC4all extension to enjoy audio/video calls?\nIMPORTANT: You must restart your browser after the installation.")) {
                        window.location = 'http://code.google.com/p/webrtc4all/downloads/list';
                    }
                    return false;
                }
            }
            // OSX, Unix, Android, iOS...
            else {
                if (confirm('WebRTC not supported on your browser.\nDo you want to download a WebRTC-capable browser?')) {
                    window.location = 'https://www.google.com/intl/en/chrome/browser/';
                }
                return false;
            }
        }

        // checks for WebSocket support
        if (!SIPml.isWebSocketSupported() && !SIPml.isWebRtc4AllSupported()) {
            if (confirm('Your browser don\'t support WebSockets.\nDo you want to download a WebSocket-capable browser?')) {
                window.location = 'https://www.google.com/intl/en/chrome/browser/';
            }
            return false;
        }
        return true;
    }
}