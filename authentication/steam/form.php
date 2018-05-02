<?php
if (isset($error)) {
    ?>
    <div class="alert alert-danger"><?php echo $error ?></div>
<?php

}
if (isset($message)) {
    ?>
    <div class="alert alert-success"><?php echo $message ?></div>
<?php

}

$user = new User();

if ($user->isLoggedIn()) {
    ?>
    <div class="form-group">
        <span>
            <?php echo t('Attach a %s account', t('steam')) ?>
        </span>
        <hr>
    </div>
    <div class="form-group">
        <a href="<?php echo \URL::to('/ccm/system/authentication/oauth2/steam/attempt_attach');
    ?>" class="btn btn-steam btn-block">
            <i class="fa fa-steam"></i>
            <?php echo t('Attach a %s account', t('steam')) ?>
        </a>
    </div>
<?php

} else {
    ?>
    <div class="form-group">
        <span>
            <?php echo t('Sign in with %s', t('steam')) ?>
        </span>
        <hr>
    </div>
    <div class="form-group">
        <a href="<?php echo \URL::to('/ccm/system/authentication/oauth2/steam/attempt_auth');
    ?>" class="btn btn-block">
            <img src="https://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_01.png" alt="<?=t('Steam Login')?>">
        </a>
    </div>
<?php

}
?>
<style>
    .btn-steam {
        color: #fff !important;
        background: #171a21 !important;
    }
    .btn-steam:hover {
        background: #31343B !important;
    }
    .btn-steam .fa-steam {
        margin: 0 6px 0 3px;
    }
</style>
