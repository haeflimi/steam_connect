<?php defined('C5_EXECUTE') or die('Access Denied.');
$c = new Concrete\Package\SteamAuthentication\Authentication\Steam\Controller;?>

<div class="form-group">
    <a href="<?php echo \URL::to('/ccm/system/authentication/oauth2/steam/attempt_attach'); ?>" class="btn btn-steam strip_button">
        <i class="fa fa-steam"></i>
        <?php echo t('Attach a %s account', t('steam')) ?>
    </a>
    <span class="help-block">
        <?=t('Connected Steam Account ID').': '.$c->getUniqueId()?>
    </span>
</div>

<style>
    .btn-steam {
        color: #fff !important;
        background: #171a21 !important;
    }
    .btn-steam:hover {
        background: #31343B !important;
    }
    .btn-facebook .fa-steam {
        margin: 0 6px 0 3px;
    }
</style>
