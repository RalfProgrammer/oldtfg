<?php
if(!isset($CONFIG))
    require_once('../config.php');
?>
<div class="hv-call">
    <input type="hidden" name="call_id" value="0">
    <div class="call_header">
        <nav class="call_header-left">
            <i class="fa fa-video-camera"></i>
        </nav>
        <span class="call_name">XXXXX</span>
        <nav class="call_header-right">
            <i class="fa fa-minus" data-action="min"></i>
            <i class="fa fa-plus" data-action="normal"></i>
            <i class="fa fa-expand" data-action="max"></i>
            <i class="fa fa-compress" data-action="rest"></i>
            <i class="fa fa-times" data-action="close"></i>
        </nav>
    </div>
    <div class="call_body">
        <div class="_calling">
            <h5>Llamando...</h5>
            <i class="fa fa-user"></i>
            <img src="<?= $CONFIG->www?>/resources/images/call2.gif">
        </div>
        <div class="_incoming">
            <h5>¿Entrar en la consulta?</h5>
            <i class="fa fa-user-md"></i>
            <div class="row">
                <div class="col-xs-6">
                    <a class="btn btn-primary bt-enter">Entrar</a>
                </div>
                <div class="col-xs-6">
                    <a class="btn btn-danger bt-reject">Rechazar</a>
                </div>
            </div>
        </div>
        <div class="_connect">
            <audio id="sip_remoteAudio" autoplay="autoplay" />
            <audio id="sip_ringtone" loop src="<?= $CONFIG->www?>/resources/sounds/ringtone.wav" />
            <audio id="sip_ringbacktone" loop src="<?= $CONFIG->www?>/resources/sounds/ringbacktone.wav" />
            <div id="divVideoRemote">
                <video class="video" width="100%" height="100%" id="sip_remoteVideo" autoplay="autoplay">
                </video>
            </div>
            <div id="divVideoLocal">
                <video class="video" width="100%" height="100%" id="sip_localVideo" autoplay="autoplay" muted="true">
                </video>
            </div>
        </div>
        <div class="_finished">
            <h5></h5>
            <i class="fa fa-sign-out"></i>
        </div>
    </div>
</div>

<style>
    .hv-call{
        display: none;
        position: fixed;
        bottom: 0;
        right: 5px;
        width: 80%;
        height: 300px;
        border: 1px solid #d8d8d8;
        background-color: #E5E5E5;
        z-index: 500;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.15);
    }
    .hv-call.on{
        display: block;
    }
    .hv-call .call_header,
    .hv-call .call_body{
        padding: 10px;
    }

    .hv-call .call_header{
        background-color: #EB9532;
        color: #fff;
        position: relative;
        height: 40px;
        font-size: 14px;
        padding-left: 35px;
        padding-right: 55px;
    }

    .hv-call .call_header .call_header-right,
    .hv-call .call_header .call_header-left{
        position: absolute;
        top: 0;
        padding: 10px 0;
        font-size: 20px;
        color: #fff;
        text-align: right;
    }
    .hv-call .call_header i {
        color: #fff;
        margin: 0 3px;
    }

    .hv-call .call_header .call_header-left{
        text-align: left;
        left: 5px;
    }
    .hv-call .call_header .call_header-left i{
        cursor: default;
    }

    .hv-call .call_header .call_header-right{
        right: 5px;
        width: 80px;
        font-size: 20px;
        color: #fff;
        text-align: right;
    }

    .hv-call .call_body{
        height: calc(100% - 40px);
        width: 100%;
        position: relative;
    }
    .hv-call .call_body img.calling{
        margin-top: 50px;
        margin-left: auto;
        margin-right: auto;
    }

    .hv-call .fa-plus{
        display: none;
    }
    .hv-call.minimize .fa-plus{
        display: inline-block;
    }
    .hv-call.minimize .fa-minus{
        display: none;
    }

    .hv-call.maximize{
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
    }

    .hv-call.maximize .fa-expand{
        display: none;
    }
    .hv-call .fa-compress{
        display: none;
    }
    .hv-call.maximize .fa-compress{
        display: inline-block;
    }

    @media (min-width: 768px) {
        .hv-call {
            width: 400px;
        }
    }

    /* CALL STATUS */
    .hv-call .call_body div._calling,
    .hv-call .call_body div._incoming,
    .hv-call .call_body div._connect,
    .hv-call .call_body div._finished{
        display: none;
        position: relative;
        text-align: center;
    }

    .hv-call .call_body.calling div._calling,
    .hv-call .call_body.incoming div._incoming,
    .hv-call .call_body.connect div._connect,
    .hv-call .call_body.finished div._finished{
        display: block;
    }

    .hv-call .call_body div._finished i,
    .hv-call .call_body div._calling i,
    .hv-call .call_body div._incoming i {
        font-size: 150px;
        padding: 5px 0 15px 0;
    }
    .hv-call .call_body div._calling img{
        margin: 0 auto;
    }
    .hv-call .call_body div._connect #divVideoRemote{
        width: 100%;
        height: 100%;
    }
    .hv-call .call_body div._connect #divVideoLocal{
        position: absolute;
        width: 25%;
        height: 25%;
        left: 0;
        bottom: 0;
    }
</style>