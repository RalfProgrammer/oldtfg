<header>
    <h4> Hospital VIHrtual</h4>
</header>

<main class="login">
    <div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
        <div class="col-xs-12">
            <div class="box login_logo">
                <img src="<?= $CONFIG->www . '/resources/images/hospital_logo.png'?>">
            </div>

        </div>
        <div class="col-xs-12">
            <div class="box access_login">
                <?php
                if($login_error){?>
                    <div class="login_error">
                        <?= $login_error ?>
                    </div><?php
                }?>
                <h5>Entra con tu dni y contraseña</h5>
                <form method="post" action="">
                    <input type="text" name="dni" placeholder="DNI">
                    <input type="password" name="password" placeholder="Contraseña">
                    <button type="submit" class="button green" type="submit">Entrar</button>
                </form>
                <nav class="remember_password">
                    ¿Has olvidado tu contraseña?
                </nav>
            </div>
            <div class="box request_password" style="display: none">
                <h5>Si has olvidado tu contraseña introduce tu DNI o email para solicitar una nueva</h5>
                <input type="text" name="rem_password" value="" placeholder="DNI o email">
                <button type="submit" class="button green bt-request" type="submit">Solicitar</button>
                <nav class="back_login">
                    Volver login
                </nav>
            </div>
        </div>
    </div>
</main>

<footer>

</footer>

<style>
    .login .remember_password,
    .login .back_login {
        font-size: 12px;
        width: 100%;
        text-align: right;
        cursor: pointer;
        margin-top: 7px;
    }
</style>