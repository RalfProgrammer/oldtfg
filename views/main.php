<header>
    <nav class="header">
        <div class="header_avatar hidden-xs hidden-sm">
            <img class="avatar big" src="<?= $USER->getSrcAvatar()?>">
        </div>
        <a class="hidden-md hidden-lg">
            <i class="fa fa-bars"></i>
        </a>
        <a href=".">
            <i class="fa fa-hospital-o"></i>
        </a>
    </nav>
    <h4 class="hidden-xs hidden-sm" style="position: absolute;left: 75px;top: 0;text-transform: capitalize;font-size: 14px;padding: 17px 0;">Bienvenido <?= $USER->getFullName()?></h4>
    <h4> Hospital VIHrtual</h4>
</header>

<nav class="top">
    <div class="div_2 on">Noticias</div>
    <div class="div_2">Notificaciones</div>
</nav>

<nav class="left">
    <?php require($CONFIG->dir . 'views/menu.php');?>
</nav>

<nav class="main hidden-xs hidden-sm">
    <?php
    $main_menu = true;
    require($CONFIG->dir . 'views/menu.php');?>
</nav>
<main>
    <?php
    if(!$go_to)
        require_once($CONFIG->dir . 'views/summary/index.php');?>
</main>
<?php
if(!$go_to){?>
    <footer>
        <script>
            $(function(){
                _Summary.initialize($('main'));
            })
        </script>
    </footer><?php
}?>