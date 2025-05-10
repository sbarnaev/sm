<?php defined('BILLINGMASTER') or die;?>

<style>
    .hero-wrap {
        min-height: 300px;
        height: <?=$this->tr_settings['heroheigh'];?>px;
        background-position: <?=$this->tr_settings['position']?>;
        background-size: cover;
    }
    .hero-wrap:before {
        opacity: <?=$this->tr_settings['overlay']?>;
        background: <?=$this->tr_settings['overlaycolor']?>;
    }
    .hero_header.h1 {
        color: <?=$this->tr_settings['color']?>;
        font-size: <?=$this->tr_settings['fontsize']?>px;
    }

    @media screen and (max-width: 640px), only screen and (max-device-width:640px) {
        .hero-wrap {
            height: <?=$this->tr_settings['heromobileheigh'];?>px;
            min-height:<?=$this->tr_settings['heromobileheigh'];?>px;
        }
        .hero_header.h1 {
            font-size: <?=$this->tr_settings['fontsize_mobile'];?>px
        }
    }
</style>