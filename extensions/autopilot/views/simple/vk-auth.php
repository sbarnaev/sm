<?php defined('BILLINGMASTER') or die;
$autopilot = Autopilot::getSettings();
if (isset($autopilot['modules']['login']) && $autopilot['modules']['login']==1):?>
    <style>
        .icon-white,.icon-blue {
            width: 30px;
            vertical-align: top;
            margin-right: 7px;
        }
        .vkauth:hover .icon-white, .vkauth .icon-blue{
            display: none;
        }
        .vkauth:hover .icon-blue{
            display: inline-block;
        }
    </style>

    <div class="modal-form-line">
        <a href="/autopilot/vkauth" class="btn-blue d-block button text-center vkauth">
            <img src="/extensions/autopilot/web/images/vk_logo.svg" alt="" class="icon-white">
            <img src="/extensions/autopilot/web/images/vk_logo_blue.svg" alt="" class="icon-blue">
            Войти через ВК
        </a>
    </div>
    <div class="modal-form-line text-center">
        или
    </div>
<?php endif;?>