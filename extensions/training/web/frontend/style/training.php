<?php defined('BILLINGMASTER') or die;

if($training['full_cover_param']):
    $full_cover_param = json_decode($training['full_cover_param'], true);
endif;?>

<style>
    .hero-wrap {
        height: <?=$full_cover_param['heroheigh'];?>px;
        background-position: <?=$full_cover_param['position']?>;
        background-size: cover;
    }
    .hero-wrap:before {
        opacity: <?=$full_cover_param['overlay']?>;
        background: <?=$full_cover_param['overlaycolor']?>;
    }
    .lesson_cover {
        width: <?=$this->tr_settings['width_less_img'];?>px
    }
    <?php if(isset($this->tr_settings['show_blocks']) && $this->tr_settings['show_blocks'] == 0):?>
        .module-number {
            display:none
        }
    <?php endif;?>

    @media screen and (max-width: 640px), only screen and (max-device-width:640px) {
        .hero-wrap {
            height: <?=$full_cover_param['heromobileheigh'];?>px;
            min-height:<?=$full_cover_param['heromobileheigh'];?>px;
        }
    }
</style>
