<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0"/>
        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta name="apple-mobile-web-app-status-bar-style" content="black">

<!--        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/splash/splash-icon.png">-->
<!--        <link rel="apple-touch-startup-image" href="images/splash/splash-screen.png" 			media="screen and (max-device-width: 320px)" />-->
<!--        <link rel="apple-touch-startup-image" href="images/splash/splash-screen@2x.png" 		media="(max-device-width: 480px) and (-webkit-min-device-pixel-ratio: 2)" />-->
<!--        <link rel="apple-touch-startup-image" sizes="640x1096" href="images/splash/splash-screen@3x.png" />-->
<!--        <link rel="apple-touch-startup-image" sizes="1024x748" href="images/splash/splash-screen-ipad-landscape" media="screen and (min-device-width : 481px) and (max-device-width : 1024px) and (orientation : landscape)" />-->
<!--        <link rel="apple-touch-startup-image" sizes="768x1004" href="images/splash/splash-screen-ipad-portrait.png" media="screen and (min-device-width : 481px) and (max-device-width : 1024px) and (orientation : portrait)" />-->
<!--        <link rel="apple-touch-startup-image" sizes="1536x2008" href="images/splash/splash-screen-ipad-portrait-retina.png"   media="(device-width: 768px)	and (orientation: portrait)	and (-webkit-device-pixel-ratio: 2)"/>-->
<!--        <link rel="apple-touch-startup-image" sizes="1496x2048" href="images/splash/splash-screen-ipad-landscape-retina.png"   media="(device-width: 768px)	and (orientation: landscape)	and (-webkit-device-pixel-ratio: 2)"/>-->

        <title>Hospital VIHrtual</title>
        <?php
            $css_files = get_css_files();
            foreach($css_files as $css){?>
                <link href="<?= $CONFIG->www . $css ?>" rel="stylesheet" type="text/css"><?php
            }

            $js_files = get_js_files();
            foreach($js_files as $js){?>
                <script type="text/javascript" src="<?= $CONFIG->www . $js ?>"></script><?php
            }
        if(is_logged()){?>
            <script type="text/javascript">
                _User.id     = <?= $USER->id?>;
                _User.name   = '<?= $USER->name . ' ' . $USER->lastname?>';
                _User.avatar = '<?= $USER->getSrcAvatar() ?>';
                _User._sip   = {
                        id   : "sip:<?= trim($USER->sip_id) ?>",
                        name : "<?= $USER->sip_name?>",
                        pass : "<?= $USER->sip_pass?>",
                        url  : 'sip2sip.info'
                };
                var base_url = '<?= $CONFIG->www?>';
            </script>
            <?php
        }?>

        <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" rel="stylesheet">

        <audio id="audio_remote" autoplay="autoplay"></audio>
        <audio id="sip_ringtone" loop src="<?= $CONFIG->www ?>/resources/sounds/ringtone.wav"></audio>
        <audio id="sip_ringbacktone" loop src="<?= $CONFIG->www ?>/resources/sounds/ringbacktone.wav"></audio>
    </head>
    <body>
